<?php

namespace App\Http\Controllers\Sample;

use App\Http\Controllers\Controller;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Session;
use Memcached;
use DB;
use Cookie;
use App\Libs\Api\SlackUtil;

class SampleController extends Controller
{
    /**
     * 表示されるとルーティングできている
     */
    public function hello(){
        return response( "Hello SampleController.");
    }

    public function phpinfo() {
        phpinfo();
    }

    /**
     * 自分へのslack通信テスト
     */
    public function postSlack() {
        $response = [];

        $result = SlackUtil::postMessage("WMGECKETX", "socialoutput-share テスト");
        $response['slack_result'] = $result;

        return new JsonResponse($response);
    }

    /**
     * 表示されると環境編集が取得できている
     * @param Request $request
     * @return JsonResponse
     */
    public function env(Request $request) {
        $response = array();
        $response["title"] = "Hello env";
        $response["APP_NAME"] = env("APP_NAME");
        $response["DB_USERNAME"] = env("DB_USERNAME");
        $response["TZ"] = env("TZ");
        return new JsonResponse($response);
    }

    /**
     * 表示されるとCacheと接続できている
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function cache(Request $request) {
        $value = Cache::get('sample', 0);
        $value = $value + 1;
        Cache::put('sample', $value);
        return response("Hello Cache = $value");
    }

    /**
     * 表示されるとMemcachedと接続できている
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function memcached(Request $request) {
        $mc = new Memcached();
        $mc->addServer("memcached", env("MEMCACHED_PORT"));
        $value = $mc->get("sample_memcached");
        $value = $value + 1;
        $mc->set("sample_memcached", $value);
        return response("Hello Memcached = $value");
    }

    /**
     * 表示されるとデータベースと接続できている
     * @return JsonResponse
     */
    public function db(){
        $response = [];
        $simpleNumber = DB::select('select 123+200');
        $response["select 123+200"] = $simpleNumber;
        $databases = DB::select('show databases');
        $response["databases"] = $databases;
        return new JsonResponse($response);
    }

    public function cookie(Request $request){
        $value = $request->cookie('sample', 0);
        $value = $value + 1;
        $cookie = cookie('sample', $value, 5);
        return response("Hello Cookie = $value")->cookie($cookie);
    }
    public function cookieFacade(Request $request){
        $value = Cookie::get('sample', 0);
        $value = $value + 1;
        Cookie::queue('sample', $value, 5);
        return response("Hello Cookie Facade = $value");
    }

    public function sessionId(Request $request){
        $sessionId = session()->getId();
        return response("Hello Session Id = $sessionId");
    }
    public function session(Request $request) {
        $value = Session::get('sample', 0);
        $value = $value + 1;
        Session::put('sample', $value);
        return response("Hello Session = $value");
    }

    public function login(Request $request) {
        $response = [];
        $userId = $request->get('user_id');
        session()->put('user_id', $userId);
        $response["user_id"] = $userId;
        $response["message"] = "ログインしました。/sample/user を開くと user_id が表示されます";
        return new JsonResponse($response);
    }

    public function logout(Request $request){
        $response = [];
        session()->remove('user_id');
        $response["message"] = "ログアウトしました。";
        return new JsonResponse($response);
    }

    public function user(Request $request) {
        $userId = $request->session()->get('user_id');
        return response("session user id = $userId");
    }

}
