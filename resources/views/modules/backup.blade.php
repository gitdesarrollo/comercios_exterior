@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <respaldo-archivos csrf="{{ csrf_token() }}"></respaldo-archivos>
            </div>
        </div>
    </div>
@endsection
@section('breadcrumb')
    Respaldo de archivos
@endsection
