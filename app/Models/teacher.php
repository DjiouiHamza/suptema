<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class teacher extends Model
{
    use HasFactory;

    public function students(){
        return $this->hasMany(student::class);

    }

    public function sections(){
        return $this->hasMany(teacher::class);

    }

    public function timetables()
    {
        return $this->hasOne(Timetable::class, 'teacher_id');
    }


}
