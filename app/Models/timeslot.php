<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class timeslot extends Model
{
    use HasFactory;
    protected $fillable = ['time'];


    public function absences()
    {
        return $this->hasMany(absence::class);
    }
}
