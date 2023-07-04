<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class UserDetails extends Eloquent
{
    protected $connection = 'mysql';
    protected $collection = 'user_details';
    
}