<template>
    <div>
        
        <div class="card" >
            <div class="card-header text-white bg-primary">
                Sistema de SICOIN
            </div>
            <div class="card-body">
                <el-form 
                ref="form"
                :model="form"
                :rules="rules"
                label-width="150px"
                onSubmit={form.onSubmit}
                >
                    <el-form-item label="SICOIN" prop="name">
                        <el-input v-model="form.name" autofocus ref="autoInput" @keyup.enter.native.prevent="enter_onSubmit()" ></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" icon="el-icon-search" @click="onSubmit('form')" ></el-button>
                        <el-button @click="reset">Cancel</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div v-show="showCard" v-loading="loading" element-loading-text="Loading..."  element-loading-spinner="el-icon-loading"  element-loading-background="rgba(0, 0, 0, 0.8)" >
            <el-row>
                <el-col :span="24" >
                    <el-card :body-style="{ padding: '0px' }">
                    
                    <div style="padding: 14px;">
                        <span class="font-weight-bold text-uppercase">Información del Bien</span>
                        <div class="bottom clearfix">
                            <div v-for="(items, i) in listProduct" :key="i">
                                <el-row :gutter="10" class="mt-3">
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="5" class="text-right">
                                        <span class="font-weight-bold">Código Sicoin:</span>
                                    </el-col>
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="7">
                                            {{ items.codigo_sicoin}}
                                    </el-col>
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="5" class="text-right">
                                        <span class="font-weight-bold">Sistema:</span>
                                    </el-col>
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="7">
                                            {{ items.cantidad}} Unidad
                                    </el-col>
                                </el-row>

                                <el-row :gutter="10" class="mt-2">
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="12">
                                        <span class="font-weight-bold">Bien:</span>
                                    </el-col>
                                </el-row>
                                <el-row :gutter="10" class="mt-2 mb-4">
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24" class="text-justify font-weight-light">
                                            {{ items.descripcion}}
                                    </el-col>
                                </el-row>

                                <el-form   ref="formInventory" :model="formInventory" :rules="rulesinv" label-width="120px"> 
                                    <el-form-item label="Físico:" prop="fisico">
                                        <el-input class="font_custom_input" v-model="formInventory.fisico" ref="form_fisico" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="Lugar:" prop="lugar">
                                        <el-input class="font_custom_input" v-model="formInventory.lugar"></el-input>
                                    </el-form-item>
                                    <el-form-item label="Empleado:" prop="empleado">
                                        <el-input class="font_custom_input" v-model="formInventory.empleado"></el-input>
                                        <!-- <el-input class="font_custom_input" v-model="formInventory.empleado" @change="searchProvider"></el-input> -->
                                    </el-form-item>
                                    <el-row :gutter="10" v-show="visible">
                                        <el-col :span="24">
                                            <el-table :data="dataSatProvider" style="width: 100%">
                                                <el-table-column label="Nit" prop="nit"></el-table-column>
                                                <el-table-column label="Nombre" prop="name"></el-table-column>
                                                <el-table-column label="Dirección" prop="business_address"></el-table-column>
                                            </el-table>
                                        </el-col>
                                    </el-row>
                                    <el-form-item>
                                        <el-button type="primary" @click.prevent="updateInventory('formInventory')">Guardar</el-button>
                                        <el-button @click="reset">Cancel</el-button>
                                    </el-form-item>
                                </el-form>
                            </div>
                        </div>
                    </div>
                    </el-card>
                </el-col>
            </el-row>
        </div>
    </div>
</template>

<style >
    .title_bien{
        font-style: bold !important;
        
    }
    /* .cardCenter{
        top: 50%;
        left: 50%;
        width: 600px;  
         margin-top: -200px;
        margin-left: 30%;
} */

</style>
<script>
export default {
    data() {
        return {
            form: {
                name: "",
            },
            visible: false,
            formInventory: {
                fisico: "",
                lugar: "",
                empleado: "",
            },
            id_activo_data:"",
            
            showCard: false,
            listProduct: [],
            rules: {
                name: [
                    {
                        required: true,
                        message: "Este campo no puede ser vacio",
                        trigger: "blur"
                    }
                ],
            },
            rulesinv: {
                fisico: [
                    {
                        required: true,
                        message: "Este campo no puede ser vacio",
                        trigger: "blur"
                    }
                ],
                lugar: [
                    {
                        required: true,
                        message: "Este campo no puede ser vacio",
                        trigger: "blur"
                    }
                ],
                empleado: [
                    {
                        required: true,
                        message: "Este campo no puede ser vacio",
                        trigger: "blur"
                    }
                ],
            },
            fullscreenLoading: false,
            dataSatProvider: [],
            urlData: {
                    searchCode: "searchCode/",
                    setCountInventory: "setCountInventory",
                    urlSat: "http://gestorquejas.diaco.gob.gt/Consulta/rs/proveedores/empresa?nit=",
                    
                },
            loading: false
        }
    },
    mounted() {
        this.enterbloqueado();
    },
    methods: {

        searchProvider (){
              this.visible = true;
              this.nit = this.formInventory.empleado;
              this.getProviderSat();
        },
        getProviderSat(){
            const config = { headers: {'Content-Type': 'application/json'} };
            axios.get(this.urlData.urlSat+this.nit,config).then(response => {
                this.dataSatProvider = [];
                console.log(this.nit)
                this.dataSatProvider.push({
                    nit: response.data.value.nitProveedor,
                    name: response.data.value.nombre,
                    business_address: response.data.value.direccion
                });
            })
        },
        enterbloqueado(){
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('input[type=text]').forEach( node => node.addEventListener('keypress', e => {
                if(e.keyCode == 13) {
                    e.preventDefault();
                    
                }
            }))
            });
        },
        enter_onSubmit(){
            this.onSubmit('form');
        },
        reset() {
            this.form.name = "";
            this.formInventory.fisico = "";
            this.formInventory.lugar = "";
            this.formInventory.empleado = "";
            this.listProduct = [];
            this.showCard = false;
            this.$refs.autoInput.$el.getElementsByTagName('input')[0].focus();
            

        },
        updateInventory(form) {
            
            const h = this.$createElement;
            this.$refs[form][0].validate(valid => {
                if (valid) {
                    this.showCard = true;
                    this.loading = true;
                    axios
                        .post(this.urlData.setCountInventory,{
                            id_activo: this.id_activo_data,
                            fisico: this.formInventory.fisico,
                            lugar: this.formInventory.lugar,
                            empleado: this.formInventory.empleado
                        }
                        )
                        .then(response => {
                            const status = JSON.parse(response.status); 
                            
                            if (status == "200") {
                                this.$message({
                                    message: h("p", null, [
                                        h(
                                            "i",
                                            { style: "color: teal" },
                                            "Operación realizada con exito!"
                                        )
                                    ]),
                                    type: "success"
                                });
                                this.reset();
                                
                                
                            }
                        });
                } else {
                    this.$message.error({
                        message: h("p", null, [
                            h(
                                "i",
                                { style: "color: red" },
                                "Complete datos" 
                            )
                        ])
                    });
                    return false;
                }
                });
            
        },

        enter(form,event){
            event.preventDefault();
            this.onSubmit(form,event);
        },
        onSubmit(form) {
           
            const h = this.$createElement;
            this.$refs[form].validate(valid => {
                if (valid) {
                    this.showCard = true;
                    this.loading = true;
                    axios
                        .get(this.urlData.searchCode+this.form.name)
                        .then(response => {
                            if(response.data.length == 0){
                                this.loading = false;   
                                this.showCard = false;
                                this.$notify({
                                    title: 'Warning',
                                    message: 'No se encuentra información para el código',
                                    type: 'warning'
                                }); 
                            }else{
                                const status = JSON.parse(response.status); 
                                this.listProduct = response.data;
                                if (status == "200") {
                                    this.id_activo_data = response.data[0].id_activo;
                                    this.loading = false;                               
                                }

                            }
                            
                        });
                } else {
                    this.$message.error({
                        message: h("p", null, [
                            h(
                                "i",
                                { style: "color: red" },
                                "Complete datos" 
                            )
                        ])
                    });
                    return false;
                }
            });
        },
    }
}
</script>