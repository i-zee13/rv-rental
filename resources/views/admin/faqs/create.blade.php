@extends('admin.layout')

@section('title', 'Add FAQ')

@section('content')
<h1 class="text-xl font-semibold mb-4">Add FAQ</h1>
@include('admin.faqs._form', ['faq' => null])
@endsection
