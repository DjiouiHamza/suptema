<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class student extends Model
{
    use HasFactory;
    protected $table='students';
    protected $fillable=['full_name', 'age', 'email', 'section_id', 'phone', 'phone_number'];
    protected $primaryKey='id';




    public function section(){
        return $this->belongsTo(section::class);
    }


    public function teachers(){
        return $this->hasMany(user::class);

    }

    public function absences()
    {
        return $this->hasMany(absence::class, 'student_id');
    }


}
