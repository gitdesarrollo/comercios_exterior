<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="./">
        <img
          class="navbar-brand-full"
          src="imagenes/gobierno.jpg"
          width="89"
          height="35"
          alt="Logo"
        />
        <img
          class="navbar-brand-minimized"
          src="imagenes/gobierno.jpg"
          width="30"
          height="30"
          alt="Logo"
        />
      </a>
      <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class=" navbar-toggler-icon"></span>
      </button>
          <ul class="nav navbar-nav ml-auto mr-3">
              <mensaje-component ></mensaje-component> 


        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="user mr-2 d-none d-lg-inline text-gray-600 small"></span>
                <i class="fa fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                    <button class="dropdown-item">
                        <a class="dropdown-item" href="">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Cambiar Clave
                        </a>
                    </button>
                <div class="dropdown-divider"></div>
                <button class="dropdown-item">
                    <a class="dropdown-item" href="/logout">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Cerrar sesión
                    </a>
                </button>
            </div>
        </li>
    </ul>
</header>

