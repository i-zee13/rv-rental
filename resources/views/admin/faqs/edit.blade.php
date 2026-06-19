@extends('admin.layout')

@section('title', 'Edit FAQ')

@section('content')
<h1 class="text-xl font-semibold mb-4">Edit FAQ</h1>
@include('admin.faqs._form', ['faq' => $faq])
@endsection
