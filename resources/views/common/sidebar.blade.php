   
  @php
        $usuario = \Illuminate\Support\Facades\Auth::user()->id; 
        $rol = \App\Model\userHasRoles::where('idUser', $usuario)->select('idRoles')->get();

        $permiso = App\Model\user_has_view::join('roles_users','user_has_views.rol','=','roles_users.id')->select('user_has_views.permits as vista')
                    ->where('user_has_views.rol',$rol[0]->idRoles)->distinct()->get();

  @endphp


<nav class="sidebar-nav">
    <ul class="nav sidebar-mineco sidebar sidebar-dark navbar-nav accordion"> 
    <hr class="sidebar-divider">
        <div class="sidebar-heading">MÓDULOS </div>
        <li class="nav-item nav-dropdown">
            @if($permiso->count() > 0)
            <a class="nav-link nav-dropdown-toggle" href="#">
                Administrativo 
            </a>
                <ul class="nav-dropdown-items">
                    @foreach ($permiso as $permisos)
                        @if($permisos->vista == 1)
                            <li class="nav-item ml-3">
                                <a class="nav-link" href="{{ route('recepcion') }}">
                                    <i class="nav-icon fas fa-angle-double-right"></i> Ingreso</a>
                            </li>
                        @elseif($permisos->vista == 2)
                            <li class="nav-item ml-3">
                                <a class="nav-link" href="{{ route('recibido') }}">
                                    <i class="nav-icon fas fa-angle-double-right"></i> Documentos</a>
                            </li>
                        @elseif($permisos->vista == 3)
                            <li class="nav-item ml-3">
                                <a class="nav-link" href="{{ route('showDocument') }}">
                                <i class="nav-icon fas fa-angle-double-right"></i> Expedientes</a>
                            </li>   
                        @elseif($permisos->vista == 4)
                            <li class="nav-item ml-3">
                                <a class="nav-link" href="{{ route('bitacora') }}">
                                    <i class="nav-icon fas fa-angle-double-right"></i> consulta</a>
                            </li>   
                        @elseif($permisos->vista == 5)
                            <li class="nav-item ml-3">
                                <a class="nav-link" href="{{ route('Mis_ingresos') }}">
                                    <i class="nav-icon fas fa-angle-double-right"></i> Mis Ingresos</a>
                            </li>   
                        @elseif($permisos->vista == 6)
                                <li class="nav-item ml-3">
                                    <a class="nav-link" href="{{ route('Seguimientos') }}">
                                        <i class="nav-icon fas fa-angle-double-right"></i> Mis Seguimientos</a>
                                </li>       
                        
                        @elseif($permisos->vista == 8)
                                <li class="nav-item ml-3">
                                    <a class="nav-link" href="{{ route('Remitente') }}">
                                        <i class="nav-icon fas fa-angle-double-right"></i> Remitentes</a>
                                </li>       

                                <li class="nav-item ml-3">
                                    <a class="nav-link" href="{{ route('inbox') }}">
                                        <i class="nav-icon fas fa-angle-double-right"></i> Notificaciones</a>
                                </li>       

                                <li class="nav-item ml-3">
                                    <a class="nav-link" href="{{ route('inbox-chat') }}">
                                        <i class="nav-icon fas fa-angle-double-right"></i> Mensajes</a>
                                </li>       
                        @endif
                    @endforeach
                </ul>
            @endif
        
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
                <li class="nav-item ml-3">
                    <a class="nav-link" href="{{ route('archivos')}}">
                        <i class="nav-icon fab fa-red-river"></i> Drive</a>
                </li>                
                <li class="nav-item ml-3">
                    <a class="nav-link" href="{{ route('respaldo')}}">
                        <i class="nav-icon fas fa-download"></i> Respaldo</a>
                </li>                
            </ul>
        </li>
        @else
        @endif
    </ul>
</nav>
