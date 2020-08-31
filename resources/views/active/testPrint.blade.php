@extends('layouts.app')
@section('content')

      <div >
            <div >
                  <div class="container">
                        <div class="BarCode">{!! DNS1D::getBarcodeHTML("123","C128",2,80,'black',false) !!}</div>
                  </div>
            </div>
      </div>


@endsection
@section('breadcrumb')
    Inventario de Bienes
@endsection

