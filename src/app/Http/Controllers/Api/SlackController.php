<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Libs\Api\SlackUtil;
use App\Http\Controllers\Api\SlackControllerUtil;
use App\Http\Controllers\Api\GoogleSheetController;

class SlackController extends Controller
{
    const AUTHOR_SLACK_ID = 'WMGECKETX'; // kawasumi
    const TEST_SLACK_CHANNEL_ID = 'CJH8BKRHB'; // #study_slackbot
    const SOCIAL_OUTPUT_SHARE_SLACK_ID = "C921SPS9J";

    /**
     * api接続確認用
     */
    public function index(Request $request) {
        $response = [];

        $response['all'] = $request->all();

        SlackUtil::postLog($request->all(), "👏 Success");

        $response['message'] = 'success';
        return new JsonResponse($response);
    }

    /**
     * SlackEvent発火時にPOSTされるメソッド
     * messageを解析してnote投稿のみスプシに書き込み
     *
     * エラー時も200で返す必要がある
     */
    public function events(Request $request) {
        $response = [];

        $retryNum = $request->header('X-Slack-Retry-Num');
        if ($retryNum != null) {
            // 複数回slackからrequestがくるので、初回（null）のみ対応
            return response('', 200);
        }

        $type = $request->input('type');
        if($type == "url_verification") {
            // 認証
            $response['challenge'] = $request->input('challenge');
            return new JsonResponse($response);
        }

        // 正しいリクエストか判定
        list($isValid, $message)  = SlackControllerUtil::isValid($request);
        if (!$isValid) {
            return response($message, 200);
        }

        $event = $request->input('event');
        $channel = $event['channel'];

        // if ($channel != self::SOCIAL_OUTPUT_SHARE_SLACK_ID) {
        //     return response('#000_socialoutput_share チャンネルのみ対応しています', 200);
        // }

        if (array_key_exists('subtype', $event)) {
            $subtype = $event['subtype'];
            if ($subtype == "message_deleted") {
                return response("削除は未対応", 200);
            }
            if ($subtype == "message_changed") {
                return response("編集は未対応", 200);
            }
        }

        if (!array_key_exists('blocks', $event)) {
            SlackUtil::postLog($request->all(), "⛅️ blocksが見つかりません");
            return response('blocksが見つかりません', 200);
        }

        $blocks = $event['blocks'];

        // noteのURLを特定
        $noteURLs = [];
        foreach ($blocks as $block) {
            $elementArray = $block['elements'];
            // elements が二重で存在する
            foreach ($elementArray as $elementSection) {
                if ($elementSection['type'] != "rich_text_section") {
                    continue;
                }
                $elements = $elementSection['elements'];
                foreach ($elements as $element) {
                    if ($element['type'] != 'link') {
                        continue;
                    }

                    $url = $element['url'];
                    $isNoteURL = SlackControllerUtil::isNoteURL($url);
                    if(!$isNoteURL) {
                        continue;
                    }

                    $noteURLs[] = $url; // noteURLなら追加
                }
            }
        }

        if (empty($noteURLs)) {
            return response('noteのURLが見つかりません', 200);
        }

        // ユーザの特定
        $userId = $event['user'];

        // 時刻の特定
        $timestamp = $request->input('event_time');
        $date = date("Y-m-d H:i", (int) $timestamp);

        // user_nameを取る手段がないので仕方なく別シートから参照
        $userNameFuncText = '=VLOOKUP("' . $userId . '", members!$A$1:$B$256, 2, 0)';

        $records = [$date, $userNameFuncText];
        foreach ($noteURLs as $url) {
            $records[] = $url;
        }

        $values = [$records];
        $result = GoogleSheetController::create($values);
        if (empty($result)) {
            SlackUtil::postLog($result, "🤖 Spreadsheetへの書き込みエラー");
            return response('Spreadsheetへの書き込みに失敗しました', 200);
        }

        $response['result'] = $result;
        $response['message'] = 'Success';
        SlackUtil::postLog($request->all(), "🐊 Success！");

        return new JsonResponse($response);
    }
}
