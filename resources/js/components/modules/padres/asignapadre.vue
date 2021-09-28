
<template>
  <div class="card">
    <div class="card-header text-white bg-primary">
      Listado de documentos por agrupar
    </div>
    <div class="card-body">
      <el-row :gutter="10">
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
          <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="18">
              <div class="block">
                <span class="demonstration">
                  Seleccione el Agrupador y presione el boton Agrupar
                </span>
                <el-select
                  v-model="padre_asignado"
                  class="select_width"
                  placeholder="Seleccione ..."
                  clearable
                  filterable
                >
                  <el-option
                    v-for="item in links"
                    :key="item.id"
                    :label="item.value"
                    :value="item.id"
                  >
                  </el-option>
                </el-select>
              </div>
            </el-col>
            <el-col :xl="6">
              <div class="block mt-4">
                <el-button
                  type="primary"
                  icon="el-icon-search"
                  @click="asignaPadre"
                ></el-button>
              </div>
            </el-col>
          </el-row>
        </el-col>
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
          <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="18">
              <div class="block">
                <span class="demonstration">
                  Seleccione el agrupador y presione el boton de ordenar
                </span>
                <el-select
                  v-model="filter_padre"
                  class="select_width"
                  placeholder="Seleccione ..."
                  clearable
                  filterable
                >
                  <el-option
                    v-for="item in links"
                    :key="item.id"
                    :label="item.value"
                    :value="item.id"
                  >
                  </el-option>
                </el-select>
              </div>
            </el-col>
            <el-col :xl="6">
              <div class="block mt-4">
                <el-button
                  type="primary"
                  icon="el-icon-search"
                  @click="getLista_filter"
                ></el-button>
                <el-button
                  type="success"
                  icon="el-icon-refresh"
                  @click="reset_data"
                ></el-button>
              </div>
            </el-col>
          </el-row>
        </el-col>
      </el-row>

      <el-divider></el-divider>

      <div v-if="skeleton_active">
        <el-skeleton :rows="6" animated />
      </div>
      <div v-else>
        <el-table
          :data="displayData"
          :header-cell-style="tableHeaderColor"
          border
          style="width: 100%"
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="60"></el-table-column>
          <!-- <el-table-column label="No." type="index"></el-table-column> -->
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
          <el-table-column
            label="Fecha"
            width="90"
            prop="fecha"
          ></el-table-column>
          <el-table-column
            prop="agrupador"
            label="Agrupador"
            width="190"
            align="center"
          >
            <template slot="header" slot-scope="scope">
              <el-input v-model="search" size="mini"> </el-input>
            </template>
          </el-table-column>
        </el-table>
        <div style="text-align: left; margin-top: 30px">
          <el-pagination
            background
            layout="prev, pager, next"
            @current-change="handleCurrentChange"
            :page-size="pagesize"
            :total="total"
          ></el-pagination>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.el-table--border th,
.el-table__fixed-right-patch {
  border-bottom: 1px solid #009879 !important;
}
.el-table thead.is-group th {
  background: #009879 !important;
}
.azul {
  background-color: #409eff;
}
.doc {
  width: 100%;
  height: 500px;
}
.el-autocomplete {
  display: contents !important;
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
<script>
import dialogExpediente from "../dialogos/expedientes.vue";
import { RotateSquare2, RotateSquare5 } from "vue-loading-spinner";
import JQuery from "jquery";
import moment from "moment";
export default {
  components: {
    RotateSquare2,
    RotateSquare5,
    dialogExpediente,
  },
  props: {
    csrf: {
      type: String,
    },
  },
  data() {
    return {
      input1: "",
      input2: "",
      input3: "",
      select: "",
      skeleton_active: false,
      links: [],
      padre_asignado: "",
      filter_padre: "",
      publicPath: __dirname,
      event_at: "",
      handlerFile: {
        showPdf: false,
        showLoading: true,
        showError: false,
        code: "",
        dialogComent: false,
        tableComent: false,
      },
      search: "",
      message: {
        title: "",
        visible: false,
        width: "",
        top: "2vh",
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
          ministro: "",
          viceministerio: "",
          instruccion: "",
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
        id_traslado: "",
        code: "",
      },
      formInstrucciones: {
        instruccion: "",
        ministro: "",
        viceministerio: "",
        cc: [],
      },
      documentWord: {
        url: "",
      },
      url_list: {
        lista: "lista",
        dependencias: "dependencias",
        trasladar: "retornar",
        message: "getViceById",
        message_filter: "getViceByUserIdFilter",
        toAccept: "Aceptar",
        getUser: "usuarios",
        TrasladoInterno: "TrasladoInterno",
        info: "infoPDF",
        comentario: "getComentario",
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
        getPdfFiles: "getPdfFiles",
        getSeguimientoDocumento: "getSeguimientoDocumento",
        makeBoleta: "makeBoleta",
        listaVice: "listaVice",
        padres: "getPadresDet",
        asignapadres: "asignaPadreAll",
      },
      list_response: {
        documentos: [],
        list_dependencia: [],
        list_user: [],
        list_padres: [],
        listcomentarios: [],
        listUrl: [],
        getFileWord: [],
        getDireccionesByUser: [],
        listaVice: [],
        prueba: [],
      },
      total: 0,
      currentPage: 1,
      pagesize: 10,
      EditscreenLoading: false,
      dialogo: false,
      interno: false,
      agrupador: false,
      externo: false,
      agrupadorfrm: false,
      trasladoUsuario: false,
      idDocumento: 0,
      depActual: 0,
      idTracing: 0,
      form: {
        departamentoId: "",
        usuario: "",
        cc: [],
      },
      formExterno: {
        lugar: "",
        correlativo: "",
      },
      formPadre: {
        iddocumento: "",
        descripcion: "",
        id: "",
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
          width: "85%",
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
        boleta: {
          // title: "Boleta de delegación",
          visible: false,
          width: "75%",
          top: "3vh",
          ver: false,
          img: "./imagenes/gobierno.jpg",
          img2: "/imagenes/gobierno.jpg",
          formato: "",
          correlativo: "",
          fecha: "",
          empresa: "",
          descripcion: "",
          code: "",
          instrucciones_generales: "",
          instrucciones_ministro: "",
          fechaInicio: "",
          fechafin: "",
          viceministerio: "",
          cc: "",
        },
      },
      multipleSelection: [],
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
    this.selectDireccion();
    this.getUserTransfer();
    this.event_at = moment(new Date()).format("D/MM/YYYY");
    this.getListaVice();

    this.getAgrupador();
  },
  computed: {
    searching() {
      if (!this.search) {
        this.total = this.list_response.documentos.length;
        return this.list_response.documentos;
      }
      this.page = 1;
      return this.list_response.documentos.filter(
        (data) =>
          data.empresa.toLowerCase().includes(this.search.toLowerCase()) ||
          data.correlativo.toLowerCase().includes(this.search.toLowerCase()) ||
          data.formato.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    displayData() {
      this.total = this.searching.length;

      return this.searching.slice(
        this.pagesize * this.currentPage - this.pagesize,
        this.pagesize * this.currentPage
      );
    },
  },
  methods: {
    asignacion_masiva() {
      console.log(this.multipleSelection);
    },
    async reset_data() {
      this.skeleton_active = !this.skeleton_active;
      this.getLista();
      this.filter_padre = "";
      this.getAgrupador();
      await this.sleep(1000);
      this.skeleton_active = !this.skeleton_active;
    },
    sleep(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms));
    },
    handleCurrentChange(currentPage) {
      this.currentPage = currentPage;
    },
    querySearch(queryString, cb) {
      var links = this.links;
      var results = queryString
        ? links.filter(this.createFilter(queryString))
        : links;
      // call callback function to return suggestions
      cb(results);
    },
    createFilter(queryString) {
      return (link) => {
        return (
          link.descripcion.toLowerCase().indexOf(queryString.toLowerCase()) ===
          0
        );
      };
    },

    handleSelectionChange(val) {
      this.multipleSelection = val;
    },

    getListaVice() {
      axios.get(this.url_list.listaVice).then((response) => {
        this.list_response.listaVice = response.data;
      });
    },
    imprimir() {
      axios
        .post(
          this.url_list.makeBoleta,
          {
            img: this.handlerDialog.boleta.img2,
            correlativo: this.handlerDialog.boleta.correlativo,
            mineco: this.handlerDialog.boleta.formato,
            fecha: this.handlerDialog.boleta.fecha,
            fechaFin: this.handlerDialog.boleta.fechafin,
            descripcion: this.handlerDialog.boleta.descripcion,
            empresa: this.handlerDialog.boleta.empresa,
            ministro: this.handlerDialog.boleta.instrucciones_ministro,
            general: this.handlerDialog.boleta.instrucciones_generales,
            viceministerio: this.handlerDialog.boleta.viceministerio,
            cc: this.handlerDialog.boleta.cc,
          },
          {
            responseType: "blob",
          }
        )
        .then((response) => {
          var fileURL = window.URL.createObjectURL(new Blob([response.data]));

          var fileLink = document.createElement("a");
          fileLink.href = fileURL;
          fileLink.setAttribute("download", "file.pdf");
          document.body.appendChild(fileLink);
          fileLink.click();
        });
    },
    reload() {
      this.handlerFile.showLoading = true;
      this.handlerFile.showPdf = false;
      this.getFileCopies(this.handlerFile.code);
    },

    getDireccionesByUser() {
      axios.get(this.url_list.getDireccionesByUser).then((response) => {
        this.list_response.getDireccionesByUser = response.data;
      });
    },
    viewFile(valor) {
      this.handlerDialog.preview.html =
        '<body style="margin:0px;"><object data="' +
        this.url +
        '" type="application/pdf" width="100%" height="600"><iframe src="' +
        this.url +
        '" scrolling="no" width="100%" height="100%" frameborder="0" ></iframe></object></body>';
    },

    deleteWord(name, id) {
      axios
        .put(this.url_list.deleteWord, {
          files: name,
          id: id,
        })
        .then((response) => {
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
      // this.message.dialog.instruccion = "";
      // this.message.dialog.ministro = "";
      // this.message.dialog.viceministerio = "";
      this.getLista();
    },
    setActiveMonitoring() {
      axios
        .post(this.url_list.tracing, {
          documento: this.message.dialog.fecha.file,
          tracing: this.message.dialog.fecha.tracing,
          fechaF: this.message.dialog.fecha.vModelSeguimiento,
          instruccion: this.formInstrucciones.instruccion,
          ministro: this.formInstrucciones.ministro,
          viceministerio: this.formInstrucciones.viceministerio,
          cc: this.formInstrucciones.cc,
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
      //
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
                  h(
                    "i",
                    {
                      style: "color: teal",
                    },
                    "Agregado!"
                  ),
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
                  h(
                    "i",
                    {
                      style: "color: teal",
                    },
                    "Documento Archivado!"
                  ),
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
    getComentario(traslado, documento) {
      axios
        .post(this.url_list.comentario, {
          code: traslado,
          documento: documento,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          const result = response.data;
          if (status == "200") {
            this.list_response.listComentarios = response.data;

            this.total = response.data.length;
          }
        });
    },
    getAgrupador() {
      axios.get(this.url_list.padres).then((response) => {
        const status = JSON.parse(response.status);
        const result = response.data;

        if (status == "200") {
          this.links = response.data;
        }

        // return this.links;
      });
    },
    getLista() {
      axios.get(this.url_list.message).then((response) => {
        this.list_response.documentos = response.data;
        this.total = response.data.length;
      });
    },
    async getLista_filter() {
      this.skeleton_active = !this.skeleton_active;
      axios
        .post(this.url_list.message_filter, { id_padre: this.filter_padre })
        .then((response) => {
          this.list_response.documentos = response.data;
          this.skeleton_active = !this.skeleton_active;
          // this.total = response.data.length;
        });
    },

    getTraslado(id) {
      this.dialogo = true;
      this.idDocumento = id;
    },
    getTrasladoInterno(id, traslado, tracing) {
      this.interno = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.idTracing = tracing;
      this.getComentario(traslado, id);
    },

    getPadreAgrupador(documentox) {
      this.agrupador = true;
      this.formPadre.iddocumento = documentox;
      this.getAgrupador();
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
    boleta(formato, correlativo, fecha, empresa, descripcion, code) {
      this.handlerDialog.boleta.visible = true;
      this.handlerDialog.boleta.formato = formato;
      this.handlerDialog.boleta.correlativo = correlativo;
      this.handlerDialog.boleta.fecha = fecha;
      this.handlerDialog.boleta.empresa = empresa;
      this.handlerDialog.boleta.descripcion = descripcion;
      this.handlerDialog.boleta.code = code;

      axios
        .post(this.url_list.getSeguimientoDocumento, {
          code: code,
        })
        .then((response) => {
          this.handlerDialog.boleta.instrucciones_generales =
            response.data[0]["instruccion"];
          this.handlerDialog.boleta.instrucciones_ministro =
            response.data[0]["instruccion_ministro"];
          this.handlerDialog.boleta.fechaInicio =
            response.data[0]["fechaInicial"];
          this.handlerDialog.boleta.fechafin = response.data[0]["fechaFinal"];
          this.handlerDialog.boleta.viceministerio =
            response.data[0]["viceministerio"];
          this.handlerDialog.boleta.cc = response.data[0]["cc"];
        });
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
              copy: this.form.cc,
            })
            .then((response) => {
              this.trasladoUsuario = false;
              this.interno = false;
              this.getLista();
            })
            .catch((error) => {});
        }
      });
    },
    asignaPadre() {
      const h = this.$createElement;
      if (this.padre_asignado != "") {
        axios
          .post(this.url_list.asignapadres, {
            id: this.padre_asignado,
            hijos: this.multipleSelection,
          })
          .then((response) => {
            if (response.data) {
              this.$swal({
                icon: "success",
                title: "Asignación completada con exito",
                showConfirmButton: false,
                timer: 2500,
              });

              this.getLista();
            }
          });
      } else {
        this.$swal({
          icon: "error",
          title: "faltan datos para la asignación",
          showConfirmButton: false,
          timer: 2500,
        });
      }
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
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.$message({
              message: h("p", null, [
                h(
                  "i",
                  {
                    style: "color: teal",
                  },
                  "Operación exitosa"
                ),
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
    preview(code, traslado, correlativo, url) {
      this.handlerDialog.preview.title = "Expediente No. " + correlativo;
      this.ruleForm.id_traslado = traslado;
      this.ruleForm.code = code;

      this.handlerDialog.preview.visible = true;
      this.datacoment.idDocumento = code;
      this.datacoment.idTraslado = traslado;
      this.datacoment.correlativo = correlativo;
      this.url = url;
      this.handlerDialog.inner.codeSearch = code;
      this.handlerFile.showLoading = false;
      this.handlerFile.showPdf = true;
      this.handlerFile.code = code;
      // this.viewFile("primero");
      // this.getNameFiles(code);
    },

    getFileCopies(code) {
      axios
        .post(this.url_list.getPdfFiles, {
          document: code,
        })
        .then((response) => {
          if (response.data.length > 0) {
            this.url = "./../files/" + response.data[0].file;
            this.handlerFile.showPdf = true;
            this.handlerFile.showLoading = false;
          } else {
            this.handlerFile.showPdf = false;
            this.handlerFile.showLoading = false;
            this.handlerFile.showError = true;
          }
        });
    },
    closeEvent() {
      this.controlButton.buttonWord = false;
      this.controlButton.buttonPdf = false;
      this.src = "";
      this.documentWord.url = "";
      this.ruleForm.comentario = "";
      this.url = "";
      this.handlerFile.showPdf = false;
      this.handlerFile.showLoading = true;
      this.handlerFile.showError = false;
      this.ruleForm.id_traslado = "";
      this.ruleForm.code = "";
      this.list_response.listcomentarios = [];
      this.handlerFile.dialogComent = false;
      this.handlerFile.tableComent = false;
    },
    formCloseDialog() {
      this.formClose.comentarioCierre = "";
      this.controlButton.buttonPdfClose = true;
      this.datacoment.idDocumento = "";
      this.datacoment.correlativo = "";
    },
    closeTrasladoInterno() {
      this.form.usuario = "";
      this.form.cc = [];
    },
    closeAgrupador() {
      this.formPadre.descripcion = "";
      this.formPadre.id = "";
      this.agrupador = false;
    },
    opened() {
      this.handlerFile.dialogComent = true;

      axios
        .post(this.url_list.comentario, {
          code: this.ruleForm.id_traslado,
          documento: this.ruleForm.code,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.handlerFile.dialogComent = false;
            this.handlerFile.tableComent = true;

            this.list_response.listcomentarios = response.data;

            this.total = response.data.length;
          }
        });
    },
    openEvent() {
      // this.getComentario(this.ruleForm.id_traslado, this.ruleForm.code);

      axios
        .post(this.url_list.exists, {
          id: this.datacoment.idDocumento,
          formato: "word",
        })
        .then((response) => {
          const result = response.data;
          if (result != false) {
            // this.documentWord.url = "./../files/" + response.data[0].file;
            this.controlButton.buttonWord = true;
          } else {
            this.controlButton.buttonWord = false;
          }
        });

      axios
        .post(this.url_list.url, {
          id: this.datacoment.idDocumento,
          formato: "pdf",
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          const length = JSON.parse(response.data.length);
          if (status == "200" && length > 0) {
            this.src = "./../files/" + response.data[0].file;
            // this.drawer = flag;
            // this.controlButton.disabled = false;
            // this.controlButton.icon ="";
            this.controlButton.buttonPdf = true;
          } else {
            this.controlButton.buttonPdf = false;
          }
        });
    },
    handleRemove(file, fileList) {
      let vm = this;
      axios
        .delete("/upload/" + file.uid)
        .then(function () {
          let index = _.findIndex(vm.fileList, ["uid", file.uid]);
          vm.$delete(vm.fileList, index);
        })
        .catch(function (error) {});
    },
    handlePreview(file) {},
    handleExceed(files, fileList) {
      this.$message.warning(
        `El límite es 1, haz seleccionado ${
          files.length
        } archivos esta vez, añade hasta ${files.length + fileList.length}`
      );
    },
    cargaSuccess(res, file, fileList) {
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
