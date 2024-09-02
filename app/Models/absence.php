<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class absence extends Model
{
    use HasFactory;
    protected $fillable = ['student_id', 'section_id', 'timeslot_id', 'day'];



    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function section()
    {
        return $this->belongsTo(section::class, 'section_id');
    }

    public function timeslot()
    {
        return $this->belongsTo(timeslot::class, 'timeslot_id');
    }


}
