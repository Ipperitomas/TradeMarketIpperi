<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiResponseController extends Controller
{
    //

    public function sendResponse($code,$result, $message = "")
    {
        
        $response = [
            'code' => $code,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, $code);
    }

    public function ArmarLinks($cantidad,$limit,$page,$request){
        $i = 0;
        $links = array();
        if($cantidad[0]->cantidad > $limit){
            $links[$i]["url"] = null; 
            $links[$i]["label"] = "Previous"; 
            $links[$i]["active"] = false; 
            $cant_pages = ceil($cantidad[0]->cantidad/$page);
            for ($i=1; $i < $cant_pages; $i++) { 
                $links[$i]["url"] = url('/')."/".$request->path()."?page=".$i; 
                $links[$i]["label"] = $i; 
                $links[$i]["active"] = false;     
            }
            $links[$i]["url"] = null; 
            $links[$i]["label"] = "Next"; 
            $links[$i]["active"] = false; 
            
        }else{
            $cant_pages = 1;

            $links[$i]["url"] = null; 
            $links[$i]["label"] = "Previous"; 
            $links[$i]["active"] = false; 
            $i++;
            $links[$i]["url"] = url('/')."/".$request->path()."?page=".$cant_pages;; 
            $links[$i]["label"] = "1"; 
            $links[$i]["active"] = true; 
            $i++;
            $links[$i]["url"] = null; 
            $links[$i]["label"] = "Next"; 
            $links[$i]["active"] = false; 
        }

        return $links;

    }
}
