<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiResponseController extends Controller
{
    //

    public function sendResponse($code,$result, $message)
    {
        
        $response = [
            'code' => $code,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, $code);
        var_dump($response);
    }
}
