<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Libs\Api\SlackUtil;
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
        } elseif ($type != "event_callback") {
            return response('typeがevent_callback以外は未対応です。', 200);
        }

        $token = $request->input('token');
        if ($token != env('SLACK_VERIFICATION_TOKEN')) {
            return response('tokenの値が不正です', 200);
        }

        $apiAppID = $request->input('api_app_id');
        if ($apiAppID != env('SLACK_API_APP_ID')) {
            return response('許可されていないSlackアプリケーションです', 200);
        }

        $workspaceID = $request->input('team_id');
        if ($workspaceID != env('SLACK_WORKSPACE_ID')) {
            return response('許可されていないworkspaceです', 200);
        }

        $event = $request->input('event');
        $channel = $event['channel'];

        // if ($channel != self::SOCIAL_OUTPUT_SHARE_SLACK_ID) {
        //     return response('#000_socialoutput_share チャンネルのみ対応しています', 200);
        // }

        // $subtype = $event['subtype'];
        // if ($subtype != 'message_changed') {
        //     return response('subtypeがmessage_changed以外は未対応です', 200);
        // }

        if (!array_key_exists('message', $event)) {
            SlackUtil::postLog($request->all(), "⛅️ messageが見つかりません");
            return response('messageが見つかりません', 200);
        }

        // messageの抽出
        $message = $event['message'];
        $userId = $message['user'];
        $text = $message['text'];

        $pattern = "/<https?:\/\/note.com\/.+>/";
        preg_match_all($pattern, $text, $matches);

        $patternAll = $matches[0];
        if (empty($patternAll)) {
            return response('noteのurlが見つかりませんでした', 200);
        }

        $timestamp = $request->input('event_time');
        $date = date("Y-m-d H:i", (int) $timestamp);
        // user_nameを取る手段がないので仕方なく別シートから参照
        $userNameFuncText = '=VLOOKUP("' . $userId . '", members!$A$1:$B$256, 2, 0)';

        $records = [$date, $userNameFuncText];
        foreach ($patternAll as $key => $match) {
            $match = rtrim($match, '>');
            $match = ltrim($match, '<');
            $records[] = $match;
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
