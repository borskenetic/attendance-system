<?php

namespace App\Models;

use App\Support\SchoolLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Program extends Model
{
    use HasFactory;

    protected $fillable = ['program_code', 'program_name', 'school_level', 'total_years'];

    public function years()
    {
        return $this->hasMany(ProgramYear::class);
    }

    public function schoolLevelLabel(): string
    {
        return SchoolLevel::label($this->school_level);
    }

    public static function groupedForSelect(): Collection
    {
        return static::query()
            ->get()
            ->groupBy(fn (self $program) => $program->school_level ?: SchoolLevel::COLLEGE)
            ->map(function (Collection $group, string $level) {
                if (SchoolLevel::usesIndividualGrades($level)) {
                    return $group
                        ->sortBy(fn (self $program) => SchoolLevel::gradeSortValue($program->program_name))
                        ->values();
                }

                return $group->sortBy('program_name')->values();
            });
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($program) {
            // Delete all related years and their courses
            foreach ($program->years as $year) {
                $year->courses()->delete();
                $year->delete();
            }
        });
    }
    
    public function books() {
        return $this->belongsToMany(Book::class, 'book_program');
    }
}
