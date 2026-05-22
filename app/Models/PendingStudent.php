<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingStudent extends Model
{
    protected $fillable = [
        'student_id',
        'id_number',
        'firstname',
        'lastname',
        'mobile_number',
        'middle_initial',
        'course',
        'year',
        'profile_picture',
        'qrcode',
        'birth_date',
        'blood_type',
        'emergency_person',
        'emergency_relationship',
        'emergency_number',
        'emergency_address',
        'student_signature',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
