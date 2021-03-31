<template>
  <div>
    <div class="card">
      <div class="card-header text-write bg-primary">Mis Seguimientos</div>
      <div class="card-body">
        <el-table
          :data="
            endPoint.response.listfiles.filter(
              (data) =>
                !search ||
                data.interesado.toLowerCase().includes(search.toLowerCase()) ||
                data.correlativo.toLowerCase().includes(search.toLowerCase()) ||
                data.correlativo_externo
                  .toLowerCase()
                  .includes(search.toLowerCase()) ||
                data.fecha_ingreso
                  .toLowerCase()
                  .includes(search.toLowerCase()) ||
                data.usuario.toLowerCase().includes(search.toLowerCase())
            )
          "
          :header-cell-style="tableHeaderColor"
          border
          empty-text="Sin Datos"
        >
          <el-table-column
            label="#"
            type="index"
            width="40"
            align="center"
          ></el-table-column>
          <el-table-column label="Remitente" prop="Remitente"></el-table-column>
          <el-table-column
            label="Correlativo"
            prop="correlativo"
          ></el-table-column>
          <el-table-column
            label="Correlativo Interno"
            prop="correlativo_interno"
          ></el-table-column>
          <el-table-column label="Fecha Limite" width="140">
            <template slot-scope="scope">
              <i class="el-icon-time"></i>
              <span>{{ scope.row.final }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Días Restantes" width="140" prop="Dias" align="center">
          </el-table-column>
          <el-table-column label="Usuario Seguimiento" prop="nombre_traslada"></el-table-column>
          <el-table-column label="Usuario Asignado" width="190" prop="usuarioActual">
          </el-table-column>
          <el-table-column  align="center" width="250">
            <template slot="header" slot-scope="scope">
              <el-input v-model="search" size="mini" placeholder="Buscar" />
            </template>
            <template slot-scope="scope">
              <div slot="reference" class="name-wrapper">
                <!-- <el-tag size="medium">{{ scope.row.usuario }}</el-tag> -->
                <el-button
                  type="success"
                  icon="el-icon-news"
                  plain
                  @click="send(scope.row.idTracing,scope.row.correlativo_interno,scope.row.nombre_traslada,scope.row.usuarioActual,1)"
                ></el-button>
                <el-button
                  type="warning"
                  icon="el-icon-receiving"
                  plain
                  @click="send(scope.row.idTracing,scope.row.correlativo_interno,scope.row.nombre_traslada,scope.row.usuarioActual,2)"
                ></el-button>
                  <el-button
                    type="danger"
                    
                    icon="el-icon-s-comment"
                    plain
                    @click="
                      preview(
                        scope.row.id,
                        scope.row.transfer,
                        scope.row.correlativo,
                        scope.row.correlativo_interno,
                        scope.row.url
                      )
                    "
                  ></el-button>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
    <el-dialog
      :title="dialog.preview.title"
      :visible.sync="dialog.preview.visible"
      :width="dialog.preview.width"
      @close="handleClose"
      :top="dialog.preview.top"
      :destroy-on-close="dialog.preview.destroy"
    >
      <el-row :gutter="10">
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="14">
          <el-table
            :data="endPoint.response.comments"
            height="600"
            :header-cell-style="tableComment"
            border
          >
            <el-table-column type="index" label="#"></el-table-column>
            <el-table-column
              prop="usuario"
              label="Usuario"
              width="200"
            ></el-table-column>
            <el-table-column
              prop="comentario"
              label="Mensaje"
            ></el-table-column>
            <el-table-column
              prop="fecha"
              label="Fecha"
              width="170"
            ></el-table-column>
          </el-table>
        </el-col>
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="10">
          <embed :src="url" type="application/pdf" width="100%" height="700" />
        </el-col>
      </el-row>
    </el-dialog>
    <el-dialog
      :title="endPoint.dialogs.message.title"
      :visible.sync="endPoint.dialogs.message.active"
      :width="endPoint.dialogs.message.width"
      :center="endPoint.dialogs.message.center"
      :top="endPoint.dialogs.message.top"
    >
      <el-form ref="form" :model="endPoint.forms.formMessage.form" :rules="endPoint.forms.formMessage.rules" label-width="150px"> 
        <el-form-item label="Mensaje:" prop="message">
           <el-input type="textarea" v-model="endPoint.forms.formMessage.form.message"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm('form')">Enviar</el-button>
          <el-button @click="resetForm('ruleForm')">Cancelar</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <el-dialog
      :title="endPoint.dialogs.tracing.title"
      :visible.sync="endPoint.dialogs.tracing.active"
      :width="endPoint.dialogs.tracing.width"
      :center="endPoint.dialogs.tracing.center"
      :top="endPoint.dialogs.tracing.top"
    >
    <el-table :data="endPoint.response.listMessage">
      <el-table-column label="Usuario Envia" prop="NAME"></el-table-column>
      <el-table-column label="Mensaje" prop="message"></el-table-column>
      <el-table-column label="Fecha" prop="fecha"></el-table-column>
    </el-table>
    </el-dialog>
  </div>
</template>

<style lang="css">
.el-table__row td {
  font-size: 12px;
}
</style>


<script>
export default {
  data() {
    return {
      dialog: {
        preview: {
          title: "",
          visible: false,
          width: "85%",
          top: "2vh",
          destroy: true,
        },
      },
      endPoint: {
        forms: {
          formMessage: {
            data: {
              idTracing: "",
              traslada:"",
              actual:"",
              correlativo:"",
            },
            form: {
              message:""
            },
            rules:{
              message: [
                {
                  required:true,
                  message: 'Ingrese un mensaje',
                  trigger: 'blur'
                }
              ]
            }
          }
        },
        get: {
          files: "getFollowUp",
        },
        post: {
          files: "getFilesByName",
          send: "sendTracingMail",
          message: "getMessagesTracking",
          comments: "getComentario",
        },
        response: {
          listfiles: [],
          listMessage: [],
          comments: [],
        },
        dialogs: {
          message: {
            active: false,
            title: "",
            width: "30%",
            top: "3vh",
            lockScroll: true,
            center: true,
            destroyOnclose: true,
            src: "",
            titleNotifySuccess:'Envio de Correo',
            titleNotifyError:'Envio de Correo',
            MessageNotifySuccess:'Correo Enviado',
            MessageNotifyError:'El correo no se pudo enviar'
          },
          tracing: {
            active: false,
            title: "",
            width: "30%",
            top: "3vh",
            lockScroll: true,
            center: true,
            destroyOnclose: true,
            src: "",
            titleNotifySuccess:'Envio de Correo',
            titleNotifyError:'Envio de Correo',
            MessageNotifySuccess:'Correo Enviado',
            MessageNotifyError:'El correo no se pudo enviar'
          },
        },
      },
      search: "",
      url: "",
    };
  },
  mounted() {
    this.getListFiles();
  },
  methods: {
    tableComment({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #2c3c5c;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    comments(document, transfer) {
      axios
        .post(this.endPoint.post.comments, {
          code: transfer,
          documento: document,
        })
        .then((response) => {
          this.endPoint.response.comments = response.data;
          console.log(response.data);
        });
    },
    handleClose() {
      this.url = "";
      this.endPoint.response.comments = [];
    },
    preview(code, transfer, correlative, externalId,url) {
      console.log(code, transfer, correlative, externalId);
      this.dialog.preview.title = "Expediente " + externalId;
      this.dialog.preview.visible = true;
      this.comments(code, transfer);
      this.url = url;
    },
    getListFiles() {
      axios.get(this.endPoint.get.files).then((response) => {
        this.endPoint.response.listfiles = response.data;
        console.log(response.data)
        
      });
    },
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #009879;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    getMessages(id){
      axios.post(this.endPoint.post.message,{
        id: id
      }).then(response => {
        this.endPoint.response.listMessage = response.data;
        console.log(response.data)
      })
    },
    submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            axios.post(this.endPoint.post.send,{
              tracing: this.endPoint.forms.formMessage.data.idTracing,
              message: this.endPoint.forms.formMessage.form.message,
              traslada:this.endPoint.forms.formMessage.data.traslada,
              actual:this.endPoint.forms.formMessage.data.actual,
              correlativo:this.endPoint.forms.formMessage.data.correlativo
            }).then(response => {
              const status = JSON.parse(response.status);
              const data = response.data;
              if(status === 200 && data != false){
                this.$notify({
                  title:this.endPoint.dialogs.message.titleNotifySuccess,
                  message: this.endPoint.dialogs.message.MessageNotifySuccess,
                  type: 'success'
                })
                this.endPoint.dialogs.message.active = false;
                this.resetForm(formName);
              }
              else{
                this.$notify.error({
                  title:this.endPoint.dialogs.message.titleNotifyError,
                  message: this.endPoint.dialogs.message.MessageNotifyError,
                })
              }
            })
          } else {
            return false;
          }
        });
      },
    send(files,id,userTraslada,userActual,flag) {

      if(flag === 1) {
        this.endPoint.dialogs.message.title = "Expediente No. " + id;
        this.endPoint.dialogs.message.active = true;
        this.endPoint.forms.formMessage.data.idTracing = files;
        this.endPoint.forms.formMessage.data.traslada = userTraslada;
        this.endPoint.forms.formMessage.data.actual = userActual;
        this.endPoint.forms.formMessage.data.correlativo = id;
      }else{
        this.endPoint.dialogs.tracing.active = true;
        this.endPoint.dialogs.tracing.title = "Expediente No. " + id;
        this.getMessages(files);
      }
    },
    resetForm(formName) {
        this.$refs[formName].resetFields();
    }
  },
  computed: {},
};
</script>