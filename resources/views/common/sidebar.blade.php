   
  @php
        $usuario = \Illuminate\Support\Facades\Auth::user()->id; 
        $rol = \App\Model\userHasRoles::where('idUser', $usuario)->select('idRoles')->get();

        $permiso = App\Model\user_has_view::join('users','user_has_views.idUser','=','users.id')->select('user_has_views.idView as vista')->where('user_has_views.idUser',$usuario)->get();

  @endphp


<nav class="sidebar-nav">
    <ul class="nav sidebar-mineco sidebar sidebar-dark navbar-nav accordion"> 
    <hr class="sidebar-divider">
        <div class="sidebar-heading">MÓDULOS </div>
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                Administrativo </a>
            <ul class="nav-dropdown-items">
            @foreach ($permiso as $permisos)
                @if($permisos->vista == 1)
                    <li class="nav-item ml-3">
                        <a class="nav-link" href="{{ route('recepcion') }}">
                            <i class="nav-icon fas fa-plus"></i> Ingreso</a>
                    </li>
                @elseif($permisos->vista == 2)
                    <li class="nav-item ml-3">
                        <a class="nav-link" href="{{ route('recibido') }}">
                            <i class="nav-icon fas fa-plus"></i> Documentos</a>
                    </li>
                @elseif($permisos->vista == 3)
                    <li class="nav-item ml-3">
                        <a class="nav-link" href="{{ route('showDocument') }}">
                        <i class="nav-icon fas fa-plus"></i> Expedientes</a>
                    </li>   
                @elseif($permisos->vista == 4)
                    <li class="nav-item ml-3">
                        <a class="nav-link" href="{{ route('bitacora') }}">
                            <i class="nav-icon fas fa-plus"></i> consulta</a>
                    </li>   
                @elseif($permisos->vista == 5)
                    <li class="nav-item ml-3">
                        <a class="nav-link" href="{{ route('Mis_ingresos') }}">
                            <i class="nav-icon fas fa-plus"></i> Mis Ingresos</a>
                    </li>   
                @elseif($permisos->vista == 6)
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('Seguimientos') }}">
                                <i class="nav-icon fas fa-plus"></i> Mis Seguimientos</a>
                        </li>       
                @endif
            @endforeach
            </ul>
        
        </li>        
        @if (Auth()->user()->admin == 1)
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                 Herramientas</a>
            <ul class="nav-dropdown-items">
                <!-- <li class="nav-item ml-3">
                    <a class="nav-link" href="./showEntidad">
                        <i class="nav-icon fas fa-edit"></i> Entidades</a>
                </li> -->
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./showunidades">
                        <i class="nav-icon fas fa-address-book"></i> Direcciones</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="{{ route('SystemViews')}}">
                    <i class="nav-icon fas fa-list"></i> Menú</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="{{ route('userPermits')}}">
                    <i class="nav-icon fas fa-calendar-check"></i> Permisos</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./roles">
                        <i class="nav-icon fas fa-universal-access"></i> Roles</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./showUsuarios">
                        <i class="nav-icon fas fa-users"></i> Usuarios</a>
                </li>                
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./showReceptores">
                        <i class="nav-icon fas fa-user-plus"></i> Receptores</a>
                </li> 
                                              
                <li class="nav-item ml-3">
                    <a class="nav-link" href="{{ route('settings') }}">
                        <i class="nav-icon fas fa-cogs"></i> Configuración</a>
                </li>                
                <li class="nav-item ml-3">
                    <a class="nav-link" href="{{ route('delegado')}}">
                        <i class="nav-icon fas fa-users"></i> Delegados</a>
                </li>                
            </ul>
        </li>
        @else
        @endif
    </ul>
</nav>
