<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class TableColumns
{
    /**
     * Keep only attributes that exist as columns on the given table.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public static function filter(string $table, array $attributes): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        $filtered = [];
        foreach ($attributes as $column => $value) {
            if (Schema::hasColumn($table, $column)) {
                $filtered[$column] = $value;
            }
        }

        return $filtered;
    }
}
