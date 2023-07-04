<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Libraries\Utils as Utils;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Illuminate\Support\Arr;

class UserController extends Controller{

    public function register(\Illuminate\Http\Request $request){
        $inputs=$request->all();
       
        $Validator = Validator::make($inputs, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);


        if ($Validator->fails()) {
            $errors = $Validator->messages()->all();
            return Utils::sendFailedResponse($errors[0], 'Please provide all the mandatory fields');
        }

       $inputs['password']=Hash::make($inputs['password']);
       $user=User::create($inputs);

       if(!empty(Arr::get($inputs,'client_id',[]))){
        
       $client= \DB::table('oauth_clients')->where('id',$inputs['client_id'])->exists();
       $user->client_id=Arr::get($inputs,'client_id',null);
       $user->save();
       if($client){
           $client=\DB::table('oauth_clients')->where('id',$inputs['client_id'])->first();
           $user->client_secret=$client->secret;
       }else{
    
        $client = Passport::client()->forceFill([
            'id'=>$inputs['client_id'],
            'user_id' => null,
            'name' => $inputs['name'].'_client',
            'secret' => hash('sha256', $inputs['client_id']),
            'redirect' => 'http://localhost',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
        ]);
        $client->save();
        $user->client_secret=$client->secret;
       }
       
       }
      
       return Utils::sendSuccessResponse(['user'=>$user],'User Created');
    }

    
}
