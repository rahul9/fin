<?php

namespace App\Http\Middleware;

use App\Libraries\Utils;
use App\Models\Sources;
use Closure;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $token = $request->get('token');
        $source = $request->get('source');
        $authorization = $request->header('Authorization');
        $user_id = $request->get('user_id');
       
        if(empty($user_id) || empty($authorization)) {
            return Utils::sendFailedResponse([], 'User id and authorization header is mandatory');
        } else {
            if(!empty($authorization)) {
                if ($authorization != $_ENV['AUTHORISATION_VAL']) {
                    return Utils::sendFailedResponse([], 'Authorisation header doesnt match');
                }
            } 
        }
        // if (empty($token)) {
        //     return Utils::sendFailedResponse([], "Authentication token is mandatory");
        // } else {
        //     if(!empty($token)) {
        //         if(empty($source)) {
        //             return Utils::sendFailedResponse([], "Source is mandatory");
        //         } else {
        //             $handle = $this->handleToken($token,$source);
        //             if ($handle) {
        //                 return $handle;
        //             }
        //         }
        //     }
        // }
        return $next($request);
    }
    private function handleToken($token, $source) {
        if(!empty($source)) {
            
            if($source == 1) {
                $source = 'ent';
            }
            if($source == 2) {
                $source = 'sme';
            }
            $sources = Sources::where('source_name', $source)->first();
            // print_r($sources); exit;
        
            if(empty($sources)) {
                return Utils::sendFailedResponse([], "Invalid Source");
            }
            // return true;
        } else {
            return Utils::sendFailedResponse([], "Source is empty");
        }
        config(['env.persist_code' => $token]);
    }
    // private function handleSmeToken($smeToken, $token, $request) {
    //     $access = ApiAccessData::where('access_key', $smeToken)
    //         ->where('status', 1)->first();
    //     if(!empty($access)) {
    //         $userDetails = UserDetails::where('persist_code', $token)->first();
    //         if (empty($userDetails)) {
    //             return Utils::sendFailedResponse([], "Authentication Failed");
    //         }
    //     } else {
    //         return Utils::sendFailedResponse([], "No Access token matched");
    //     }
    //     config(['env.sme_code' => $smeToken]);
    // }
    // private function handleSystemToken($systemToken, $token, $request) {
    //     $access = ApiAccessData::where('access_key', $systemToken)
    //         ->where('status', 1)->first();
    //     if(!empty($access)) {
    //         $userDetails = UserDetails::where('persist_code', $token)->first();
    //         if (empty($userDetails)) {
    //             return Utils::sendFailedResponse([], "Authentication Failed");
    //         }
    //     } else {
    //         return Utils::sendFailedResponse([], "No Access token matched");
    //     }
    //     config(['env.system_code' => $systemToken]);
    // }
}
