<?php

namespace App\Models;

use App\Support\SchoolLevel;
use Illuminate\Database\Eloquent\Model;

class ProgramYear extends Model
{
    protected $fillable = ['program_id', 'year_level'];

    protected $appends = ['display_label'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function courses()
    {
        return $this->hasMany(ProgramCourse::class);
    }

    public function getDisplayLabelAttribute(): string
    {
        $level = $this->program?->school_level ?? SchoolLevel::COLLEGE;

        return SchoolLevel::yearLabel($level, (int) $this->year_level, $this->program?->program_name);
    }
}
