@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <social-chat :usuario="{{ json_encode($usuario) }}" :code=" {{ json_encode($code) }} " ></social-chat>
            </div>
        </div>
    </div>
@endsection
@section('breadcrumb')
    Respaldo de archivos
@endsection
