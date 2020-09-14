@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <documento-component :id_departamento="{{ $departamento }}"></documento-component>
            </div>
        </div>
    </div>
@endsection
@section('breadcrumb')
    Crear Documentos
@endsection
