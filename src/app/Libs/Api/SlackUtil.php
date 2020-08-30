<?php

namespace App\Libs\Api;
use \App\Libs\Http\HttpRequestUtil;

class SlackUtil {

    const BASE_URL = "https://slack.com/api";
    const DEFAULT_HEADERS = [
        "Content-Type: application/x-www-form-urlencoded,application/json"
    ];

    /**
     * Access Token
     */
    private static function getAccessToken() {
        $accessToken = env('SLACK_ACCESS_TOKEN', null);
        return $accessToken;
    }

    /**
     * URL を作成
     * example:
     *  $path = "/users.list"
     *  $param = "&limit=1000"
     */
    private static function createUrl($path, $param) {

        $accessToken = self::getAccessToken();
        if (empty($accessToken)) {
            echo "error: app/.env の SLACK_ACCESS_TOKEN に値をセットしてください";
            return null;
        }

        $base = self::BASE_URL . "/${path}";
        $param = "?token=${accessToken}" . $param;

        return $base . $param;
    }

    /**
     * channel, DM 投稿
     * $options は下記を参照
     * Arguments: https://api.slack.com/methods/chat.postMessage
     */
    public static function postMessage($channel, $text, $options = []) {
        $params = [];
        $params["channel"] = $channel;
        $params["text"] = $text;

        foreach ($options as $key => $value) {
            $params[$key] = $value;
        }
        $body = http_build_query($params);

        $path = "chat.postMessage";
        $url = self::createUrl($path, "");

        $response = HttpRequestUtil::postRequest($url, $body, self::DEFAULT_HEADERS);

        return $response;
    }
}
