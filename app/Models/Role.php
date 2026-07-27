<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['description'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Resolve the faculty role id, creating the role if it is missing.
     * Avoids hardcoding role_id = 2 (which breaks when roles were never seeded
     * or ids differ across environments).
     */
    public static function facultyId(): int
    {
        $role = static::query()->firstOrCreate(
            ['description' => 'faculty'],
            ['description' => 'faculty']
        );

        return (int) $role->id;
    }

    public static function studentId(): int
    {
        $role = static::query()->firstOrCreate(
            ['description' => 'student'],
            ['description' => 'student']
        );

        return (int) $role->id;
    }
}
