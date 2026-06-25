@extends(auth()->check() && auth()->user()->can('isAdminOrStaff') ? 'layouts.app' : 'layouts.shell-public')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/books/index.css') }}">
@endsection

@section('banner')
    <img src="{{ asset('images/Bannernew.jpg') }}" alt="Banner" class="banner-img">
@endsection
