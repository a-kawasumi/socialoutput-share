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
    /**
     * Spreadsheetへの接続確認用
     */
    public function index(Request $request) {
        $response = [];

        //保存するデータ
        $values = [
            ["列A test", "列B test"]
        ];

        $response['google_response'] = self::create($values);

        $response['message'] = 'success';
        return new JsonResponse($response);
    }

    /**
     * Spreadsheetに書き込むデータの作成
     */
    public static function create($values) {
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

        //データ操作領域を設定
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values,
        ]);

        //追記
        $googleResponse = $sheet->spreadsheets_values->append(
            env("GOOGLE_SPREADSHEETS_ID"), // 対象スプレッドシートのID
            'note_sheet', //シート名
            $body, //データ
            ["valueInputOption" => 'USER_ENTERED']
        );

        //書き込んだ処理結果
        return $googleResponse->getUpdates();
    }
}
