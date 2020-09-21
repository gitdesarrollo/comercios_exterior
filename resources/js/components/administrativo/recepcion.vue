<template>
  <div>
    <div class="card">
      <div class="card-header text-white bg-primary">Recepción de Documentos</div>
      <div class="card-body">
        <el-form ref="form" :model="form" :rules="rules">
          <el-row :gutter="10" class="mt-2">
            <el-col :xs="24" :sm="24" :md="12" :lg="24" :xl="12">
              <el-form-item prop="interesado">
                <el-input v-model="form.interesado">
                  <template slot="prepend">
                    <span class="requiredColor mr-1">*</span> Interesado:
                  </template>
                </el-input>
              </el-form-item>
            </el-col>
            <el-col :xs="24" :sm="24" :md="12" :lg="24" :xl="12">
              <el-form-item prop="expediente">
                <el-input v-model="form.expediente">
                  <template slot="prepend">
                    <span class="requiredColor mr-1">*</span> No. Expediente:
                  </template>
                </el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="10" class="mt-2">
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
              <el-form-item prop="asunto" label="Dirigido:">
                <el-select
                  v-model="form.persona"
                  class="select_width"
                  clearable
                  filterable
                  placeholder="Seleccione usuario"
                >
                  <el-option
                    v-for="items in handler_response.getUsuarios"
                    :key="items.id"
                    :label="items.name"
                    :value="items.id"
                  ></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="10" class="mt-2">
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
              <el-form-item prop="asunto" label="Asunto:">
                <el-input
                  v-model="form.asunto"
                  type="textarea"
                  :rows="10"
                  placeholder="Ingrese el Asunto"
                  maxlength="2000"
                  show-word-limit
                ></el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-form-item>
            <el-button
              type="primary"
              @click="onSubmit('form')"
              v-loading.fullscreen.lock="handler_loader.loaderOnSubmit.loader"
            >Guardar</el-button>
            <el-button @click="resetForm('form')">Cancelar</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
</template>

<style lang="css">
.requiredColor {
  color: red;
}
</style>

<script>
export default {
  data() {
    return {
      form: {
        interesado: "",
        expediente: "",
        asunto: "",
        persona: "",
      },
      rules: {
        interesado: [
          {
            required: true,
            message: "Ingrese un Interesado",
            trigger: "blur",
          },
        ],
        expediente: [
          {
            required: true,
            message: "Ingrese un Expediente",
            trigger: "blur",
          },
        ],
        asunto: [
          {
            required: true,
            message: "Ingrese un asunto",
            trigger: "blur",
          },
        ],
        persona: [
          {
            required: true,
            message: "Seleccione una Persona",
            trigger: "blur",
          },
        ],
      },
      handler_loader: {
        loaderOnSubmit: {
          loader: false,
        },
      },
      handler_url: {
        getUsuarios: "usuarios",
        storeDocumento: "storeDocumento",
      },
      handler_response: {
        getUsuarios: [],
        
      },
    };
  },
  mounted() {
      this.getUserTransfer();
  },
  methods: {
    onSubmit(form) {
        const h = this.$createElement;
        this.$refs[form].validate(valid => {
            if(valid){
                this.handler_loader.loaderOnSubmit.loader = true;
                axios.post(this.handler_url.storeDocumento,{
                    interesado: this.form.interesado,
                    correlativo: this.form.expediente,
                    descripcion: this.form.asunto,
                    usuario: this.form.persona
                }).then(response => {
                    const status = JSON.parse(response.status);
                    if(status == "200" && response.data != false){
                        this.$message({

                            message: h("p", null, [
                                h("i", { style: "color: teal" }, "Agregado!")
                            ]),
                            type: "success"  
                        });
                        this.handler_loader.loaderOnSubmit.loader = false;
                        this.resetForm(form);
                    }
                })
            }
        })
    },
    resetForm(form) {
      this.$refs[form].resetFields();
    },
    getUserTransfer() {
      axios.get(this.handler_url.getUsuarios).then((response) => {
        this.handler_response.getUsuarios = response.data;
      });
    },
  },
};
</script>