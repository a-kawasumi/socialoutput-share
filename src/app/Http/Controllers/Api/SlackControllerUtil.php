<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Libs\Api\SlackUtil;

class SlackControllerUtil extends Controller
{
    public static function isValid($request) {
        $message = null;

        if (empty($request)) {
            $message = "引数が正しくありません";
            return [false, $message];
        }

        $type = $request->input('type');
        if ($type != "event_callback") {
            $message = 'typeがevent_callback以外は未対応です。';
            return [false, $message];
        }

        $token = $request->input('token');
        if ($token != env('SLACK_VERIFICATION_TOKEN')) {
            $message = 'tokenの値が不正です';
            return [false, $message];
        }

        $apiAppID = $request->input('api_app_id');
        if ($apiAppID != env('SLACK_API_APP_ID')) {
            $message = '許可されていないSlackアプリケーションです';
            return [false, $message];
        }

        $workspaceID = $request->input('team_id');
        if ($workspaceID != env('SLACK_WORKSPACE_ID')) {
            $message = '許可されていないworkspaceです';
            return [false, $message];
        }

        $message = "有効なrequestです";
        return [true, $message];
    }

    public static function isNoteURL($url) {
        if (empty($url)) {
            return false;
        }

        $pattern = "/https?:\/\/note.com\/.+/";
        $length = preg_match($pattern, $url, $matches);

        if ($length === false) {
            SlackUtil::postLog($url, "📻  noteのURLマッチでエラー発生");
            return false;
        }
        if ((int)$length === 0) {
            return false;
        }

        return true;
    }

}
