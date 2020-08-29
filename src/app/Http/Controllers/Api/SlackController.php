<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlackController extends Controller
{
    public function index(Request $request) {
        $response = [];

        $response['all'] = $request->all();

        $response['app_env'] = env("APP_ENV");

        $response['message'] = 'success';
        return new JsonResponse($response);
    }
}
