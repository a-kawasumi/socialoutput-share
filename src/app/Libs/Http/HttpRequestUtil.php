<?php

namespace App\Libs\Http;

/**
 * JSONのみ対応したHttp Request Util
 */
class HttpRequestUtil {

    /**
     * GET Request
     */
    public static function getRequest($url, $headers) {
        return self::request($url, "GET", null, $headers);
    }

    /**
     * POST Request
     */
    public static function postRequest($url, $body, $headers){
        return self::request($url, "POST", $body, $headers);
    }

    /**
     * Http Requestを実行
     */
    private static function request($url, $method, $body, $headers) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($body)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        $responseJsonText = curl_exec($curl);
        $body = json_decode($responseJsonText , true);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $resultObj = [];
        $resultObj['status_code'] = $httpCode;
        $resultObj['body'] = $body;

        return $resultObj;
    }
}
