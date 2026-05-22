<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['description'];

    // Optional: Relationship (if you want later)
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
