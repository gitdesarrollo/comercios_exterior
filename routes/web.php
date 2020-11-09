<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route; 
use App\Model\userHasRoles;




Auth::routes();


Route::get('/','inventario@index')->name('index');
// Route::get('/home', 'HomeController@index')->name('home');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('getYear','catalogo@getYear');
Route::get('sequence/{table}','catalogo@sequences_data');
Route::post('barCode','BarCode@barcodeGet');
Route::get('barCode/{account}','BarCode@BarCodeAll');
Route::get('inventarioFisico','inventario@showInventory');
Route::get('Reporteinventario','inventario@showReport');
Route::post('reportInventory','BarCode@BarCodeAllReport');


/**
 * Router Cors
 */

 /******Product Data****************** */
 Route::get('product','inventario@showProduct');
 Route::post('addProduct','catalogo@addProduct');
 Route::get('allProduct','catalogo@allProduct');
 Route::post('editProduct','catalogo@editProduct');
 Route::post('deleteProductById','catalogo@deleteProductById');

 /****************************** */

 /** Form Active */

 Route::get('active','inventario@showActive');
 Route::post('active','inventario@addActivosBienes');
 /***************** */

  /** Form Active */
  Route::get('entidades','catalogo@getEntidad');
  Route::get('unidades','catalogo@getUnidad');
  Route::get('grupos','catalogo@getGrupo');
  Route::post('categorias','catalogo@getCategoria');
  Route::post('secciones','catalogo@getSeccion');
  Route::post('tipos','catalogo@getTipo');
  Route::post('Bienes','catalogo@getBien');
  Route::get('EstadosProducto','catalogo@getEstadoProducto');
  Route::post('PersonasEntidad','catalogo@getPersonas');
  Route::post('addproductobien','catalogo@addProductBienes');
  Route::get('dependencias','catalogo@getDependencias');
  Route::get('cuentas','catalogo@getCuentasActivo');
  Route::get('respaldos','catalogo@getDocumentosRespaldo');
  Route::get('secuenciasFac','catalogo@getSecuenciasFactura');
  /***************** */

  /** Printer BarCode */

  Route::get('printer/{data_account}','BarCode@BarCodePrinter');
  Route::get('search','inventario@showSearch'); 
  Route::get('printCode/{code}','BarCode@GetBarCodeById');
  Route::get('searchCode/{code}','BarCode@GetSearchCodeById');
  Route::get('List','inventario@showList');
  /******************** */



  /****Inventario Inicial */

  // Route::get('Inicial','InventarioInicial@SetCategory');
  // Route::get('inventarioinicial1','initialCharge@setDataDB_01');
  // Route::get('inventarioinicial2','initialCharge@setDataDB_02');
  // Route::get('inventarioinicial3','initialCharge@setDataDB_03');
  // Route::get('inventarioinicial4','initialCharge@setDataDB_04');
  // Route::get('inventarioinicial5','initialCharge@setDataDB_05');
  // Route::get('inventarioinicial6','initialCharge@setDataDB_06');
  // Route::get('inventarioinicial7','initialCharge@setDataDB_07');
  // Route::get('inventarioinicial8','initialCharge@setDataDB_08');
  // Route::get('getAccountInitial','inventario@getAccountInitial');
  // /********************* */

  /*** Inventario */
Route::post('setCountInventory','inventario@setCountInventory');
Route::get('testBar','barcode@barTest');
  /************** */
  /*** Administrativo */
Route::post('setUnidad','catalogo@setUnidad');
Route::get('showunidades','catalogo@showUnidad');
Route::get('getUnit','catalogo@getUnit');
Route::get('showEntidad','catalogo@showEntidad');
Route::post('setEntidad','catalogo@setEntidad');
Route::get('showUsuarios','catalogo@showUsuarios');
Route::get('getUser','catalogo@getUser');
Route::post('registerUser','catalogo@createUser');
Route::get('visualizar', 'catalogo@visualizar');
Route::post('setDataExcel','Inventario\ControllerInitial@setDataExcel'); 
Route::get('getRoles','catalogo@getRoles');
Route::get('roles','catalogo@showroles');
Route::post('setRoles','catalogo@setRoles');

  /************** */

 

  /********** COMERCIO EXTERIOR **************** */ 

  //Administrador
  //Crear
  Route::get('DocumentCreate','documentos@showCreate')->name('showCreate'); 
  Route::post('setDocumentos', 'documentos@createDocumento')->name('setDocumentos');
  Route::get('showReceptores','documentos@showReceptores')->name('showReceptores');
  Route::get('getReceptores','documentos@getReceptores')->name('getReceptores');
  Route::post('setReceptores','documentos@setReceptores')->name('setReceptores');
  Route::put('updateReceptor','documentos@updateReceptor')->name('updateReceptor');
  Route::get('showDocument','documentos@showDocument')->name('showDocument');
  Route::get('lista','documentos@listDocument')->name('lista');
  Route::get('showTraslados','documentos@showTraslados')->name('showTraslados'); 
  Route::put('Trasladar','documentos@documentTransfer')->name('Trasladar');
  Route::get('getMessage','documentos@getRecepcion')->name('getMessage');
  Route::get('getRecepcionMessage','documentos@getRecepcionMessage')->name('getRecepcionMessage');
  Route::get('recibido','documentos@showRecibido')->name('recibido');
  Route::put('Aceptar','documentos@toAccept')->name('Aceptar');  
  Route::post('filterReceptores','documentos@getReceptoresbyId')->name('filterDocumentos');
  Route::put('retornar','documentos@retornar')->name('retornar');
  Route::get('traslados','documentos@listDocumentTransfert')->name('traslados');
  Route::get('usuarios','documentos@getUsersTransfer')->name('usuarios');
  Route::put('TrasladoInterno','documentos@setTransferInt')->name('TrasladoInterno');
  Route::get('bitacora','documentos@showBitacora')->name('bitacora');
  Route::post('bitacoraDocumento','documentos@bitacoraDocument')->name('bitacoraDocumento');
  Route::get('download','documentos@downloadPDF')->name('downloadPDF');
  Route::get('previewPDF','documentos@previewPDF')->name('previewPDF');
  Route::get('getProfesiones','documentos@getProfesiones')->name('getProfesiones');
  Route::post('infoPDF','documentos@getDataPDF')->name('infoPDF');
  Route::post('getComentario','documentos@getComentario')->name('getComentario');
  Route::post('setComentario','documentos@setComentario')->name('setComentario'); 
  Route::put('closeDocumento','documentos@closeDocumento');


  /********************************************* */

  /**Nuevo */

  Route::get('getRecepcion','recepcionController@recepcion')->name('recepcion'); 
  Route::post('storeDocumento','recepcionController@storeRecepcion')->name('Almacenar');
  Route::get('listDocumentAll','documentos@listDocumentAll');

  Route::post('upload','Upload@store');
  Route::post('Uploadfile','Upload@uploadfiles');
  Route::post('getNameFiles','Upload@getNameFiles');

  Route::get('correlativoN','recepcionController@getCorrelativoDocumento');
 


  /************************************************ */