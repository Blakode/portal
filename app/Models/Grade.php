<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ]; 


/***
 -------------------------------------
|associate multiple classes to a grade (primary 1)
-------------------------------------
***/
public function class_type()
{
    return $this->hasMany(ClassType::class, 'grade_id');
}


}
