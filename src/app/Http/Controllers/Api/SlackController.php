<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Libs\Api\SlackUtil;

class SlackController extends Controller
{
    const AUTHOR_SLACK_ID = 'WMGECKETX'; // kawasumi

    public function index(Request $request) {
        $response = [];

        $response['all'] = $request->all();

        $response['message'] = 'success';
        return new JsonResponse($response);
    }

    public function postMessage(Request $request) {
        $response = [];

        $text = "s test";
        $slackResponse = SlackUtil::postMessage(self::AUTHOR_SLACK_ID, $text);

        $response['slack_response'] = $slackResponse;

        return new JsonResponse($response);
    }
}
