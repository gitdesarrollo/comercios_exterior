   
  @php
        $usuario = \Illuminate\Support\Facades\Auth::user()->id; 
        $rol = \App\Model\userHasRoles::where('idUser', $usuario)->select('idRoles')->get();

  @endphp


<nav class="sidebar-nav">
    <ul class="nav sidebar-mineco sidebar sidebar-dark navbar-nav accordion"> 
    <hr class="sidebar-divider">
        <div class="sidebar-heading">MÓDULOS </div>        
        @if (Auth()->user()->admin == 1)
        <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">
                        Administrativo </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('recepcion') }}">
                                <i class="nav-icon fas fa-plus"></i> Ingreso</a>
                        </li>
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('recibido') }}">
                                <i class="nav-icon fas fa-plus"></i> Documentos</a>
                        </li>
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('showDocument') }}">
                            <i class="nav-icon fas fa-plus"></i> Expedientes</a>
                        </li>   
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('bitacora') }}">
                                <i class="nav-icon fas fa-plus"></i> consulta</a>
                        </li>   
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('Mis_ingresos') }}">
                                <i class="nav-icon fas fa-plus"></i> Mis Ingresos</a>
                        </li>   
    
                    </ul>
        </li>
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
        
            @if($rol[0]->idRoles  == 3)
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    Administrativo </a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item ml-3">
                        <a class="nav-link" href="{{ route('recepcion') }}">
                            <i class="nav-icon fas fa-plus"></i> Ingreso</a>
                    </li> 
                    <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('Mis_ingresos') }}">
                                <i class="nav-icon fas fa-plus"></i> Mis Ingresos</a>
                    </li>    
                </ul>
            </li>
            @elseif($rol[0]->idRoles  == 5)

                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">
                        Administrativo </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('showDocument') }}">
                                <i class="nav-icon fas fa-plus"></i> Expedientes</a>
                        </li>   
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('bitacora') }}">
                                <i class="nav-icon fas fa-plus"></i> consulta</a>
                        </li> 
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('recibido') }}">
                                <i class="nav-icon fas fa-plus"></i> Documentos</a>
                        </li> 
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('Mis_ingresos') }}">
                                <i class="nav-icon fas fa-plus"></i> Mis Ingresos</a>
                        </li>   
                    </ul>
                </li>

            @else
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">
                        Administrativo </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('recepcion') }}">
                                <i class="nav-icon fas fa-plus"></i> Ingreso</a>
                        </li>
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('recibido') }}">
                                <i class="nav-icon fas fa-plus"></i> Documentos</a>
                        </li>
                        <li class="nav-item ml-3">
                            <a class="nav-link" href="{{ route('Mis_ingresos') }}">
                                <i class="nav-icon fas fa-plus"></i> Mis Ingresos</a>
                        </li>  
    
                    </ul>
                </li>
                @endif
        @endif
        <!-- <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                <i class="nav-icon fas fa-address-book"></i> Inventario</a>
            <ul class="nav-dropdown-items">
                <li class="nav-item ml-3">
                    <a class="nav-link" href="active">
                        <i class="nav-icon fas fa-edit"></i> s</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="barCode">
                        <i class="nav-icon fas fa-barcode"></i> Código de Barras</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="search">
                        <i class="nav-icon fas fa-barcode"></i> Buscar</a>
                </li>                
            </ul>
        </li> -->
    </ul>
</nav>
