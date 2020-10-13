@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <recibos-component csrf="{{ csrf_token() }}"></recibos-component>
            </div>
        </div>
    </div>
@endsection
@section('breadcrumb')
    Listado de Documentos Recibidos
@endsection
