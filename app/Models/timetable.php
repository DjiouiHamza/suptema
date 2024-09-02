<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class timetable extends Model
{
    use HasFactory;
    protected $fillable = ['teacher_id', 'section_id', 'timing', 'class_name', 'day'];


    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    


}
