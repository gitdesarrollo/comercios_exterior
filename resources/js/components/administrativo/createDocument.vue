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
                <el-col :xs="25" :sm="12" :md="12" :lg="20" :xl="12">
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
                        <el-select v-model="form.copia_unidad" multiple  class="select_width" clearable filterable placeholder="Seleccione Copias">
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
                        <el-select v-model="form.receptor_copia" multiple class="select_width" clearable filterable placeholder="Seleccione Receptor">
                            <el-option
                                v-for="item in list_response.list_receptor"
                                :key="item.id"
                                :label="item.descripcion"
                                :value="item.id"
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
                        <el-input type="textarea" v-model="form.cuerpo" :rows="15" placeholder="Cuerpo del documento">
                        </el-input>
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
export default {
    props: ['id_departamento'],
    data() {
        return {
            form: {
                dirigido: "",
                direccion:"",
                copia_unidad:[],
                receptor_copia:[],
                cuerpo:"",
                correlativo: "",
            },
            url_data: {
                dependencias: "dependencias",
                setDocumentos: "setDocumentos",
                getReceptor: 'getReceptores',
            },
            list_response: {
                list_dependencia:[],
                list_receptor: [],
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
                ]
            },
            fullscreenLoading: false,
        };
    },
    mounted() {
        this.getDependencia();
        this.getReceptor();
    },
    methods: {
        getDependencia() {
            axios.get(this.url_data.dependencias)
            .then(response => {
                this.list_response.list_dependencia = response.data;
            })
        },
        getReceptor() {
            axios.get(this.url_data.getReceptor)
            .then(response => {
                this.list_response.list_receptor = response.data;
            })
        },
        onSubmit(form){
            const h = this.$createElement;
            this.$refs[form].validate(valid => {
                if(valid){
                    this.fullscreenLoading = true;
                    axios.post(this.url_data.setDocumentos,{
                        dirigido: this.form.dirigido,
                        destinatario: this.form.destinatario,
                        copia: this.form.copia_unidad,
                        receptor: this.form.receptor_copia,
                        correlativo: this.form.correlativo,
                        cuerpo: this.form.cuerpo,
                        departamento: this.id_departamento
                    }).then(response => {
                        const status = JSON.parse(response.status);
                        if(status == "200"){
                        console.log(status);
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