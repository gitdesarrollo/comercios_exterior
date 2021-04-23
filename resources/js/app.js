/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('jquery');
require('popper.js');
require('./bootstrap');
require('pace-progress');
require('perfect-scrollbar');
require('@coreui/coreui');



window.Vue = require('vue');

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/es';
import VueBarcodeScanner from 'vue-barcode-scanner';
// import CKEditor from '@ckeditor/ckeditor5-vue';
import CKEditor from 'ckeditor4-vue';
// import Datatable from 'vue2-datatable-component'
import { SimpleTimelinePlugin } from 'simple-vue-timeline';
import { Vue } from 'vue-property-decorator';






Vue.use(ElementUI,{locale});
Vue.use(VueBarcodeScanner);

Vue.use( CKEditor );
Vue.use(SimpleTimelinePlugin);


// Vue.use(Datatable);



/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('sidebar-component', require('./components/sidebar/sidebar.vue').default);
Vue.component('product-component', require('./components/product/formProduct.vue').default);
Vue.component('active-component', require('./components/active/formActive.vue').default);
Vue.component('scanner-component', require('./components/Scanner/scanner.vue').default);
Vue.component('printreport-component', require('./components/Scanner/report.vue').default);
Vue.component('checkinventory-component', require('./components/Scanner/scannerSearch.vue').default);
Vue.component('reportinventory-component', require('./components/Scanner/reportInventory.vue').default);
Vue.component('index-component', require('./components/tablero/index.vue').default);


Vue.component('unidad-component', require('./components/admin/unidades.vue').default);
Vue.component('entidad-component', require('./components/admin/entidades.vue').default);
Vue.component('usuario-component', require('./components/admin/usuarios.vue').default);
Vue.component('visualizar-component', require('./components/admin/visualizar.vue').default);
Vue.component('roles-component', require('./components/admin/roles.vue').default);



/* comercio exterior */
Vue.component('cabecera-component', require('./components/cabecera/cabecera_header.vue').default);
Vue.component('documento-component', require('./components/administrativo/createDocument.vue').default);
Vue.component('receptor-component', require('./components/administrativo/createReceptores.vue').default);
Vue.component('listado-component', require('./components/administrativo/listadoDocument.vue').default);
Vue.component('mensaje-component', require('./components/cabecera/alertas.vue').default);
Vue.component('recibos-component', require('./components/administrativo/listadoRecibido.vue').default);
Vue.component('traslados-component', require('./components/administrativo/listadoTraslados.vue').default);
Vue.component('bitacora-component', require('./components/administrativo/bitacora.vue').default);
Vue.component('download-component', require('./components/administrativo/download.vue').default);
/* ******************** */

Vue.component('recepcion-component', require('./components/administrativo/recepcion.vue').default);
Vue.component('settings-component', require('./components/settings/settings.vue').default);

Vue.component('ingresos-component', require('./components/modules/misIngresos/ingresos.vue').default);
Vue.component('delegados-component', require('./components/modules/delegado/delegados.vue').default);
Vue.component('seguimiento-component', require('./components/modules/seguimientos/seguimiento.vue').default);
Vue.component('views-component', require('./components/admin/views.vue').default);
Vue.component('permits-component', require('./components/admin/permits.vue').default);
Vue.component('expedientes', require('./components/modules/expedientes/expedientes.vue').default);
Vue.component('remitente', require('./components/modules/remitente/remitente.vue').default);
Vue.component('visualizador-pdf', require('./components/modules/visualizadorPdf/visualizador.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
