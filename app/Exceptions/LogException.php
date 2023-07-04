<?php

namespace App\Exceptions;
use Illuminate\Http\Request;
use App\Libraries\Utils;
use Illuminate\Support\Facades\URL;

class LogException {

    public static function logEexceptionError($exception, $request = null)
    {
        
        $log = 1;
        if($request == null){
            $context = array();
        }else{
        $context = array(
            'context' => array(
                'get_post' => $request->all(),
            )
        );
    }
        $where = '';
        if ($exception instanceof \Throwable) {
            $file = $exception->getFile();
            $path = pathinfo($file);
            $where = ' (' . $exception->getFile() . ':' . $exception->getLine() . ')';
            $errorMsg = $exception->getMessage();
            $errorMsg = $exception->getMessage();
        } else {

            $errorMsg = $exception;
        }
        
        $error = array();
        if (!empty($errorMsg)) {
            $error['log_message'] = $errorMsg;
            $error['tag_name'] = "exception";
            $error['hostname'] = gethostname();
            $error['full_url'] = URL::full();
            $error['file'] = $where;
            $error['line'] = '';
            if ($exception instanceof \Throwable) {
                $error['file'] = $exception->getFile();
                $error['line'] = $exception->getLine();
            }
            $error['context'] = $context;
            $error['from_url'] = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $error['datetime'] = date('Y-m-d H:i:s');
            $error['timestamp'] = date('YmdHis');
            $error['type'] = 'ERROR';
            $error['log_level'] = 'ERROR';
            //$error['uuid'] = Utils::get_event_uuid();
            $error['eid'] = round(microtime(true) * 1000);
            if ((!empty($code) &&  $code == -1) || (empty($code) && $exception instanceof \Throwable &&  $exception->getCode() == -1)) {
                if (!empty($_ENV['LOGGING_LEVEL']) && $_ENV['LOGGING_LEVEL'] != 3) {
                    $log = 0;
                }
                $error['type'] = 'INFO';
                $error['log_level'] = 'INFO';
            } elseif ((!empty($code) &&  $code == -2) || (empty($code) && $exception instanceof \Throwable &&  $exception->getCode() == -2)) {
                if (!empty($_ENV['LOGGING_LEVEL']) && $_ENV['LOGGING_LEVEL'] != 2 && $_ENV['LOGGING_LEVEL'] != 3) {
                    $log = 0;
                }
                $error['type'] = 'DEBUG';
                $error['log_level'] = 'DEBUG';
            }
            if ($log == 1) {
            
                file_put_contents(storage_path() . "/logs/exception.log", json_encode($error) . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            
            $error['trace'] = [];
            if ($exception instanceof \Throwable) {
                $error['trace'] = $exception->getTrace();
            }
            
            return Utils::sendFailedResponse([],!empty($errorMsg) ? $errorMsg : "Something went wrong. Kindly try again");
        }
    }

    public static function NotFoundException(){
        return Utils::sendFailedResponse([],"This url does not exist");
    }
}