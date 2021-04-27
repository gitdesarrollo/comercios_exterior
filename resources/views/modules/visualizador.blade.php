@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <visualizador-pdf csrf="{{ csrf_token() }}"></visualizador-pdf>
            </div>
        </div>
    </div>
@endsection
@section('breadcrumb')
    Archivos cargados
@endsection
