<template >
    <div>

        <div class="card">
            <div class="card-header text-white bg-primary">Subir archivos</div>
            <div class="card-body">
                <el-form ref="form" :model="form" :rules="rules" label-width="150px">
                    <el-form-item label="Unidad Ejecutora" prop="unidad">
                        <el-select v-model="form.unidad" class="select_width" clearable filterable placeholder="Seleccionar">
                            <el-option
                                v-for="item in list_response.list_unidad"
                                :key="item.id_unidad"
                                :label="item.unidad"
                                :value="item.id_unidad"
                                >
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Cuenta:" prop="cuenta_nueva">
                        <el-select class="select_width" v-model="form.cuenta_nueva" clearable filterable placeholder="Seleccionar">
                            <el-option
                                v-for="item in list_response.list_cuenta"
                                :key="item.id_cuenta"
                                :label="item.descripcion"
                                :value="item.id_cuenta" 
                                >
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-row :gutter="10">
                        <el-col :xs="25" :sm="6" :md="8" :lg="12" :xl="6">
                            <el-form-item>
                                <el-upload
                                ref="upload_excel"
                                class="upload-demo"
                                action=""
                                :on-change="handleChange"
                                :on-remove="handleRemove"
                                :on-exceed="handleExceed"
                                :limit="limitUpload"
                                accept="application/vnd.openxmlformats-    
                                officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                                :auto-upload="false">
                                    <el-button  class="drop">Buscar Archivo</el-button>
                                </el-upload>
                            </el-form-item>
                        </el-col>
                        <el-col :xs="25" :sm="6" :md="8" :lg="12" :xl="5">
                            <el-form-item>
                                <el-button type="success" class="drop" plain :disabled="submit_button"  @click="onSubmit('form')" >Guardar</el-button>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-form>
            </div>
        </div>


        

        <el-main>
          <el-table :data="da">
            <el-table-column prop="fecha" label="FECHA" width="100">
            </el-table-column>
            <el-table-column prop="bien" label="BIEN" width="100">
            </el-table-column>
            <el-table-column prop="descripcion" label="DESCRIPCION">
            </el-table-column>
            <el-table-column prop="cantidad" label="CANTIDAD" width="100">
            </el-table-column>
            <el-table-column prop="costo" label="COSTO" width="100">
            </el-table-column>
          </el-table>
        </el-main>
    </div>
</template>


<style >
    .drop{
        border: 2px dashed #bbb;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        padding: 25px;
        text-align: center;
        font: 20pt bold,"Vollkorn";
        color: #bbb;
    }
    .upload-demo{
        width: 100%;
    }
</style>
<script>
    import XLSX from 'xlsx'
export default {
    data() {
        return {
            tickets:[{name:"test"}],
            fullscreenLoading: false,
            headers:[],
            limitUpload:1,
            fileTemp:null,
            file:null,
            da:[],
            submit_button:true,
            dalen:0,
            form: {
                unidad:'',
                cuenta_nueva: '',
            },
            list_response: {
                list_unidad: [],
                list_cuenta: [],
                list_excel: [],
            },
            rules: {
                unidad: [
                    {
                        required: true,
                        message: "Seleccione unidad",
                        trigger: "blur"
                    }
                ],
                cuenta_nueva: [
                    {
                        required: true,
                        message: "Seleccione cuenta nueva",
                        trigger: "blur"
                    }
                ],
            },
            urlData: {
                    urlEntity: "entidades",
                    urlSat: "http://gestorquejas.diaco.gob.gt/Consulta/rs/proveedores/empresa?nit=",
                    barCode: "barCode",
                    unidades: "unidades",
                    grupos: "grupos",
                    categorias: "categorias",
                    secciones: "secciones",
                    tipos: "tipos",
                    Bienes: "Bienes",
                    EstadosProducto: "EstadosProducto",
                    addProductoBien: 'addproductobien',
                    getPersonas: 'PersonasEntidad',
                    dependencias: "dependencias",
                    cuentas: "cuentas",
                    respaldos: "respaldos",
                    secuenciasFac: "secuenciasFac",
                    addActives: "active",
                    setExcel: "setDataExcel",
                    
                },
        }
    },
    mounted() {
        this.getUnidad();
        this.getCuentas();
    },
    methods: {
        //Processing Method for Uploading Files  
        onSubmit(form){
            const h = this.$createElement;
                this.$refs[form].validate(valid => {                    
                        if (valid) {
                            //loading
                            const loading = this.$loading({
                                lock: true,
                                text: 'Guardando Datos',
                                spinner: 'el-icon-loading',
                                background: 'rgba(0,0,0,0.7'
                            })


                            this.fullscreenLoading = true;
                            axios.post(this.urlData.setExcel,{
                                UE: this.form.unidad,
                                CUENTA: this.form.cuenta_nueva,
                                data_excel: this.list_response.list_excel
                            }).then(response =>{
                                const status = JSON.parse(response.status);
                                if (status == "200") {
                                    this.$message({
                                        message: h("p", null, [
                                            h("i", {style: "color: teal"}, "Cambio realizado con exito!")
                                        ]),
                                        type: "success"
                                    });
                                    this.form.unidad = "";
                                    this.form.cuenta_nueva = "";
                                    // this.fullscreenLoading = false;
                                    this.$refs.upload_excel.clearFiles()
                                    loading.close()
                                    this.da = []
                                    this.list_response.list_excel = []
                                }
                            })
                        }
                })
        },
        getUnidad() {
            axios.get(this.urlData.unidades)
                .then(response => {
                    this.list_response.list_unidad = response.data;
                    
                })
        },
        getCuentas() {
            axios.get(this.urlData.cuentas)
                .then(response => {
                    this.list_response.list_cuenta = response.data;
                })
        },
        handleChange(file, fileList){
            this.fileTemp = file.raw;
            if(this.fileTemp){
                if((this.fileTemp.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') 
                    || (this.fileTemp.type == 'application/vnd.ms-excel')){
                    this.importfxx(this.fileTemp);
                } else {
                    this.$message({
                        type:'warning',
                        message:'Attachment format error, please delete and upload again!'
                    })
                }
            } else {
                this.$message({
                    type:'warning',
                    message:'Please upload the attachment!'
                })
            }
        },
        //Processing method when exceeding the maximum number of uploaded files
        handleExceed(){
            this.$message({
                type:'warning',
                message:'Exceed the maximum number of uploaded files limit!'
            })
            return;
        },
        //How to remove files
        handleRemove(file,fileList){
            this.fileTemp = null
        },

        importfxx(obj) {
            let _this = this;
            let inputDOM = this.$refs.inputer;
            // Retrieving file data through DOM

            this.file = event.currentTarget.files[0];

            var rABS = false; //Read the file as a binary string
            var f = this.file;

            var reader = new FileReader();
            //if (!FileReader.prototype.readAsBinaryString) {
            FileReader.prototype.readAsBinaryString = function(f) {
                var binary = "";
                var rABS = false; //Read the file as a binary string
                var pt = this;
                var wb; //Read completed data
                var outdata;
                var reader = new FileReader();
                reader.onload = function(e) {
                    var bytes = new Uint8Array(reader.result);
                    var length = bytes.byteLength;
                    for (var i = 0; i < length; i++) {
                        binary += String.fromCharCode(bytes[i]);
                    }
                    //If not introduced in main.js, you need to introduce it here to parse excel
                    // var XLSX = require("xlsx");
                    if (rABS) {
                        wb = XLSX.read(btoa(fixdata(binary)), {
                        //Manual conversion
                        type: "base64"
                        });
                    } else {
                        wb = XLSX.read(binary, {
                        type: "binary"
                        });
                    }
                    outdata = XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]]); 
                    // outdata son los datos del documento
                    
                    
                    //outdata is read data (without header rows or headers, the header will be the subscript of the object)
                    //Data can be processed here.
                    let arr = [];
                    outdata.map(v => {
                        let obj = {}
                        // obj.ue = v['UE']
                        // obj.cuenta = v['CUENTA']
                        obj.fecha = v['FECHA']
                        obj.bien = v['BIEN']
                        obj.descripcion = v['DESCRIPCION']
                        obj.cantidad = v['CANTIDAD']
                        obj.costo = v['COSTO']
                        obj.alza = v['COSTO']
                        arr.push(obj)
                    });
                    _this.da=arr;
                    _this.dalen=arr.length;
                    _this.list_response.list_excel = arr;
                    _this.submit_button = false;
                    return arr;
                };
                reader.readAsArrayBuffer(f);
            };
            if (rABS) {
                reader.readAsArrayBuffer(f);
            } else {
                reader.readAsBinaryString(f);
            }
        }

    }
}
</script>