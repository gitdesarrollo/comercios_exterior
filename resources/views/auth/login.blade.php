@extends('layouts.session')
@section('titulo','Inicio de sesión')

@section('contenido')
<form class="form-signin shadow-lg p-3 bg-white rounded" method="POST" action="{{ route('login') }}">
        @csrf
    <div class="text-center mt-3">
        <i class="fa fa-lock fa-5x text-dark"></i>
        <h1 class="h3 mb-3 font-weight-normal">Iniciar sesión</small></h1>
    </div>
    <div class="form-label-group">
        <input type="text" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Introdúzca su correo mineco" required {{ old('email') ? '' : 'autofocus' }}>
        <!-- <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"  placeholder="Introdúzca su correo mineco" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus> -->
        <label for="email" title="Correo mineco">Correo mineco</label>
    </div>    
    <div class="form-label-group">
        <input type="password" id="password" name="password" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Contraseña" required>
        <!-- <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password"> -->
        <label for="password">Contraseña</label>
        <a href="#" id="show-password" class="float-right my-2 text-decoration-none text-danger">Mostrar contraseña</a>
        @if ($errors->has('email'))
        <span class="text-danger">
            {{ $errors->first('email') }}
        </span>
        @endif
    </div>
    <br>
    <!-- <div class="checkbox mb-3">
        <label>
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Recordarme
        </label>
    </div> -->
    <button class="btn btn-lg btn-primary btn-block" type="submit"><i class="fa fa-sign-in fa-lg mr-2"></i>Acceder</button>
    <div class="text-center mt-3">
        <!-- a class="btn btn-link text-center link" href="/password/reset">Olvidé mi contraseña</a-->
    </div>
    <!-- <p class="mt-5 mb-3 text-muted text-center">&copy; 2018-2019</p> -->
</form>
@endsection
@section('script')

<script>
    $(document).ready(function(){
        let $password = $('#password');

        $('#password').focus(function(){
            $(this).removeClass('is-invalid').siblings('span.text-danger').attr('hidden', 'hidden');
        });

        $('#show-password').click(function(){
            if($password.attr('type') == 'password'){
                $password.removeAttr('type', 'password');
                $password.attr('type', 'text');
                $('#show-password').html('Ocultar contraseña').removeClass('text-danger').addClass('text-success');
                $('#password').focus();
            }else{
                $password.removeAttr('type', 'text');
                $password.attr('type', 'password');
                $('#show-password').html('Mostrar contraseña').removeClass('text-success').addClass('text-danger');
                $('#password').focus();
            }
        });

        $(".app-name").html(localStorage.getItem('app-name-aj'));
    });
</script>
@endsection