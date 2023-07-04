<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Emis extends Eloquent
{
    protected $connection = 'mysql';
    protected $collection = 'emis';
    public function saveData($inputs)
    {
        foreach ($inputs as $key => $value) {
        
            if (!empty($value)) {
                $this->$key = $value;
            }
        }
       
        $this->created_at = time();
        $this->updated_at = time();
        $this->save();
        return $this;
    }


}
