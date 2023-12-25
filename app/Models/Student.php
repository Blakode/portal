<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable  = [
        'name',
        'email',
        'avatar',
        'user_id',
        'password'
    ]; 

/***
 -------------------------------------
|associate a user(parent) to multiple student
-------------------------------------
***/
    public function parent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
