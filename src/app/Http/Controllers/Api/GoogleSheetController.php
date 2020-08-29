<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;

class GoogleSheetController extends Controller
{
    public function index(Request $request) {
        $response = [];

        // アカウント認証情報インスタンスを作成
        $client = new Google_Client();

        $credentials = storage_path('json/credentials.json');
        $client->setAuthConfig($credentials);

        //任意名
        $client->setApplicationName("Sheet API TEST");

        //シート情報を操作するインスタンスを生成
        $sheet = new Google_Service_Sheets($client);

        //サービスの権限スコープ
        $scopes = [Google_Service_Sheets::SPREADSHEETS];
        $client->setScopes($scopes);

        //保存するデータ
        $values = [
            ["列A test", "列B test"]
        ];

        //データ操作領域を設定
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values,
        ]);

        //追記
        $googleResponse = $sheet->spreadsheets_values->append(
            env("GOOGLE_SPREADSHEETS_ID"), // 作成したスプレッドシートのIDを入力
            'note_sheet', //シート名
            $body, //データ
            ["valueInputOption" => 'USER_ENTERED']
        );

        //書き込んだ処理結果を確認
        $response['google_response'] = $googleResponse->getUpdates();

        $response['message'] = 'success';
        return new JsonResponse($response);
    }
}
