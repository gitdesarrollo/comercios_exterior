<template>
  <div>
    <div class="card">
      <div class="card-header text-white bg-primary">Creación de documentos</div>
      <div class="card-body">
          <el-form ref="form" :model="form" :rules="rules" label-width="20px">
              <el-row :gutter="10" class="mt-2">
                <el-col :xs="25" :sm="12" :md="12" :lg="20" :xl="12">
                    <el-form-item prop="dirigido">
                        <el-input v-model="form.dirigido">
                            <template slot="prepend"> 
                                Dirigido: 
                            </template>
                        </el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs="25" :sm="12" :md="12" :lg="20" :xl="6">
                    <el-form-item prop="direccion">
                        <el-select v-model="form.profesion"   class="select_width" clearable filterable placeholder="Seleccione Profesion">
                            <el-option
                                v-for="item in list_response.listProfesiones"
                                :key="item.id"
                                :label="item.descripcion"
                                :value="item.id"
                                >
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :xs="25" :sm="12" :md="12" :lg="20" :xl="6">
                    <el-form-item prop="direccion">
                        <el-input v-model="form.direccion">
                            <template slot="prepend">
                                Dirección: 
                            </template>
                        </el-input>
                    </el-form-item>
                </el-col>
              </el-row>
              <el-row :gutter="10" class="mt-2">
                <el-col :xs="25" :sm="7" :md="8" :lg="20" :xl="9">
                    <el-form-item prop="copia_unidad">
                        <el-select v-model="form.copia_unidad" multiple  class="select_width" clearable filterable placeholder="Seleccione Copias" @change="receptores" @remove-tag="removeReceptor" @clear="removeReceptor">
                            <el-option
                                v-for="item in list_response.list_dependencia"
                                :key="item.id_dependencia"
                                :label="item.descripcion"
                                :value="item.id_dependencia"
                                >
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :xs="25" :sm="7" :md="8" :lg="20" :xl="9">
                    <el-form-item prop="receptor_copia">
                        <el-select v-model="form.receptor_copia" multiple class="select_width" clearable filterable placeholder="Seleccione Receptor" >
                            <el-option
                                v-for="item in list_response.list_receptor"
                                :key="item.code"
                                :label="item.descripcion"
                                :value="item.code"
                                >
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :xs="25" :sm="9" :md="8" :lg="20" :xl="6">
                    <el-form-item prop="correlativo">
                      <el-input v-model="form.correlativo">
                            <template slot="prepend">
                                Correlativo: 
                            </template>
                        </el-input>
                    </el-form-item>
                </el-col>
              </el-row>
              <el-row :gutter="20" class="mt-2">
                <el-col :xs="25" :sm="24" :md="24" :lg="20" :xl="24">
                    <el-form-item prop="cuerpo">
                        <ckeditor :editor="editor" v-model="form.cuerpo" :config="editorConfig"></ckeditor>
                        <!-- <el-input type="textarea" maxlength="2000" show-word-limit v-model="form.cuerpo" :rows="15" placeholder="Cuerpo del documento"> -->
                        <!-- </el-input> -->
                    </el-form-item>
                </el-col>
              </el-row>
                <el-form-item>
                    <el-button
                    type="primary"
                    @click="onSubmit('form')"
                    v-loading.fullscreen.lock="fullscreenLoading"
                    >Guardar</el-button>
                </el-form-item>
          </el-form>
      </div>
    </div>
  </div>
</template>

<script>
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    // import EssentialsPlugin from '@ckeditor/ckeditor5-essentials/src/essentials';
    // import BoldPlugin from '@ckeditor/ckeditor5-basic-styles/src/bold';
    // import ItalicPlugin from '@ckeditor/ckeditor5-basic-styles/src/italic';
    // import LinkPlugin from '@ckeditor/ckeditor5-link/src/link';
    // import ParagraphPlugin from '@ckeditor/ckeditor5-paragraph/src/paragraph';

export default {
    props: ['id_departamento'],
    data() {
        return {
                editor: ClassicEditor,
                editorData: '',
                editorConfig: {
                    // toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote',  ]
                },
            text:"hola",
            form: {
                dirigido: "", 
                direccion:"",
                copia_unidad:[],
                receptor_copia:[],
                cuerpo:"",
                correlativo: "",
                profesion: "",
            },
            url_data: {
                dependencias: "dependencias",
                setDocumentos: "setDocumentos",
                getReceptor: 'getReceptores',
                filter: 'filterReceptores',
                profesiones: 'getProfesiones',
            },
            list_response: {
                list_dependencia:[],
                list_receptor: [],
                listProfesiones: [],
            },
            rules: {
                dirigido: [
                    {
                        required: true,
                        message: "Ingrese a quien va dirigido",
                        trigger: "blur"
                    }
                ],
                direccion: [
                    {
                        required: true,
                        message: "Ingrese destinatario",
                        trigger: "blur"
                    }
                ],
                cuerpo: [
                    {
                        required: true,
                        message: "Ingrese el cuerpo del mensaje",
                        trigger: "blur"
                    }
                ],
                profesion: [
                    {
                        required: true,
                        message: "Ingrese la profesion",
                        trigger: "blur"
                    }
                ]
            },
            fullscreenLoading: false,
        };
    },
    mounted() {
        this.getDependencia();
        this.Profesiones();
        // this.getReceptor();
    },
    methods: {
        Profesiones(){
            axios.get(this.url_data.profesiones).then(response => {
                this.list_response.listProfesiones = response.data;
            })
        },
        receptores(dato){
            axios.post(this.url_data.filter,{
                direcciones: dato
            }).then(response => {
                // console.log(response.data);
                this.list_response.list_receptor = response.data;
            })
        },
        jsonReceptor(data) {
            // console.log(this.list_response.list_receptor);
            console.log(data);
        },
        removeReceptor(dato){
            console.log(dato);
            this.form.receptor_copia = "";
        },
        getDependencia() {
            axios.get(this.url_data.dependencias)
            .then(response => {
                this.list_response.list_dependencia = response.data;
            })
        },
        // getReceptor() {
        //     axios.get(this.url_data.getReceptor)
        //     .then(response => {
        //         this.list_response.list_receptor = response.data;
        //     })
        // },
        onSubmit(form){
            const h = this.$createElement;
            this.$refs[form].validate(valid => {
                if(valid){
                    this.fullscreenLoading = true;
                    axios.post(this.url_data.setDocumentos,{
                        dirigido: this.form.dirigido,
                        destinatario: this.form.direccion,
                        // copia: this.form.copia_unidad,
                        receptor: this.form.receptor_copia,
                        correlativo: this.form.correlativo,
                        cuerpo: this.form.cuerpo,
                        departamento: this.id_departamento,
                        profesion: this.form.profesion,
                    }).then(response => {
                        const status = JSON.parse(response.status);
                        if(status == "200"){
                        // console.log(status);
                            if(response.data){
                                this.$message({
                                    message: h("p", null, [
                                        h("i", { style: "color: teal" }, "Agregado!")
                                    ]),
                                    type: "success"
                                });
                                this.fullscreenLoading = false;
                                this.$refs[form].resetFields();
                                // this.form.dirigido = "";
                                // this.form.direccion = "";
                                // this.form.copia_unidad = [];
                                // this.form.receptor_copia = [];
                                // this.form.cuerpo = "";
                                // this.form.correlativo = "";

                            }else{
                                this.$message.error({
                                    message: h("p", null, [
                                    h("i", { style: "color: red" }, "debe de seleccionar la misma cantidad de destinatarios")
                                    ])
                                });
                            }

                        }else{
                            this.$message.error({
                                message: h("p", null, [
                                h("i", { style: "color: red" }, "error en el servidor")
                                ])
                            });

                        }
                        console.log(response.data);
                    })
                }
            })
        }
    },
}
</script>