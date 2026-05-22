<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    // POST /checkout/{id}
    public function checkout(Request $request)
    {
        // Example: save checkout details
        $book = Book::find($request->book_id);
    
        // Save book log or whatever logic you have...
    
        return view('checkout.receipt', [
            'title' => $book->title_statement,
            'author' => $book->main_author,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }
}
