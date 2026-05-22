<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'control_no','date_time_stamp', 'fixed_length_data',
        'isbn', 'price','cataloging_source_a','cataloging_source_b','cataloging_source_e',
        'main_author', 'title_statement',
        'title_author','edition',
        'pub_place', 'publisher', 'pub_year',
        'pages', 'illustrations', 'size', 'volume',
        'content_type','content_code', 'media_type','media_code','carrier_type','carrier_code',
        'series_title', 'general_note', 'bibliography_note',
        'source_vendor', 'source_date',
        'subject_topic', 'subject_form', 'genre',
        'library_name', 'section', 'call_number',
        'accession_no','created_at','updated_at','barcode',
        'rfid','availability','year','course','program','cover_image'
    ];

    public function logs() {
        return $this->hasMany(BookLog::class);
    }
    
    // App\Models\Book.php
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'book_program', 'book_id', 'program_id');
    }

}
