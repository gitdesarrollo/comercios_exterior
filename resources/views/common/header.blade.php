  @php
        $idusuario = \Illuminate\Support\Facades\Auth::user()->id; 
        $idusuarioCount = \Illuminate\Support\Facades\Auth::user()->id; 
        $trasladoU = \App\Model\traslados::where('idUsuarioTramito',$idusuario)->select('id')->count();

        
            $documento = \Illuminate\Support\Facades\DB::select("SELECT
                    d.id as code,
                    d.interesado AS empresa,
                    d.correlativo_documento AS correlativo,
                    d.descripcion as descripcion,
                    ed.id AS estado,
                    us.NAME AS usuario,
                    d.created_at as fecha,
                    tras.id as idTraslado,
                    rol.idRoles as rol,
                    d.correlativo_externo as formato,
                    d.tracing,
                    (SELECT id FROM tracings WHERE idDocumento = d.id AND estado in (4,5)) AS idTracing,
                    (SELECT 
                    (CASE 
                        WHEN idUsuarioTraslada = :local THEN 'true'
                        ELSE 'false'
                        END) AS flag 
                        FROM tracings WHERE idDocumento = d.id AND estado in (4,5)) AS flag,
                    concat('./../files/',files.`file`) AS url
                    FROM documentos d
                    INNER JOIN traslados tras
                        ON d.id = tras.id
                    INNER JOIN estado_documentos ed
                    ON tras.estado = ed.id
                    INNER JOIN users us
                        ON tras.idUsuarioTramito = us.id
                    INNER JOIN user_has_roles rol
                        ON us.id = rol.idUser
                    INNER JOIN upload_files files
                        ON files.evento_id = d.id
                    WHERE us.id = :id  AND d.id_status != 7 AND files.formato = 'pdf' AND ed.id = 2
                    ",['id' => $idusuario, 'local' =>$idusuario]);

            $cantidad = \Illuminate\Support\Facades\DB::select("SELECT
                    count(d.id) as code
                    FROM documentos d
                    INNER JOIN traslados tras
                        ON d.id = tras.id
                    INNER JOIN estado_documentos ed
                    ON tras.estado = ed.id
                    INNER JOIN users us
                        ON tras.idUsuarioTramito = us.id
                    INNER JOIN user_has_roles rol
                        ON us.id = rol.idUser
                    INNER JOIN upload_files files
                        ON files.evento_id = d.id
                    WHERE us.id = :id  AND d.id_status != 7 AND files.formato = 'pdf' AND ed.id = 2
                    ",['id' => $idusuarioCount]);





  @endphp



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
              <!-- <mensaje-component ></mensaje-component>  -->


        <div class="topbar-divider d-none d-sm-block"></div>
        <!-- <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle  pr-2" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="far fa-comment-dots"></i> 
                <span class="badge badge-pill badge-info mr-3">
                @foreach ($cantidad as $mensajes)
                    {{ $mensajes->code }}
                @endforeach
                </span>  
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                @foreach ($documento as $documentos)
                   
                    <div class="dropdown-divider"></div>
                    <button class="dropdown-item">
                        <a class="dropdown-item" href="{{ route('inbox') }}">
                            <i class="fas fa-envelope-open fa-sm fa-fw mr-2 text-gray-400"></i>
                            {{ $documentos->formato }}
                        </a>
                    </button>
                @endforeach
            </div>
        </li> -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle  pr-2" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <!-- <span class="">Notificaciones </span> -->
                <i class="fas fa-envelope"></i> 
                <span class="badge badge-pill badge-danger mr-3">
                @foreach ($cantidad as $mensajes)
                    {{ $mensajes->code }}
                @endforeach
                </span>  
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                @foreach ($documento as $documentos)
                   
                    <div class="dropdown-divider"></div>
                    <button class="dropdown-item">
                        <a class="dropdown-item" href="{{ route('inbox') }}">
                            <i class="fas fa-envelope-open fa-sm fa-fw mr-2 text-gray-400"></i>
                            {{ $documentos->formato }}
                        </a>
                    </button>
                @endforeach
            </div>
        </li>
        <li class="nav-item dropdown no-arrow ">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span>{{ auth()->user()->name!=null ? auth()->user()->name : "Administrator" }}</span>
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

