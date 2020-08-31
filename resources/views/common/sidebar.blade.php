<nav class="sidebar-nav">
    <ul class="nav">
        {{-- <li class="nav-title"></li> --}}
        <!-- <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                <i class="nav-icon fas fa-book-open"></i> Catálogo</a>
            <ul class="nav-dropdown-items">
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./product">
                        <i class="nav-icon fas fa-user-plus"></i> Producto</a>
                </li>
            </ul>
        </li> -->
        
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                <i class="nav-icon fas fa-address-book"></i> Activos </a>
            <ul class="nav-dropdown-items">
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./active">
                        <i class="nav-icon fas fa-edit"></i> Ingreso</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./List">
                        <i class="nav-icon fas fa-file-invoice"></i> Cuentas</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./search">
                        <i class="nav-icon fas fa-ticket-alt"></i> Ticket</a>
                </li>                
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./inventarioFisico">
                        <i class="nav-icon fas fa-dolly-flatbed"></i> Inventario Físico</a>
                </li>                
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./Reporteinventario">
                        <i class="nav-icon fas fa-file-invoice"></i> Reporte</a>
                </li>                
            </ul>
        </li>
        
        @if (Auth()->user()->admin == 1)
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                <i class="nav-icon fas fa-cogs"></i> Herramientas</a>
            <ul class="nav-dropdown-items">
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./showEntidad">
                        <i class="nav-icon fas fa-edit"></i> Entidades</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./showunidades">
                        <i class="nav-icon fas fa-file-invoice"></i> Unidades</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./roles">
                        <i class="nav-icon fas fa-file-invoice"></i> Roles</a>
                </li>
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./showUsuarios">
                        <i class="nav-icon fas fa-ticket-alt"></i> Usuarios</a>
                </li>                
                <li class="nav-item ml-3">
                    <a class="nav-link" href="./visualizar">
                        <i class="nav-icon fas fa-dolly-flatbed"></i> Importar</a>
                </li>                
                <!-- <li class="nav-item ml-3">
                    <a class="nav-link" href="./">
                        <i class="nav-icon fas fa-file-invoice"></i> Dependencias</a>
                </li>                 -->
            </ul>
        </li>
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
