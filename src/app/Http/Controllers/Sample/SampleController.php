<?php

namespace App\Http\Controllers\Sample;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
