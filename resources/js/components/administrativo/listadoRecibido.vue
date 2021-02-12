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
        <el-table-column prop="estado" label="Etiqueta" width="100">
          <template slot-scope="scope">
            <div v-if="scope.row.estado === 9">
              <el-tag
                :type="scope.row.estado === '9' ? 'primary' : 'warning'"
                disable-transitions
                >Externo
              </el-tag>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Operaciones" width="280">
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
                    scope.row.formato
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
                  getTrasladoInterno(scope.row.code, scope.row.idTraslado,scope.row.idTracing)
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
                @click="cierreDocumento(scope.row.code, scope.row.idTraslado,scope.row.formato)"
              ></el-button>
              <el-switch 
                v-model="scope.row.tracing"
                active-value="1"
                inactive-value="0"
                active-color="#13ce66"
                inactive-color="#ff4949"
                @change="switchControl(scope.row.tracing,scope.row.code,scope.row.idTracing)">
              </el-switch>
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
              >Trasladar
            </el-button>
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
          <el-form-item >
              <el-col :span="3">
                  <el-form-item v-show="controlButton.buttonPdfClose">
                    <el-upload
                            class="upload-demo"
                            :action="'/upload'"
                            name="file[]"
                            :data="{
                                id_documento: datacoment.idDocumento,
                                correlativo: datacoment.correlativo,
                                count: datacoment.numberFiles,
                                type: datacoment.typePdf,
                            }"
                            :headers="{ 'X-CSRF-TOKEN': csrf }"
                            :on-preview="handlePreview"
                            :on-remove="handleRemove"
                            :show-file-list="false"
                            :on-success="cargaSuccessClose"
                            :file-list="fileList"
                            accept=".pdf"
                    >
                        <el-button size="medium" type="danger" 
                            ><i class="fas fa-file-pdf"></i
                        ></el-button>
                    </el-upload>
                  </el-form-item>
              </el-col>
              <el-col :span="5">
                  <el-form-item>
                    <el-button
                    v-if="controlButton.buttonClose"
                    type="primary"
                    @click="archivarDocument('formClose')"
                    v-loading.fullscreen.lock="trasladoUsuario"
                    >Archivar
                    </el-button>  
                  </el-form-item>
              </el-col>
              <el-col :span="11">
                  <el-form-item>
                    <el-button  @click="cierreClose('formClose')">Cancel</el-button>
                  </el-form-item>
              </el-col>
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
        <el-form
          :inline="false"
          :model="formExterno"
          ref="formExterno"
          label-width="150px"
        >
          <el-form-item label="Lugar:">
            <el-input v-model="formExterno.lugar"></el-input>
          </el-form-item>
          <el-form-item label="Correlativo:">
            <el-input v-model="formExterno.correlativo"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button
              type="primary"
              @click="transferExterno()"
              v-loading.fullscreen.lock="trasladoUsuario"
              >Trasladar
            </el-button>
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
              >Trasladar
            </el-button>
            <el-button @click="interno = false">Cancel</el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
      <el-dialog
        :title="handlerDialog.preview.title"
        :visible.sync="handlerDialog.preview.visible"
        :width="handlerDialog.preview.width"
        :top="handlerDialog.preview.top"
        @close="closeEvent()"
        @open="openEvent()"
        center
        destroy-on-close
      >
        <el-row :gutter="10">
          <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24">
            <el-table
              :data="list_response.listComentarios"
              :row-class-name="tableRowClassName"
              height="450"
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
                  >Guardar
                </el-button>
              </el-form-item>
            </el-col>
          </el-form>
        </el-row>
        <el-row :gutter="20" class="mt-2">
          <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="2">
            <el-upload
              class="upload-demo"
              :action="'/upload'"
              name="file[]"
              :data="{
                id_documento: datacoment.idDocumento,
                correlativo: datacoment.correlativo,
                count: datacoment.numberFiles,
                type: datacoment.typePdf,
              }"
              :headers="{ 'X-CSRF-TOKEN': csrf }"
              :on-preview="handlePreview"
              :on-remove="handleRemove"
              :show-file-list="false"
              :on-success="cargaSuccess"
              :file-list="fileList"
              accept=".pdf"
            >
              <el-button size="medium" type="danger"
                ><i class="fas fa-file-pdf"></i
              ></el-button>
            </el-upload>
          </el-col>
          <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="2">
            <el-upload
              class="upload-demo"
              :action="'/uploadWord'"
              name="file[]"
              :data="{
                id_documento: datacoment.idDocumento,
                correlativo: datacoment.correlativo,
                count: datacoment.numberFiles,
                type: datacoment.typeWord,
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
              accept=".docx,.doc"
            >
              <el-button size="medium" type="primary"
                ><i class="fas fa-file-word"></i
              ></el-button>
            </el-upload>
          </el-col>
          <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="12">
            <el-divider direction="vertical"></el-divider>
            <el-button
              @click="verDocumento(true, 'pdf')"
              v-if="controlButton.buttonPdf"
              type="danger"
              :icon="controlButton.icon"
              :disabled="controlButton.disabled"
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
              <embed
                :src="src"
                type="application/pdf"
                width="100%"
                height="100%"
              />
            </el-drawer>
            <el-divider direction="vertical"></el-divider>

            <el-link
              :href="documentWord.url"
              :underline="false"
              v-if="controlButton.buttonWord"
            >
              <el-button type="primary" icon="el-icon-download"> </el-button>
            </el-link>
          </el-col>
        </el-row>
        <el-row :gutter="20" class="mt-2">
          <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24">
            <div>Solo archivos PDF y WORD con un tamaño menor de 500kb</div>
          </el-col>
        </el-row>
      </el-dialog>
    </div>
    <el-dialog
      :title="message.title"
      :visible.sync="message.visible"
      :width="message.width"
      center
      destroy-on-close
      :close-on-click-modal="message.closeModal"
      :close-on-press-escape="message.closeModal"
      @close="closeDate">

      <div class="block">
        <b><span class="textBlock">Fecha Limite para Seguimiento:</span></b>
        <el-date-picker
          class="select_width"
          v-model="message.dialog.fecha.vModelSeguimiento"
          type="datetime"
          :placeholder="message.dialog.fecha.PlaceHolder"
          default-time="12:00:00"
          format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" 
          @change="confirm()">
        </el-date-picker>
      </div>
      

      <span slot="footer" class="dialog-footer">
        <el-button @click="closeDate">Cancelar</el-button>
        <el-button type="primary" @click="setActiveMonitoring" :disabled="message.button">Activar</el-button>
      </span>
    </el-dialog>
  </div>
</template>


<style>
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

.block{
 padding: 30px 0;
 text-align: left;
 flex: 1; 
}

.textBlock{
  display: block;
  font-size: 14px;
  margin-bottom: 20px;
}
</style>
<!--this.src = './../files/' + response.data[0].name + '.pdf';-->
<script>
export default {
  props: { csrf: { type: String } },
  data() {
    return {
      message: {
        title:"",
        visible: false,
        width: "",
        bodyText: "",
        closeModal: false,
        button:true,
        dialog: {
          fecha: {
            vModelSeguimiento: '',
            type: "datatime",
            PlaceHolder: "Ingrese fecha de seguimiento",
            defaultTime: "12:00:00",
            file:"",
            tracing:""
          }
        }
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
        buttonPdfClose:true,
      },
      fileList: [],
      datacoment: {
        idDocumento: "",
        idTraslado: "",
        correlativo: "",
        typePdf: "pdf",
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
        url: "url",
        exists: "exists",
        tracing:"tracingsFiles",
        inactiveTracingFile:"inactiveTracingFile"
      },
      list_response: {
        documentos: [],
        list_dependencia: [],
        list_user: [],
        listcomentarios: [],
        listUrl: [],
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
          top: "2vh",
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
    closeDate(){
      this.message.visible = false;
      this.message.dialog.fecha.vModelSeguimiento = "";
      this.getLista();
    },
    setActiveMonitoring(){
        axios.post(this.url_list.tracing,{
          documento: this.message.dialog.fecha.file,
          tracing: this.message.dialog.fecha.tracing,
          fechaF: this.message.dialog.fecha.vModelSeguimiento
        }).then(response => {
          this.closeDate();
          // this.getLista();
        })
    },
    confirm(){
      this.message.button = false;
    },
    switchControl(flag,file,tracing) {
      const h = this.$createElement;
      
      
      if(flag == "1"){

        this.message.title = 'Seguimiento de Expediente';
        this.message.visible = true;
        this.message.width = '30%';
        this.message.dialog.fecha.file = file;
        this.message.dialog.fecha.tracing = tracing;

      }else{
        this.$confirm('¿Desea inactivar el seguimiento de expediente?','Seguimiento',{
          confirmButtonText: 'Inactivar',
          cancelButtonText: 'Cancelar',
          type: 'Warning'
        }).then(() => {
          axios.post(this.url_list.inactiveTracingFile,{
            documento: file,
            tracing: tracing
          }).then(response => {
            this.getLista();
          })
        }).catch(() => {
          this.getLista();
        })
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
            // console.log("comentarios", response.data)
            this.list_response.listComentarios = response.data;
            this.total = response.data.length;
          }
        });
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
    getTrasladoInterno(id, traslado,tracing) {
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
    cierreDocumento(id, traslado,formato) {
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
    preview(code, traslado, correlativo) {
      // console.log("code: ", code, " traslado: ", traslado, " correlativo: " , correlativo);
      console.log(code);

      this.getComentario(traslado, code);
      this.handlerDialog.preview.visible = true;
      this.datacoment.idDocumento = code;
      this.datacoment.idTraslado = traslado;
      this.datacoment.correlativo = correlativo;

      this.getNameFiles(code);
    },
    closeEvent() {
      this.controlButton.buttonWord = false;
      this.controlButton.buttonPdf = false;
      this.src = "";
      this.documentWord.url = "";
    },
    openEvent() {
      axios
        .post(this.url_list.exists, {
          id: this.datacoment.idDocumento,
          formato: "word",
        })
        .then((response) => {
          const result = response.data;
          if (result != false) {
            this.documentWord.url = "./../files/" + response.data[0].file;
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
        console.log(res[0][0].file);
        if (res[0][0].format == "pdf") {
          this.src = "./../files/" + res[0][0].file;
          this.controlButton.buttonPdf = true;
        } else {
          this.controlButton.buttonWord = true;
          this.documentWord.url = "./../files/" + res[0][0].file;
        }
      }
    },
    cargaSuccessClose(res,file,filters){
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
