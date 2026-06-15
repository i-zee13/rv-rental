@extends('admin.layout')

@section('title', 'Edit Property')

@section('content')
@include('admin.properties._form', ['property' => $property, 'types' => $types])
@endsection
