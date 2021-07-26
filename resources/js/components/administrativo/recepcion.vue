<template>
<div>
  <div class="card">
    <div class="card-header text-white bg-primary">Recepción de Documentos</div>
    <div class="card-body">
      <el-form ref="form" :model="form" :rules="rules">
        <el-row :gutter="10">
          <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
            <el-form-item label="Remitente:" prop="interesado">
              <el-select v-model="form.interesado" class="select_width" clearable filterable placeholder="Seleccione Remitente" popper-class="item_data">
                <el-option v-for="items in handler_response.getRemitente" :key="items.id" :label="items.descripcion" :value="items.descripcion"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :xs="24" :sm="24" :md="12" :lg="24" :xl="8">
            <el-form-item prop="expediente" label="No. Expediente:">
              <el-input v-model="form.expediente" @change="checkNumber">
              </el-input>
            </el-form-item>
          </el-col>
          <el-col :xs="24" :sm="24" :md="12" :lg="24" :xl="4">
            <el-form-item prop="folios" label="Folios:">
              <el-input v-model="form.folios">
              </el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :xs="24" :sm="24" :md="12" :lg="24" :xl="12">
            <el-form-item prop="tipo" label="Tipo de Documento:">
              <el-select v-model="form.tipo" class="select_width" clearable filterable placeholder="Seleccione usuario" popper-class="item_data">
                <el-option v-for="items in handler_response.getTypesList" :key="items.id" :label="items.descripcion" :value="items.id"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :xs="24" :sm="24" :md="12" :lg="24" :xl="6">
            <el-form-item prop="fecha">
              <span>Fecha de Documento:</span>
              <div class="mt-2">
                <el-date-picker v-model="form.fecha" type="date" class="select_width" placeholder="Seleccione Fecha" format="dd-MM-yyyy" value-format="yyyy-MM-dd">
                </el-date-picker>
              </div>
            </el-form-item>
          </el-col>
          <el-col :xs="24" :sm="24" :md="12" :lg="24" :xl="6">
            <el-form-item prop="recepcion">
              <span>Fecha de Recepción:</span>
              <div class="mt-2">
                <el-date-picker v-model="form.recepcion" type="date" class="select_width" placeholder="Seleccione Fecha" format="dd-MM-yyyy" value-format="yyyy-MM-dd">
                </el-date-picker>
              </div>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10" class="mt-2">
          <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
            <el-form-item prop="persona" label="Destinatario:">
              <el-select v-model="form.persona" class="select_width" clearable filterable placeholder="Seleccione usuario" popper-class="item_data">
                <el-option v-for="items in handler_response.getUsuarios" :key="items.id" :label="items.name" :value="items.id"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
            <el-form-item label="Cc:">
              <el-select v-model="form.cc" class="select_width" clearable filterable placeholder="Seleccione usuario" popper-class="item_data" multiple>
                <el-option v-for="items in handler_response.getUsuarios" :key="items.id" :label="items.name" :value="items.id"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10" class="mt-2">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <el-form-item prop="asunto" label="Asunto:">
              <el-input v-model="form.asunto" type="textarea" :rows="10" placeholder="Ingrese el Asunto" maxlength="2000" show-word-limit></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10" class="mt-2">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <el-form-item label="Cargar Archivo:">
              <el-alert title="No se a cargado un documento PDF" type="error" effect="dark" v-if="flagUpload" show-icon>
              </el-alert>
              <el-upload class="upload-demo" :auto-upload="false" ref="upload" :action="'/upload'" name="file[]" :on-change="handleChange" :data="form.upload" :headers="{ 'X-CSRF-TOKEN': csrf }" :on-preview="handlePreview" :on-remove="handleRemove" :on-success="cargaSuccess" :on-exceed="handleExceed" :file-list="fileList" :limit="limitNumber" accept=".pdf">
                <el-button size="small" type="success">Clic para subir archivo</el-button>
              </el-upload>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item>
          <el-button type="primary" @click="onSubmit('form')" v-loading.fullscreen.lock="handler_loader.loaderOnSubmit.loader">Guardar
          </el-button>
          <el-button @click="resetForm('form')">Cancelar</el-button>
        </el-form-item>
      </el-form>
      <!-- <el-dialog title="Recepción de documento" :visible.sync="centerDialogVisible" :close-on-click-modal="false" :close-on-press-escape="false" width="30%" center :show-close="false">
        <el-dialog width="30%" title="Adjuntar Documento" :visible.sync="innerVisible" :close-on-click-modal="false" append-to-body :before-close="CierreDialog">
          <el-upload class="upload-demo" :action="'/upload'" name="file[]" :data="{
                                id_documento: upload.idUpload,
                                correlativo: upload.correlativo,
                                count: upload.numberFiles,
                                type: upload.type
                                }" :headers="{ 'X-CSRF-TOKEN': csrf }" :on-preview="handlePreview" :on-remove="handleRemove" :on-success="cargaSuccess" :on-exceed="handleExceed" :file-list="fileList" :limit="limitNumber" accept=".pdf">
            <el-button size="small" type="primary">Clic para subir archivo</el-button>
          </el-upload>
        </el-dialog>
        <span slot="footer" class="dialog-footer">
          <el-button @click="innerVisible = true" icon="el-icon-paperclip" type="warning">Adjuntar</el-button>
        </span>
      </el-dialog> -->
    </div>
  </div>
</div>
</template>

<style lang="css">
.requiredColor {
  color: red;
}

.item_data {
  width: 30%;
}
</style>

<script>
export default {
  props: {
    csrf: {
      type: String
    }
  },
  data() {
    return {
      centerDialogVisible: false,
      innerVisible: false,
      limitNumber: 1,
      fileList: [],
      dialogImageUrl: '',
      dialogVisible: false,
      disabled: false,
      icono: "",
      upload: {
        idUpload: 100,
        correlativo: "100",
        numberFiles: 0,
        type: "pdf",

      },
      flagUpload: false,
      form: {
        interesado: "",
        expediente: "",
        asunto: "",
        persona: "",
        folios: "",
        tipo: "",
        fecha: "",
        recepcion: "",
        cc: [],
        upload: {
          id_documento: "",
          correlativo: "",
          count: 0,
          type: "pdf",
          flag: false
        }
      },
      rules: {
        interesado: [{
          required: true,
          message: "Ingrese un Interesado",
          trigger: "blur",
        }, ],
        expediente: [{
          required: true,
          message: "Ingrese un Expediente",
          trigger: "blur",
        }, ],
        asunto: [{
          required: true,
          message: "Ingrese un asunto",
          trigger: "blur",
        }, ],
        persona: [{
          required: true,
          message: "Seleccione una Persona",
          trigger: "blur",
        }, ],
        tipo: [{
          required: true,
          message: "Seleccione un tipo",
          trigger: "blur",
        }, ],
        fecha: [{
          required: true,
          message: "Ingrese Fecha",
          trigger: "blur",
        }, ],
        upload: [{
          required: true,
          message: "Documento Requerido",
          trigger: "blur",
        }, ],
      },
      handler_loader: {
        loaderOnSubmit: {
          loader: false,
        },
      },
      handler_url: {
        getUsuarios: "usuarios",
        storeDocumento: "storeDocumento",
        storeFile: "upload",
        types: "tipos",
        remitente: "remitente",
        verificacionIngreso: "verificacion-ingreso"
      },
      handler_response: {
        getUsuarios: [],
        getTypesList: [],
        getRemitente: [],

      },
    };
  },
  mounted() {
    this.getUserTransfer();
    this.getType();
    this.getRemitente();
  },
  methods: {
    handleChange(file, fileList) {
      console.log("File: ", file, " FileList: ", fileList)
      this.form.upload.flag = true
      this.flagUpload = false
    },
    CierreDialog() {
      console.log("Se esta cerrando el dialogo sin adjuntar documento")
      console.log(this.upload)
    },
    checkNumber() {
      axios.post(this.handler_url.verificacionIngreso, {
          number: this.form.expediente
        })
        .then(response => {
          console.log(response);
          if (response.data) {
            this.$swal({
              icon: "error",
              title: "Ya se encuentra un expediente con ese correlativo",
              showConfirmButton: false,
              timer: 2500,
            });
          }
        })
    },
    getRemitente() {
      axios.get(this.handler_url.remitente)
        .then(response => {
          this.handler_response.getRemitente = response.data;
        })
    },
    getType() {
      axios.get(this.handler_url.types).then(
        response => {
          this.handler_response.getTypesList = response.data;
        }
      )
    },
    async cargaSuccess(res, file, fileList) {
      console.log("Cargar")
      const _this = this;
      if (res == false) {
        _this.$notify.error({
          title: "Error",
          message: "Solo se permiten archivos en formato PDF",
        });
      } else {
        await this.sleep(1000);

        this.$notify.success({
          title: "Carga de Archivo",
          message: "Documento cargado!",
          showClose: false,
        });

        this.innerVisible = false;
        this.centerDialogVisible = false;

        this.handler_loader.loaderOnSubmit.loader = false;

        // this.src = './../files/' + res.data[0].name + '.pdf';
      }
    },
    handleExceed(files, fileList) {
      this.$message.warning(
        `El límite es 1, haz seleccionado ${
                    files.length
                } archivos esta vez, añade hasta ${files.length + fileList.length}`
      );
    },
    handleRemove(file) {
      console.log("Remove ", file);
      this.form.upload.flag = false
    },
    handlePreview(file) {
      console.log("HandlePreview", file);
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
    },
    handleDownload(file) {
      console.log(file);
    },

    sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
    },
    async callMessage() {

      this.icono = "el-icon-loading";
      await this.sleep(1000);

      this.$notify.success({
        title: 'Información',
        message: 'El documento se envio con exito!',
        showClose: false
      });
      this.centerDialogVisible = false;
      this.icono = "";

    },
    onSubmit(form) {

      const h = this.$createElement;
      if (this.form.upload.flag) {
        this.$refs[form].validate(valid => {
          if (valid) {
            this.handler_loader.loaderOnSubmit.loader = true;
            axios.post(this.handler_url.storeDocumento, {
              interesado: this.form.interesado,
              correlativo: this.form.expediente,
              descripcion: this.form.asunto,
              usuario: this.form.persona,
              folio: this.form.folios,
              type: this.form.tipo,
              fechaDocumento: this.form.fecha,
              fechaRecepcion: this.form.recepcion,
              copy: this.form.cc
            }).then(response => {
              const status = JSON.parse(response.status);
              if (status == "200" && response.data != false) {

                this.form.upload.id_documento = response.data.id
                this.form.upload.correlativo = response.data.correlativo_externo

                //   this.upload.idUpload = response.data.id;
                //   this.upload.correlativo = response.data.correlativo_externo;
                //   this.upload.correlativo = response.data.correlativo_externo;
                this.$refs.upload.submit();
                this.$refs.upload.clearFiles();

                //   this.centerDialogVisible = true;
                // **************************
                //   this.handler_loader.loaderOnSubmit.loader = false;
                this.resetForm(form);
              }
            })
          }
        })
      } else {
        this.flagUpload = true
        this.$notify.error({
          title: "Error",
          message: "No se a cargado un documento PDF",
        });
      }
    },
    // dataUpload(dato,datas){
    //     // console.log(dato,datas)
    //     this.upload.idUpload = dato;
    //     this.upload.correlativo = datas;
    //     this.$refs.upload.submit();
    // },
    resetForm(form) {
      this.$refs[form].resetFields();
    },
    getUserTransfer() {
      axios.get(this.handler_url.getUsuarios).then((response) => {
        this.handler_response.getUsuarios = response.data;
      });
    },
  }
}
</script>
