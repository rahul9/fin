<?php

namespace App\Http\Controllers;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
   
    const HTTP_GET = 'get';
    const HTTP_POST = 'post';
    const HTTP_DELETE = 'delete';
    const REQUEST_TYPES = [
        self::HTTP_GET,
        self::HTTP_POST,
        self::HTTP_DELETE
    ];
    const API_SOURCE_JSON = 'json';



    }