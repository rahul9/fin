<?php
namespace App\Libraries;
use Illuminate\Support\Facades\Response as Response;
Class Utils {    
    public static function sendSuccessResponse($data,$message = null)
    {
        return Response::json(array('code' => 'success' , 'data' => $data, 'message' => $message));
    }

    public static function sendFailedResponse($data,$message = null)
    {
        return array('code' => 'failed' , 'data' => $data, 'message' => $message);
    }

}
