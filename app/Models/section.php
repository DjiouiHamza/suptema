<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class section extends Model
{
    use HasFactory;
    protected $table='sections';
    protected $primaryKey='id';
    protected $fillable=[
        'name',
    ];


    public function timetables()
    {
        return $this->hasOne(Timetable::class, 'section_id');
    }



    public function students(){
        return $this->hasMany(student::class);

    }

    public function teachers(){
        return $this->hasMany(user::class);

    }

    public function absences()
    {
        return $this->hasMany(absence::class);
    }
    
}
