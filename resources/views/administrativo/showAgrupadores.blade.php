@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <asignapadre csrf="{{ csrf_token() }}"></asignapadre>
            </div>
        </div>
    </div>
@endsection
@section('breadcrumb')
    Listado de documentos por agrupar
@endsection
