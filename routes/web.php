<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route; 

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

  Route::get('Inicial','InventarioInicial@SetCategory');
  Route::get('inventarioinicial1','initialCharge@setDataDB_01');
  Route::get('inventarioinicial2','initialCharge@setDataDB_02');
  Route::get('inventarioinicial3','initialCharge@setDataDB_03');
  Route::get('inventarioinicial4','initialCharge@setDataDB_04');
  Route::get('inventarioinicial5','initialCharge@setDataDB_05');
  Route::get('inventarioinicial6','initialCharge@setDataDB_06');
  Route::get('inventarioinicial7','initialCharge@setDataDB_07');
  Route::get('inventarioinicial8','initialCharge@setDataDB_08');
  Route::get('getAccountInitial','inventario@getAccountInitial');
  /********************* */

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