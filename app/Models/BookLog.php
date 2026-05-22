<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BookLog extends Model
{
    use HasFactory;

    protected $fillable = ['book_id', 'patron_name', 'status', 'timestamp'];

    protected $casts = [
        'timestamp' => 'datetime', // 👈 ensures Eloquent handles it as Carbon
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getTimestampManilaAttribute()
    {
        if (!$this->timestamp) {
            return null;
        }

        return $this->timestamp->copy()
            ->timezone('Asia/Manila')
            ->format('Y-m-d h:i A'); // e.g., 2025-07-25 12:43 PM
    }
}
