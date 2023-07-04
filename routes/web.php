<?php
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use Illuminate\Http\Request;

$router->post('register',[
    'uses' => 'UserController@register'
   ]);

 $router->group(['middleware' => 'auth:api'], function () use ($router) {
    $router->post('/apply-loan',['uses'=>'LoanController@apply']);
     $router->get('/get-pending-loan',['uses'=>'LoanController@getPendingLoan']);
     $router->post('/approved-loan',['uses' => 'LoanController@approvedLoan']);
     $router->post('/pay-emi',['uses'=>'LoanController@payEmi']);
     
});