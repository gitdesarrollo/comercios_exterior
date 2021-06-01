<template>
  <div class="card">
    <div class="card-header text-white bg-primary">
      Documentos con copia
    </div>
    <div class="card-body">
      <el-table
        :data="
          list_response.documentos
            .filter(
              (data) =>
                !search ||
                data.empresa.toLowerCase().includes(search.toLowerCase()) ||
                data.correlativo.toLowerCase().includes(search.toLowerCase()) ||
                data.formato.toLowerCase().includes(search.toLowerCase())
            )
            .slice((currentPage - 1) * pagesize, currentPage * pagesize)
        "
        :header-cell-style="tableHeaderColor"
        border
        style="width: 100%"
      >
        <el-table-column label="No." type="index"></el-table-column>
        <el-table-column label="Remitente" prop="empresa"></el-table-column>
        <el-table-column
          label="Correlativo"
          prop="correlativo"
        ></el-table-column>
        <el-table-column
          label="Correlativo Interno"
          prop="formato"
        ></el-table-column>
        <el-table-column
          label="Asunto"
          width="500"
          prop="descripcion"
        ></el-table-column>
        <el-table-column label="Operaciones" width="100">
          <template slot="header" slot-scope="scope">
            <el-input v-model="search" size="mini" placeholder="Buscar" />
          </template>
          <template slot-scope="scope" class="pl-3">
            <div>
              <el-button
                type="danger"
                size="mini"
                icon="el-icon-s-comment"
                plain
                @click="
                  preview(
                    scope.row.code,
                    scope.row.formato
                  )
                "
              ></el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <div style="text-align: left; margin-top: 30px">
        <el-pagination
          background
          layout="total,prev, pager, next"
          :total="total"
          @current-change="current_change"
        ></el-pagination>
      </div>
      <el-dialog
        :title="handlerDialog.preview.title"
        :visible.sync="handlerDialog.preview.visible"
        :width="handlerDialog.preview.width"
        :top="handlerDialog.preview.top"
        @close="closeEvent()"
        @open="openEvent()"
        :destroy-on-close="true"
      >
        <el-row :gutter="10">
          <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
            <el-table
              :data="list_response.listComentarios"
              :header-cell-style="tableComment"
              :row-class-name="tableRowClassName"
              height="450"
              border
            >
              <el-table-column label="No." type="index"></el-table-column>
              <el-table-column
                label="Usuario"
                prop="usuario"
                width="180"
              ></el-table-column>
              <el-table-column
                label="Comentario"
                prop="comentario"
              ></el-table-column>
            </el-table>
          </el-col>
          <el-col :xs="12" :sm="12" :md="12" :lg="8" :xl="12">
            <div v-show="handlerFile.showLoading">
              <RotateSquare2></RotateSquare2>
            </div>
            <b-alert v-show="handlerFile.showError" show variant="danger">Sin documento Adjunto</b-alert>
            <div class="reload-div pb-3" v-show="handlerFile.showPdf">
              <el-link type="primary" :underline="false" @click="reload">
                <i class="fas fa-sync reload"></i>
              </el-link>
              
            </div>
            <embed
              :src="url"
              type="application/pdf"
              width="100%"
              height="600"
              ref="viewPDf"
              v-show="handlerFile.showPdf"
            />
            <!-- <div>{{ handlerDialog.preview.html }}</div> -->
          </el-col>
        </el-row>
        <!-- <el-dialog
          :width="handlerDialog.inner.width"
          :title="handlerDialog.inner.title"
          :visible.sync="handlerDialog.inner.innerVisible"
          append-to-body
        >
          <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
              <el-table
                :data="list_response.getFileWord"
                :header-cell-style="tableComment"
                height="450"
                border
              >
                <el-table-column label="No." type="index"></el-table-column>
                <el-table-column label="Archivo" prop="file"></el-table-column>
                <el-table-column
                  label="Fecha"
                  prop="fecha"
                  width="150"
                ></el-table-column>
                <el-table-column label="Operaciones" width="100" align="center">
                  <template slot-scope="scope">
                    <el-link :href="scope.row.url" :underline="false"
                      ><i class="donwloadFile fas fa-download"></i
                    ></el-link>
                  </template>
                </el-table-column>
              </el-table>
            </el-col>
          </el-row>
        </el-dialog> -->
      </el-dialog>
    </div>
  </div>
</template>


<style>

.reload{
  font-size: 2rem;
  color: #2596be
}

.reload-div{
  text-align: right;
}
    .spinner {
    width: 100px!important;
    height: 100px!important;
    margin: 100px auto;
    margin-left: 300px;
    }
    .spinner:after{
        background: #2596be  !important;
    }
.doc {
  width: 100%;
  height: 500px;
}
.el-table .warning-row {
  background: oldlace;
}

.el-table .success-row {
  background: #f0f9eb;
}

.formComentario {
  width: 100%;
}

.block {
  padding: 30px 0;
  text-align: left;
  flex: 1;
}

.textBlock {
  display: block;
  font-size: 14px;
  margin-bottom: 20px;
}

.donwloadFile {
  font-size: 1.5em;
}

.deleteFile {
  font-size: 1.5em;
  color: red;
  margin-left: 5px;
}
</style>
<!--this.src = './../files/' + response.data[0].name + '.pdf';-->
<script>
import { RotateSquare2,RotateSquare5 } from "vue-loading-spinner";
export default {
  components: {
      RotateSquare2,
      RotateSquare5,
      
        
  },
  props: { csrf: { type: String } },
  data() {
    return {
      handlerFile:{
        showPdf: false,
        showLoading: true,
        showError: false,
        code: ""
      },
      search: "",
      message: {
        title: "",
        visible: false,
        width: "",
        bodyText: "",
        closeModal: false,
        button: true,
        dialog: {
          fecha: {
            vModelSeguimiento: "",
            type: "datatime",
            PlaceHolder: "Ingrese fecha de seguimiento",
            defaultTime: "12:00:00",
            file: "",
            tracing: "",
          },
        },
      },
      switchFalse: false,
      drawer: false,
      drawerWord: false,
      limitNumber: 1,
      controlButton: {
        loading: false,
        icon: "",
        disabled: false,
        buttonWord: false,
        buttonPdf: false,
        buttonClose: true,
        buttonPdfClose: true,
      },
      fileList: [],
      datacoment: {
        idDocumento: "",
        idTraslado: "",
        correlativo: "",
        typePdf: "pdf",
        typeExcel: "excel",
        typeWord: "word",
        numberFiles: 0,
      },
      ruleForm: {
        comentario: "",
      },
      documentWord: {
        url: "",
      },
      url_list: {
        lista: "lista",
        dependencias: "dependencias",
        trasladar: "retornar",
        message: "getWithCopies",
        toAccept: "Aceptar",
        getUser: "usuarios",
        TrasladoInterno: "TrasladoInterno",
        info: "infoPDF",
        comentario: "getComentarioCopies",
        setComentario: "setComentario",
        closeDocumento: "closeDocumento",
        uploadFile: "Uploadfile",
        getFiles: "getNameFiles",
        url: "url",
        exists: "exists",
        tracing: "tracingsFiles",
        inactiveTracingFile: "inactiveTracingFile",
        getFileWord: "getFileWord",
        deleteWord: "deleteWord",
        getDireccionesByUser: "getDireccionesByUser",
        getPdfFiles: "getPdfFiles"
      },
      list_response: {
        documentos: [],
        list_dependencia: [],
        list_user: [],
        listcomentarios: [],
        listUrl: [],
        getFileWord: [],
        getDireccionesByUser: []
      },
      total: 0,
      currentPage: 1,
      pagesize: 10,
      EditscreenLoading: false,
      dialogo: false,
      interno: false,
      externo: false,
      trasladoUsuario: false,
      idDocumento: 0,
      depActual: 0,
      idTracing: 0,
      form: {
        departamentoId: "",
        usuario: "",
      },
      formExterno: {
        lugar: "",
        correlativo: "",
      },
      formUser: {
        usuario: "",
      },
      formClose: {
        comentarioCierre: "",
      },
      formRule: {
        comentario: [
          {
            require: true,
            message: "Ingrese comentario",
            trigger: "blur",
          },
        ],
      },
      rules: {
        departamentoId: [
          {
            require: true,
            message: "Seleccione dirección de traslado",
            trigger: "blur",
          },
        ],
        usuario: [
          {
            require: true,
            message: "Seleccione dirección de traslado",
            trigger: "blur",
          },
        ],
      },
      rulesClose: {
        comentarioCierre: [
          {
            require: true,
            message: "Ingrese el comentario",
            trigger: "blur",
          },
        ],
      },
      handlerDialog: {
        preview: {
          title: "",
          visible: false,
          width: "70%",
          top: "2vh",
          ver: false,
          html: "",
        },
        inner: {
          title: "Listado de Archivos",
          innerVisible: false,
          width: "50%",
          codeSearch: "",
        },
        previewClose: {
          title: "Cierre del Documento",
          visible: false,
          width: "35%",
          top: "3vh",
          ver: false,
        },
      },
      src: "",
      url: "",
      Accept: 2,
      handlerLoading: {
        addComent: false,
      },
    };
  },
  mounted() {
    this.getLista();
    // this.selectDireccion();
    // this.getUserTransfer();
    
  },
  methods: {
    celdas({ row, column, rowIndex, columnIndex }) {
      if (columnIndex === 5) {
        console.log(row.tracing)
        if (row.tracing === "1") {
          if(row.dias > 0){
            return "bg-success text-white";
          }else{
            return "bg-danger text-white";
          }
        }
      }
    },
    getDireccionesByUser() {
      axios.get(this.url_list.getDireccionesByUser)
        .then(response => {
          this.list_response.getDireccionesByUser = response.data;
        })
    },
    viewFile(valor) {
      console.log(valor);

      this.handlerDialog.preview.html =
        '<body style="margin:0px;"><object data="' +
        this.url +
        '" type="application/pdf" width="100%" height="600"><iframe src="' +
        this.url +
        '" scrolling="no" width="100%" height="100%" frameborder="0" ></iframe></object></body>';
    },

    deleteWord(name, id) {
      console.log(name, id);
      axios
        .put(this.url_list.deleteWord, {
          files: name,
          id: id,
        })
        .then((response) => {
          console.log(response);
          this.getWord();
        });
    },
    filesWord() {
      this.handlerDialog.inner.innerVisible = true;
      this.getWord();
    },
    getWord() {
      axios
        .post(this.url_list.getFileWord, {
          documento: this.handlerDialog.inner.codeSearch,
        })
        .then((response) => {
          console.log(response);
          this.list_response.getFileWord = response.data;
        });
    },
    tableComment({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #2c3c5c;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #009879;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    closeDate() {
      this.message.visible = false;
      this.message.dialog.fecha.vModelSeguimiento = "";
      this.getLista();
    },
    setActiveMonitoring() {
      axios
        .post(this.url_list.tracing, {
          documento: this.message.dialog.fecha.file,
          tracing: this.message.dialog.fecha.tracing,
          fechaF: this.message.dialog.fecha.vModelSeguimiento,
        })
        .then((response) => {
          this.closeDate();
          // this.getLista();
        });
    },
    confirm() {
      this.message.button = false;
    },
    switchControl(flag, file, tracing) {
      const h = this.$createElement;

      if (flag == "1") {
        this.message.title = "Seguimiento de Expediente";
        this.message.visible = true;
        this.message.width = "30%";
        this.message.dialog.fecha.file = file;
        this.message.dialog.fecha.tracing = tracing;
      } else {
        this.$confirm(
          "¿Desea inactivar el seguimiento de expediente?",
          "Seguimiento",
          {
            confirmButtonText: "Inactivar",
            cancelButtonText: "Cancelar",
            type: "Warning",
          }
        )
          .then(() => {
            axios
              .post(this.url_list.inactiveTracingFile, {
                documento: file,
                tracing: tracing,
              })
              .then((response) => {
                this.getLista();
              });
          })
          .catch(() => {
            this.getLista();
          });
      }
    },
    verDocumento(flag, type) {
      // this.controlButton.disabled = true;
      // this.controlButton.icon ="el-icon-loading";

      // axios.post(this.url_list.url,{
      //     id: this.datacoment.idDocumento,
      //     formato: type
      // })
      // .then(response => {
      //     const status = JSON.parse(response.status);
      //     const length = JSON.parse(response.data.length);
      //     console.log(response);
      //     if(status == '200' && length > 0){
      //         this.src = './../files/' + response.data[0].file;
      this.drawer = flag;
      //         this.controlButton.disabled = false;
      //         this.controlButton.icon ="";
      //     }

      // })
    },
    submitComent(ruleForm) {
      const h = this.$createElement;
      // console.log(this.$refs[ruleForm].$el[0].onfocus());
      if (this.ruleForm.comentario != "") {
        this.handlerLoading.addComent = true;
        this.ComentLoading = true;
        axios
          .post(this.url_list.setComentario, {
            code: this.datacoment.idDocumento,
            traslado: this.datacoment.idTraslado,
            comentario: this.ruleForm.comentario,
          })
          .then((response) => {
            const status = JSON.parse(response.status);
            if (status == "200" && response.data != false) {
              this.handlerLoading.addComent = false;
              this.$message({
                message: h("p", null, [
                  h("i", { style: "color: teal" }, "Agregado!"),
                ]),
                type: "success",
              });
              this.ComentLoading = false;
              this.ruleForm.comentario = "";
              // this.handlerDialog.preview.visible = false;

              this.getComentario(
                this.datacoment.idTraslado,
                this.datacoment.idDocumento
              );
            }
          });
      } else {
        this.$notify.error({
          title: "Error",
          message: "Complete el campo de comentario",
        });
        this.$refs[ruleForm].$el[0].focus();
      }
      // });
    },
    archivarDocument(form) {
      const h = this.$createElement;
      if (this.formClose.comentarioCierre != "") {
        // console.log("adentro");
        this.trasladoUsuario = true;
        axios
          .put(this.url_list.closeDocumento, {
            code: this.idDocumento,
            traslado: this.depActual,
            comentario: this.formClose.comentarioCierre,
          })
          .then((response) => {
            const status = JSON.parse(response.status);
            if (status == "200" && response.data != false) {
              this.$message({
                message: h("p", null, [
                  h("i", { style: "color: teal" }, "Documento Archivado!"),
                ]),
                type: "success",
              });
              this.trasladoUsuario = false;
              this.handlerDialog.previewClose.visible = false;
              this.getLista();
              // this.$refs[form].resetFields();
              // this.handlerDialog.preview.visible = false;
              // this.list_response.listComentarios = [];
              // this.getComentario(this.datacoment.idTraslado);
            }
          });
      } else {
        this.$message({
          showClose: true,
          message: "Ingrese comentario",
          type: "error",
        });
        this.$refs[form].$el[0].autofocus = true;
        this.$refs.formClose.$el[0].placeholder =
          "Ingrese Comentario para continuar";
      }
    },
    cierreClose(form) {
      this.handlerDialog.previewClose.visible = false;
      this.$refs[form].resetFields();
    },
    getComentario(documento) {
      axios
        .post(this.url_list.comentario, {
          documento: documento,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          const result = response.data;
          if (status == "200") {
            // console.log("comentarios", response.data)
            this.list_response.listComentarios = response.data;
            console.log("Comentarios: ", response.data);
            this.total = response.data.length;
          }
        });
    },

    reload(){
      this.handlerFile.showLoading = true
      this.handlerFile.showPdf = false
      this.getFileCopies(this.handlerFile.code);
    },

    getFileCopies(code){
      
      axios.post(this.url_list.getPdfFiles,{document: code})
        .then(response => {
          console.log("file",response.data)
          if(response.data.length > 0){
            this.url = './../files/' + response.data[0].file
            this.handlerFile.showPdf = true
            this.handlerFile.showLoading = false
          }else{
            this.handlerFile.showPdf = false
            this.handlerFile.showLoading = false
            this.handlerFile.showError = true
          }
        })
    },
    getLista() {
      axios.get(this.url_list.message).then((response) => {
        this.list_response.documentos = response.data;
        this.total = response.data.length;
        console.log("lista", response.data);
        
      });
      // axios.get(this.url_list.lista).then((response) => {
      //   this.total = response.data.length;
      //   console.log(response.data);
      // });
    },
    getTraslado(id) {
      this.dialogo = true;
      this.idDocumento = id;

      console.log(id);
      // console.log(id);
    },
    getTrasladoInterno(id, traslado, tracing) {
      this.interno = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.idTracing = tracing;
      this.getComentario(traslado, id);
    },
    getTrasladoExterno(id, traslado) {
      this.externo = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.getComentario(traslado, id);
    },
    cierreDocumento(id, traslado, formato) {
      this.handlerDialog.previewClose.visible = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.datacoment.idDocumento = id;
      this.datacoment.idTraslado = traslado;
      this.datacoment.correlativo = formato;
      this.getNameFiles(id);
    },
    current_change: function (currentPage) {
      this.currentPage = currentPage;
    },
    selectDireccion() {
      axios.get(this.url_list.dependencias).then((response) => {
        this.list_response.list_dependencia = response.data;
      });
    },
    documentTransfer(form) {
      console.log(this.form.departamentoId, this.idDocumento);
      this.$refs[form].validate((valid) => {
        if (valid) {
          this.EditscreenLoading = true;
          axios
            .put(this.url_list.trasladar, {
              Documento: this.idDocumento,
              idDireccionTraslado: this.form.departamentoId,
            })
            .then((response) => {
              this.EditscreenLoading = false;
              this.dialogo = false;
              this.getLista();
              // console.log(response.data);
            });
        }
      });
    },
    transferUser(form) {
      this.$refs[form].validate((valid) => {
        if (valid) {
          this.trasladoUsuario = true;
          axios
            .put(this.url_list.TrasladoInterno, {
              Documento: this.idDocumento,
              Traslado: this.depActual,
              idUsuario: this.form.usuario,
              externo: false,
              tracing: this.idTracing,
            })
            .then((response) => {
              this.trasladoUsuario = false;
              this.interno = false;
              this.getLista();
            });
        }
      });
    },
    transferExterno() {
      this.trasladoUsuario = true;
      axios
        .put(this.url_list.TrasladoInterno, {
          Documento: this.idDocumento,
          Traslado: this.depActual,
          idUsuario: this.form.usuario,
          externo: true,
          destino: this.formExterno.lugar,
          correlativoEx: this.formExterno.correlativo,
        })
        .then((response) => {
          this.trasladoUsuario = false;
          this.externo = false;
          this.getLista();
        });
    },
    toAccepts(id, documento) {
      this.getComentario(documento, id);
      const h = this.$createElement;
      axios
        .put(this.url_list.toAccept, {
          code: id,
        })
        .then((response) => {
          console.log(response.data);
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.$message({
              message: h("p", null, [
                h("i", { style: "color: teal" }, "Operación exitosa"),
              ]),
              type: "success",
            });
            this.Accept = 1;
            this.getLista();
          }
        });
    },
    getUserTransfer() {
      axios.get(this.url_list.getUser).then((response) => {
        this.list_response.list_user = response.data;
      });
    },
    tableRowClassName({ row, rowIndex }) {
      if (rowIndex % 2 == 0) {
        return "warning-row";
      } else {
        return "success-row";
      }
      return "";
    },
    preview(code, correlativo) {

      this.handlerDialog.preview.title = "Expediente No. " + correlativo;
      this.getComentario(code);
      this.handlerDialog.preview.visible = true;
      this.getFileCopies(code);
      this.handlerFile.code = code
      // this.url = ""
      // this.datacoment.idDocumento = code;
      // this.datacoment.idTraslado = traslado;
      // this.datacoment.correlativo = correlativo;
      // this.url = url;
      // this.handlerDialog.inner.codeSearch = code;
      // // this.viewFile("primero");
      // this.getNameFiles(code);
    },
    closeEvent() {
      // console.log("pdf ", this.handlerFile.showPdf , " loading:" ,this.handlerFile.showLoading)
      this.handlerFile.showPdf = false
      this.handlerFile.showLoading = true
      this.handlerFile.showError = false
      // console.log("pdf ", this.handlerFile.showPdf , " loading:" ,this.handlerFile.showLoading)
      this.url = "";
      // this.controlButton.buttonWord = false;
      // this.controlButton.buttonPdf = false;
      // this.src = "";
      // this.documentWord.url = "";
      // this.ruleForm.comentario = "";
    },
    openEvent() {
      // console.log("pdf ", this.handlerFile.showPdf , " loading:" ,this.handlerFile.showLoading)
      // axios
      //   .post(this.url_list.exists, {
      //     id: this.datacoment.idDocumento,
      //     formato: "word",
      //   })
      //   .then((response) => {
      //     const result = response.data;
      //     if (result != false) {
      //       // this.documentWord.url = "./../files/" + response.data[0].file;
      //       this.controlButton.buttonWord = true;
      //     } else {
      //       this.controlButton.buttonWord = false;
      //     }
      //   });

      // axios
      //   .post(this.url_list.url, {
      //     id: this.datacoment.idDocumento,
      //     formato: "pdf",
      //   })
      //   .then((response) => {
      //     const status = JSON.parse(response.status);
      //     const length = JSON.parse(response.data.length);
      //     if (status == "200" && length > 0) {
      //       this.src = "./../files/" + response.data[0].file;
      //       // this.drawer = flag;
      //       // this.controlButton.disabled = false;
      //       // this.controlButton.icon ="";
      //       this.controlButton.buttonPdf = true;
      //     } else {
      //       this.controlButton.buttonPdf = false;
      //     }
      //   });
    },
    handleRemove(file, fileList) {
      let vm = this;
      axios
        .delete("/upload/" + file.uid)
        .then(function () {
          let index = _.findIndex(vm.fileList, ["uid", file.uid]);
          vm.$delete(vm.fileList, index);
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    handlePreview(file) {
      console.log("files", file);
    },
    handleExceed(files, fileList) {
      console.log("error", files, fileList);
      this.$message.warning(
        `El límite es 1, haz seleccionado ${
          files.length
        } archivos esta vez, añade hasta ${files.length + fileList.length}`
      );
    },
    cargaSuccess(res, file, fileList) {
      console.log("resultados ", res, " files ", file, " lista ", fileList);
      const _this = this;

      if (res == false) {
        _this.$notify.error({
          title: "Error",
          message: "Documento no permitido/documento corrupto",
        });
      } else {
        this.$notify.success({
          title: "Carga de Archivo",
          message: "Documento cargado!",
          showClose: false,
        });
        // console.log(res[0][0].file);
        // console.log(this.$refs.viewPDf)
        // this.viewFile("segundo");

        if (res[0][0].formato == "pdf") {
          this.url = "./../files/" + res[0][0].file;
          this.$refs.viewPDf.src = "";
          this.$refs.viewPDf.src = this.url;
          // this.controlButton.buttonPdf = true;
        } else {
          this.controlButton.buttonWord = true;
          this.documentWord.url = "./../files/" + res[0][0].file;
        }
      }
    },
    cargaSuccessClose(res, file, filters) {
      console.log("res ", res, " file ", file, " filters ", filters);
      const _this = this;
      if (res == false) {
        _this.$message({
          message: res.desc,
          type: "warning",
        });
      } else {
        this.$notify.success({
          title: "Carga de Archivo",
          message: "Documento cargado!",
          showClose: false,
        });
        console.log(res[0][0].file);
        this.controlButton.buttonClose = true;
        this.controlButton.buttonPdfClose = false;
      }
    },
    getNameFiles(correlativo) {
      axios
        .post(this.url_list.getFiles, {
          correlativoD: correlativo,
        })
        .then((response) => {
          this.fileList = response.data;

          if (response.data.length > 0) {
            this.limitNumber = 2;

            this.datacoment.numberFiles = response.data.length;

            this.src = "./../files/" + response.data[0].name + ".pdf";
          } else {
            this.datacoment.numberFiles = 0;
          }
        });
    },
  },
};
</script>
