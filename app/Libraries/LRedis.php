<?php 
namespace App\Libraries;
use Illuminate\Support\Facades\Redis;

class LRedis extends  Redis{

    public static function redisKey($key){
        return 'consump_'.$key;
    }
}