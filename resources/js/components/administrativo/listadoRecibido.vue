<template>
  <div class="card">
    <div class="card-header text-white bg-primary">
      Listado de Documentos Recibidos
    </div>
    <div class="card-body">
      <el-table
        :data="
          list_response.documentos.slice(
            (currentPage - 1) * pagesize,
            currentPage * pagesize
          )
        "
        style="width: 100%"
      >
        <el-table-column label="No." type="index"></el-table-column>
        <el-table-column label="Dirigido" prop="empresa"></el-table-column>
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
          prop="estado"
          label="Etiqueta"
          width="100">
          <template slot-scope="scope">
            <div v-if="scope.row.estado === 9">
              <el-tag
                :type="scope.row.estado === '9' ? 'primary' : 'warning'"
                disable-transitions>Externo</el-tag>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Operaciones" width="250">
          <template slot-scope="scope" class="pl-3">
            <div v-if="scope.row.estado == 2">
              <el-button
                type="danger"
                size="mini"
                icon="el-icon-check"
                plain
                @click="toAccepts(scope.row.idTraslado, scope.row.code)"
              ></el-button>
            </div>
            <div v-else-if="scope.row.estado == 9">
              <el-button
                v-if="scope.row.rol == 4"
                size="mini"
                type="el-icon-error"
                icon="el-icon-error"
                plain
                @click="cierreDocumento(scope.row.code, scope.row.idTraslado)"
              ></el-button>
            </div>
            <div v-else>
              <el-button
                type="danger"
                size="mini"
                icon="el-icon-s-comment"
                plain
                @click="
                  preview(
                    scope.row.code,
                    scope.row.idTraslado,
                    scope.row.correlativo
                  )
                "
              ></el-button>
              <!-- <el-button
                type="primary"
                size="mini"
                icon="el-icon-refresh-left"
                plain
                @click="getTraslado(scope.row.code)"
              ></el-button>-->
              <el-button
                size="mini"
                type="primary"
                icon="el-icon-s-check"
                plain
                @click="
                  getTrasladoInterno(scope.row.code, scope.row.idTraslado)
                "
              ></el-button>
              <el-button
                size="mini"
                type="warning"
                icon="el-icon-s-promotion"
                plain
                @click="
                  getTrasladoExterno(scope.row.code, scope.row.idTraslado)
                "
              ></el-button>
              <el-button
                v-if="scope.row.rol == 4"
                size="mini"
                type="el-icon-error"
                icon="el-icon-error"
                plain
                @click="cierreDocumento(scope.row.code, scope.row.idTraslado)"
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
        title="Trasladar Documento"
        :visible.sync="dialogo"
        width="35%"
        top="3vh"
        center
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :show-close="false"
        destroy-on-close
      >
        <el-form
          :inline="false"
          :model="form"
          ref="form"
          :rule="rules"
          label-width="150px"
        >
          <el-form-item label="Dirección:" prop="departamentoId">
            <el-select
              v-model="form.departamentoId"
              class="select_width"
              clearable
              filterable
              placeholder="Seleccione Dirección"
            >
              <el-option
                v-for="item in list_response.list_dependencia"
                :key="item.id_dependencia"
                :label="item.descripcion"
                :value="item.id_dependencia"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button
              type="primary"
              @click="documentTransfer('form')"
              v-loading.fullscreen.lock="EditscreenLoading"
              >Trasladar</el-button
            >
            <el-button @click="dialogo = false">Cancel</el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
      <el-dialog
        :title="handlerDialog.previewClose.title"
        :visible.sync="handlerDialog.previewClose.visible"
        :width="handlerDialog.previewClose.width"
        :top="handlerDialog.previewClose.top"
        center
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :show-close="false"
        destroy-on-close
      >
        <el-form
          :inline="false"
          :model="formClose"
          ref="formClose"
          :rules="rulesClose"
          id="formClose"
          label-width="150px"
        >
          <el-form-item label="Comentario:" prop="comentarioCierre">
            <el-input
              type="textarea"
              v-model="formClose.comentarioCierre"
              maxlength="1000"
              :autosize="{ minRows: 7, maxRows: 13 }"
              show-word-limit
            ></el-input>
          </el-form-item>
          <el-form-item>
            <el-button
              type="primary"
              @click="archivarDocument('formClose')"
              v-loading.fullscreen.lock="trasladoUsuario"
              >Archivar</el-button
            >
            <el-button @click="cierreClose('formClose')">Cancel</el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
      <el-dialog
        title="Traslado Externo"
        :visible.sync="externo"
        width="35%"
        top="3vh"
        center
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :show-close="false"
        destroy-on-close
      >
        <el-form :inline="false" :model="formExterno" ref="formExterno" label-width="150px">
          <el-form-item label="Lugar:" >
            <el-input
              v-model="formExterno.lugar"
            ></el-input>
          </el-form-item>
          <el-form-item label="Correlativo:" >
            <el-input
              v-model="formExterno.correlativo"
            ></el-input>
          </el-form-item>
          <el-form-item>
            <el-button
              type="primary"
              @click="transferExterno()"
              v-loading.fullscreen.lock="trasladoUsuario"
              >Trasladar</el-button
            >
            <el-button @click="externo = false">Cancel</el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
      <el-dialog
        title="Traslado a personal interno"
        :visible.sync="interno"
        width="35%"
        top="3vh"
        center
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :show-close="false"
        destroy-on-close
      >
        <el-form :inline="false" :model="form" ref="form" label-width="150px">
          <el-form-item label="Usuario:" prop="usuario">
            <el-select
              v-model="form.usuario"
              class="select_width"
              clearable
              filterable
              placeholder="Seleccione usuario"
            >
              <el-option
                v-for="items in list_response.list_user"
                :key="items.id"
                :label="items.name"
                :value="items.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button
              type="primary"
              @click="transferUser('form')"
              v-loading.fullscreen.lock="trasladoUsuario"
              >Trasladar</el-button
            >
            <el-button @click="interno = false">Cancel</el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
      <el-dialog
        :title="handlerDialog.preview.title"
        :visible.sync="handlerDialog.preview.visible"
        :width="handlerDialog.preview.width"
        :top="handlerDialog.preview.top"
        center
        destroy-on-close
      >
        <el-row :gutter="10">
          <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24">
            <el-table
              :data="list_response.listComentarios"
              :row-class-name="tableRowClassName"
              height="550"
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
          <!-- <embed :src="src" type="application/pdf" width="90%" height="600px" /> -->
          <!-- <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="9">
          </el-col>-->
        </el-row>
        <el-row :gutter="10">
          <el-form
            :model="ruleForm"
            :rules="rules"
            ref="ruleForm"
            class="formComentario"
          >
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24">
              <el-form-item label="Comentario:" prop="comentario">
                <el-input
                  type="textarea"
                  v-model="ruleForm.comentario"
                  maxlength="1000"
                  show-word-limit
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="8">
              <el-form-item>
                <el-button type="primary" @click="submitComent('ruleForm')"
                  >Guardar</el-button
                >
              </el-form-item>
            </el-col>
          </el-form>
        </el-row>
            <el-row :gutter="20" class="mt-2">
              <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="12">
                  <el-upload
                    class="upload-demo"
                    :action="'/upload'"
                    name="file[]"
                    :data="{
                      id_documento: datacoment.idDocumento,
                      correlativo: datacoment.correlativo,
                      count: numberFiles,
                    }"
                    :headers="{ 'X-CSRF-TOKEN': csrf }"
                    :on-preview="handlePreview"
                    :on-remove="handleRemove"
                    :show-file-list="false"
                    :on-success="cargaSuccess"
                    multiple
                    :limit="limitNumber"
                    :on-exceed="handleExceed"
                    :file-list="fileList"
                    accept=".pdf"
                  >
                    <el-button size="small" type="primary"
                      >Clic para subir archivo</el-button
                    >
                    <div slot="tip" class="el-upload__tip">
                      Solo archivos pdf con un tamaño menor de 500kb
                    </div>
                  </el-upload>
              </el-col>
              <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="12">
                  <el-divider direction="vertical"></el-divider>
                  <el-button
                    @click="drawer = true"
                    type="primary"
                    style="margin-left: 16px"
                  >
                    Visualizar Documento
                  </el-button>
                  <el-drawer
                    title="Documento"
                    :visible.sync="drawer"
                    :with-header="false"
                    :modal="false"
                  >
                    <embed :src="src" type="application/pdf" width="100%" height="100%" />
                  </el-drawer>
              </el-col>
            </el-row>
      </el-dialog>
    </div>
  </div>
</template>


<style>
.el-table .warning-row {
  background: oldlace;
}

.el-table .success-row {
  background: #f0f9eb;
}

.formComentario {
  width: 100%;
}
</style>

<script>
export default {
  props: { csrf: { type: String } },
  data() {
    return {
      drawer: false,
      limitNumber: 1,
      numberFiles: 0,
      fileList: [],
      datacoment: {
        idDocumento: "",
        idTraslado: "",
        correlativo: "",
      },
      ruleForm: {
        comentario: "",
      },
      url_list: {
        lista: "lista",
        dependencias: "dependencias",
        trasladar: "retornar",
        message: "getMessage",
        toAccept: "Aceptar",
        getUser: "usuarios",
        TrasladoInterno: "TrasladoInterno",
        info: "infoPDF",
        comentario: "getComentario",
        setComentario: "setComentario",
        closeDocumento: "closeDocumento",
        uploadFile: "Uploadfile",
        getFiles: "getNameFiles",
      },
      list_response: {
        documentos: [],
        list_dependencia: [],
        list_user: [],
        listcomentarios: [],
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
      form: {
        departamentoId: "",
        usuario: "",
      },
      formExterno: {
        lugar:"",
        correlativo:""
      },
      formUser: {
        usuario: "",
      },
      formClose: {
        comentarioCierre: "",
      },
      rules: {
        comentario: [
          {
            require: true,
            message: "Ingrese comentario",
            trigger: "blur",
          },
        ],
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
          title: "Visualizar Documento",
          visible: false,
          width: "50%",
          top: "3vh",
          ver: false,
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
      Accept: 2,
    };
  },
  mounted() {
    this.getLista();
    this.selectDireccion();
    this.getUserTransfer();
  },
  methods: {
    submitComent(form) {
      const h = this.$createElement;
      this.$refs[form].validate((valid) => {
        if (valid) {
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
                this.$message({
                  message: h("p", null, [
                    h("i", { style: "color: teal" }, "Agregado!"),
                  ]),
                  type: "success",
                });
                this.ComentLoading = false;
                this.$refs[form].resetFields();
                // this.handlerDialog.preview.visible = false;

                this.getComentario(
                  this.datacoment.idTraslado,
                  this.datacoment.idDocumento
                );
              }
            });
        }
      });
    },

    archivarDocument(form) {
      const h = this.$createElement;
      if (this.formClose.comentarioCierre != "") {
        console.log("adentro");
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
    getLista() {
      axios.get(this.url_list.message).then((response) => {
        this.list_response.documentos = response.data;
        this.total = response.data.length;
        console.log(response.data);
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
    getTrasladoInterno(id, traslado) {
      this.interno = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.getComentario(traslado, id);
    },

    getTrasladoExterno(id, traslado) {
      this.externo = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.getComentario(traslado, id);
    },

    cierreDocumento(id, traslado) {
      this.handlerDialog.previewClose.visible = true;
      this.idDocumento = id;
      this.depActual = traslado;
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
              externo: false
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
              correlativoEx: this.formExterno.correlativo
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
    preview(code, traslado, correlativo) {
      this.getComentario(traslado, code);
      this.handlerDialog.preview.visible = true;
      this.datacoment.idDocumento = code;
      this.datacoment.idTraslado = traslado;
      this.datacoment.correlativo = correlativo;
      this.getNameFiles(correlativo);
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
      this.$message.warning(
        `El límite es 1, haz seleccionado ${
          files.length
        } archivos esta vez, añade hasta ${files.length + fileList.length}`
      );
    },
    cargaSuccess(res, file, fileList) {
      const _this = this;
      if (res.success === false) {
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
        this.src = './../files/' + response.data[0].name + '.pdf'; 
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
            this.numberFiles = response.data.length;
            this.src = './../files/' + response.data[0].name + '.pdf'; 
          }
        });
    },
  },
};
</script>