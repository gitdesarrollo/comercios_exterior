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
import VueBarcodeScanner from 'vue-barcode-scanner'
Vue.use(ElementUI,{locale});
Vue.use(VueBarcodeScanner);

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

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
