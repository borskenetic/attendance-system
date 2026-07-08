<?php

namespace App\Support;

class SchoolLevel
{
    public const COLLEGE = 'college';

    public const SENIOR_HIGH = 'senior_high';

    public const JUNIOR_HIGH = 'junior_high';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::COLLEGE => 'College',
            self::SENIOR_HIGH => 'Senior High',
            self::JUNIOR_HIGH => 'Junior High',
        ];
    }

    /** @return list<string> */
    public static function ordered(): array
    {
        return [self::COLLEGE, self::SENIOR_HIGH, self::JUNIOR_HIGH];
    }

    public static function label(?string $level): string
    {
        if ($level === null || $level === '') {
            return 'College';
        }

        return self::labels()[$level] ?? ucwords(str_replace('_', ' ', $level));
    }

    public static function usesIndividualGrades(string $level): bool
    {
        return in_array($level, [self::SENIOR_HIGH, self::JUNIOR_HIGH], true);
    }

    public static function defaultYearCount(string $level): int
    {
        return self::usesIndividualGrades($level) ? 1 : 4;
    }

    /** @return list<string> */
    public static function yearOptions(string $level): array
    {
        return match ($level) {
            self::SENIOR_HIGH => ['Grade 11', 'Grade 12'],
            self::JUNIOR_HIGH => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'],
            default => ['1st Year', '2nd Year', '3rd Year', '4th Year'],
        };
    }

    /** @return list<array{code: string, name: string, school_level: string}> */
    public static function defaultGradePrograms(): array
    {
        $programs = [];

        foreach (self::yearOptions(self::SENIOR_HIGH) as $grade) {
            $programs[] = [
                'program_code' => 'GR'.str_replace('Grade ', '', $grade),
                'program_name' => $grade,
                'school_level' => self::SENIOR_HIGH,
            ];
        }

        foreach (self::yearOptions(self::JUNIOR_HIGH) as $grade) {
            $programs[] = [
                'program_code' => 'GR'.str_replace('Grade ', '', $grade),
                'program_name' => $grade,
                'school_level' => self::JUNIOR_HIGH,
            ];
        }

        return $programs;
    }

    public static function gradeSortValue(string $programName): int
    {
        if (preg_match('/(\d+)/', $programName, $matches)) {
            return (int) $matches[1];
        }

        return 999;
    }

    public static function yearLabel(string $level, int $yearLevel, ?string $programName = null): string
    {
        if (self::usesIndividualGrades($level) && $programName) {
            return $programName;
        }

        $options = self::yearOptions($level);

        return $options[$yearLevel - 1] ?? ('Year '.$yearLevel);
    }
}
