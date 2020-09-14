@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <traslados-component :id_departamento="{{ $departamento }}"></traslados-component>
            </div>
        </div>
    </div>
@endsection
@section('breadcrumb')
    Traslados
@endsection
