<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'grade_id'
    ]; 

    /***
     -------------------------------------
    |associate a grade(primary 1) to multiple classes 
    -------------------------------------
    ***/
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    /***
     -------------------------------------
    |associate a multple student to a class
    -------------------------------------
    ***/
    public function class_student()
    {
        return $this->hasMany(Student::class, 'class_types_id');
    }

    /***
     -------------------------------------
    |many to many relationship between the teacher and the class (class_type)
    -------------------------------------
    ***/
    public function teachers()
    {
    return $this->belongsToMany(User::class, 'class_type_teacher', 'class_type_id', 'teacher_id');
    }

}
