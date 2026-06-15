@extends('admin.layout')

@section('title', 'Add Property')

@section('content')
@include('admin.properties._form', ['property' => null, 'types' => $types])
@endsection
