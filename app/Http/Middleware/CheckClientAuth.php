<?php

namespace App\Http\Middleware;

use App\Libraries\Utils;
use Closure;

class CheckClientAuth
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

        $authorization = $request->header('Authorization');

        if (empty(config('env.user_list'))) {
            return Utils::sendFailedResponse([], 'User not configured');
        }

        if (empty($authorization)) {
            return Utils::sendFailedResponse([], 'Please send authorization');
        }

        $user_arr = explode(",", config('env.user_list'));

        $user_id = -1;

        foreach ($user_arr as $element) {

            $user_id_array = explode(":", $element);

            if (trim($user_id_array[1]) == $authorization) {
                $user_id = $user_id_array[0];
            }
        }

        if ($user_id == -1) {
            return Utils::sendFailedResponse([], 'User  does not exist');
        }

        $request['user_id'] = $user_id;

        return $next($request);
    }
}
