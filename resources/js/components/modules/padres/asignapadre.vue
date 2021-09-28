
<template>




<div class="card">
  <div class="card-header text-white bg-primary">
    Listado de documentos por agrupar
  </div>
  <div class="card-body">
    <el-row :gutter="10">

     <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
       <div class="grid-content ">
         Seleccione el Agrupador y presione el boton Agrupar
         <el-autocomplete
            class="inline-input"
            v-model="state1"
            :fetch-suggestions="querySearch"
            placeholder="Please Input"
            @select="handleSelect">
            <el-button class="azul" slot="append" icon="el-icon-guide"></el-button>
          </el-autocomplete>   
       </div>
      </el-col>
      <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
        <div class="grid-content">
          Seleccione el agrupador y presione el boton de ordenar
           <el-autocomplete
            class="inline-input"
            v-model="state2"
            :fetch-suggestions="querySearch"
            placeholder="Please Input"
            @select="handleSelect2">
            <el-button class="azul" slot="append" icon="search"></el-button>
          </el-autocomplete>   
        </div>
      </el-col>    
    </el-row>  
          
  
          
        
     
            
           
            
        <el-divider></el-divider>  
       
    
      <!--
      <div style="margin-top: 15px;">
            <el-input placeholder="Please input" v-model="input3" class="input">
                
                <el-button slot="append" icon="el-icon-search"></el-button>
            </el-input>
        </div>
      
        <el-divider><i class="el-icon-search"></i></el-divider>  
  -->
    <el-table :data="
          list_response.documentos
            .filter(
              (data) =>
                !search ||
                data.empresa.toLowerCase().includes(search.toLowerCase()) ||
                data.correlativo.toLowerCase().includes(search.toLowerCase()) ||
                data.formato.toLowerCase().includes(search.toLowerCase())
            )

        " :header-cell-style="tableHeaderColor" :cell-class-name="celdas" border style="width: 100%"
        @selection-change="handleSelectionChange">
       
       

      <el-table-column label="Agrupar" width="75">
       <el-table-column type="selection" width="150"></el-table-column> 
        <template slot-scope="scope" class="pl-3" align="center">
          <div>  
            <el-switch  align="center" v-if="scope.row.flag === 'true' && scope.row.tracing === '1'" v-model="scope.row.tracing" active-value="1" inactive-value="0" active-color="#13ce66" inactive-color="#ff4949" @change="
                  switchControl(
                    scope.row.tracing,
                    scope.row.code,
                    scope.row.idTracing
                  )
                ">
            </el-switch>
            <el-switch v-else-if="scope.row.flag === 'false' && scope.row.tracing === '1'" v-model="scope.row.tracing" active-value="1" inactive-value="0" active-color="#13ce66" inactive-color="#ff4949" disabled @change="
                  switchControl(
                    scope.row.tracing,
                    scope.row.code,
                    scope.row.idTracing
                  )
                ">
            </el-switch>
            <el-switch v-else v-model="scope.row.tracing"                                                               active-value="1" inactive-value="0" active-color="#13ce66" inactive-color="#ff4949" @change="
                  switchControl(
                    scope.row.tracing,
                    scope.row.code,
                    scope.row.idTracing
                  )
                ">
            </el-switch>

          </div>
        </template>
      </el-table-column>  
      <el-table-column label="No." type="index"></el-table-column>
      <el-table-column label="Remitente" prop="empresa"></el-table-column>
      <el-table-column label="Correlativo" prop="correlativo"></el-table-column>
      <el-table-column label="Correlativo Interno" prop="formato"></el-table-column>
      <el-table-column label="Asunto" width="500" prop="descripcion"></el-table-column>
      <el-table-column label="Fecha" width="90" prop="fecha"></el-table-column>
     <!-- <el-table-column prop="estado" label="Etiqueta  /  Restante" width="92" align="center">
      
        <template slot-scope="scope">
          <div v-if="scope.row.estado === 9">
            <el-tag :type="scope.row.estado === '9' ? 'primary' : 'warning'" disable-transitions>Externo
            </el-tag>
          </div>
        </template>
        <template slot-scope="scope" v-if="scope.row.tracing === '1'">
          {{ scope.row.dias }} dias
        </template>
      </el-table-column> -->
      <el-table-column prop="agrupador" label="Agrupador" width="90" align="center">
        
        
            
          

      </el-table-column>
      <el-table-column label="Operaciones" width="75">
        <template slot="header" slot-scope="scope">
          <el-input v-model="search" size="mini" placeholder="Buscar" />
        </template>
        <template slot-scope="scope" class="pl-3">
         
         
          <div>
            <el-popover placement="top-start" title="Comentario" width="200" trigger="hover" content="Visualiza el documento y agrega comentarios">
              <el-button slot="reference" type="danger" size="mini" icon="el-icon-s-comment" plain @click="
                    preview(
                      scope.row.code,
                      scope.row.idTraslado,
                      scope.row.formato,
                      scope.row.url
                    )
                  "></el-button>
            </el-popover>

          </div>
        </template>
      </el-table-column>
    </el-table>
    <!-- <div style="text-align: left; margin-top: 30px">
        <el-pagination
          background
          layout="total,prev, pager, next"
          :total="total"
          @current-change="current_change"
        ></el-pagination>
      </div> -->



    <!-- asignacion de padre / agrpador -->
    <el-dialog title="Asignación de Agrupador" :visible.sync="agrupador" width="35%" top="3vh" center :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" destroy-on-close @close="closeAgrupador">
      <el-form :inline="false" :model="formPadre" ref="formPadre" label-width="150px">
        <el-form-item label="Agrupador:" prop="agrupador">
          <el-select v-model="formPadre.id" class="select_width" clearable filterable placeholder="Seleccione Agrupador">
            <el-option v-for="items in list_response.list_padres" :key="items.id" :label="items.descripcion" :value="items.id"></el-option>
          </el-select>
        </el-form-item>
       
        <el-form-item>
          <el-button type="primary" @click="asignaPadre('formPadre')" v-loading.fullscreen.lock="trasladoUsuario">Agrupar
          </el-button>
          <el-button @click="agrupador = false">Cancel</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- fin de asignacion de padre /agrupador -->


    <el-dialog :title="handlerDialog.preview.title" :visible.sync="handlerDialog.preview.visible" :width="handlerDialog.preview.width" :top="handlerDialog.preview.top" @close="closeEvent()" @open="openEvent()" @opened="opened" destroy-on-close>
      
      <el-row :gutter="10">
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
            <div v-show="handlerFile.dialogComent">
              <RotateSquare2></RotateSquare2>
            </div>
            <!-- <b-alert v-show="handlerFile.showError" show variant="danger">Sin documento Adjunto</b-alert> -->

            <el-table v-if="handlerFile.tableComent" :data="list_response.listcomentarios" :header-cell-style="tableComment" :row-class-name="tableRowClassName" height="450" border>
              <el-table-column label="No." type="index"></el-table-column>
              <el-table-column label="Usuario" prop="usuario" width="180"></el-table-column>
              <el-table-column label="Comentario" prop="comentario"></el-table-column>
            </el-table>

          

          <el-button v-if="controlButton.buttonWord" type="primary" @click="filesWord()">Archivos</el-button>
        </el-col>
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
          <div v-show="handlerFile.showLoading">
            <RotateSquare2></RotateSquare2>
          </div>
          <b-alert v-show="handlerFile.showError" show variant="danger">Sin documento Adjunto</b-alert>
          <div class="reload-div pb-3" v-show="handlerFile.showPdf">
            <el-link type="primary" :underline="false" @click="reload">
              <i class="fas fa-sync reload"></i>
            </el-link>
          </div>
          <embed :src="url" type="application/pdf" width="100%" height="600" ref="viewPDf" v-show="handlerFile.showPdf" />

        </el-col>
      </el-row>
      <el-dialog :width="handlerDialog.inner.width" :title="handlerDialog.inner.title" :visible.sync="handlerDialog.inner.innerVisible" append-to-body>
        <el-row :gutter="10">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <el-table :data="list_response.getFileWord" :header-cell-style="tableComment" height="450" border>
              <el-table-column label="No." type="index"></el-table-column>
              <el-table-column label="Archivo" prop="file"></el-table-column>
              <el-table-column label="Fecha" prop="fecha" width="150"></el-table-column>
              <el-table-column label="Operaciones" width="100" align="center">
                <template slot-scope="scope">
                  <el-link :href="scope.row.url" :underline="false"><i class="donwloadFile fas fa-download"></i></el-link>
                </template>
              </el-table-column>
            </el-table>
          </el-col>
        </el-row>
      </el-dialog>
    </el-dialog>
  </div>
</div>
</template>

<style>
.el-table--border th, .el-table__fixed-right-patch {
    border-bottom: 1px solid #009879!important;
}
.el-table thead.is-group th {
    background: #009879!important;
}
.azul{
    background-color: #409EFF;
}
.doc {
  width: 100%;
  height: 500px;
}
.el-autocomplete {
   
    display: contents!important;
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
<!--this.src = './../files/' + response.data[0].name + '.pdf';

../modules/dialogos/expedientes.vue
-->

<script>
import dialogExpediente from '../dialogos/expedientes.vue'
import {
  RotateSquare2,
  RotateSquare5
} from "vue-loading-spinner";
import JQuery from 'jquery'
import moment from "moment";
export default {
  components: {
    RotateSquare2,
    RotateSquare5,
    dialogExpediente

  },
  props: {
    csrf: {
      type: String
    }
  },
  data() {
    return {
       input1: '',
        input2: '',
        input3: '',
        select: '',
        links: [],
        state1: '',
        state2: '', 
      publicPath: __dirname,
      event_at: "",
      handlerFile: {
        showPdf: false,
        showLoading: true,
        showError: false,
        code: "",
        dialogComent: false,
        tableComent: false
        
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
          instruccion: ""
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
        code: ""
      },
      formInstrucciones: {
        instruccion: "",
        ministro: "",
        viceministerio: "",
        cc: []
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
        asignapadres: "asignaPadre"
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
        prueba: []
      },
      total: 0,
      currentPage: 1,
      pagesize: 10,
      EditscreenLoading: false,
      dialogo: false,
      interno: false,
      agrupador:false,
      externo: false,
      agrupadorfrm:false,
      trasladoUsuario: false,
      idDocumento: 0,
      depActual: 0,
      idTracing: 0,
      form: {
        departamentoId: "",
        usuario: "",
        cc: []
      },
      formExterno: {
        lugar: "",
        correlativo: "",
      },
       formPadre: {
         iddocumento:"",
        descripcion: "",
        id: ""
      },
      formUser: {
        usuario: "",
      },
      formClose: {
        comentarioCierre: "",
      },
      formRule: {
        comentario: [{
          require: true,
          message: "Ingrese comentario",
          trigger: "blur",
        }, ],
      },
      rules: {
        departamentoId: [{
          require: true,
          message: "Seleccione dirección de traslado",
          trigger: "blur",
        }, ],
        usuario: [{
          require: true,
          message: "Seleccione dirección de traslado",
          trigger: "blur",
        }, ],
      },
      rulesClose: {
        comentarioCierre: [{
          require: true,
          message: "Ingrese el comentario",
          trigger: "blur",
        }, ],
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
          img: './imagenes/gobierno.jpg',
          img2: '/imagenes/gobierno.jpg',
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
          cc:""
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
    this.event_at = moment(new Date()).format('D/MM/YYYY');
    this.getListaVice();
    // this.links = this.getAgrupador();
    // this.links = this.loadAll();

    this.getAgrupador();

  },
  methods: {
    querySearch(queryString, cb) {
      var links = this.links;
        var results = queryString ? links.filter(this.createFilter(queryString)) : links;
        // console.log("link data",results)
        // call callback function to return suggestions
        cb(results);
      },  
      createFilter(queryString) {
        // console.log("query",queryString)
        return (link) => {
          return (link.descripcion.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
        };
      },
      
      handleSelect(item) {
        console.log(item);
      },
       handleSelect2(item) {
        console.log(item);
      },
    handleSelectionChange(val) {
          console.log(val)
        this.multipleSelection = val;
      },
    handleSelectionChange2(val) {
        console.log(val)
      this.multipleSelection = val;
    },
    getListaVice() {
      axios.get(this.url_list.listaVice)
        .then(response => {
          this.list_response.listaVice = response.data
        })
    },
    imprimir() {
      axios.post(this.url_list.makeBoleta, {
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
          cc: this.handlerDialog.boleta.cc
        }, {
          responseType: 'blob'
        })
        .then(response => {
          var fileURL = window.URL.createObjectURL(new Blob([response.data]));
          console.log(fileURL);
          var fileLink = document.createElement('a');
          fileLink.href = fileURL;
          fileLink.setAttribute('download', 'file.pdf');
          document.body.appendChild(fileLink);
          fileLink.click();
        })

    },
    celdas({
      row,
      column,
      rowIndex,
      columnIndex
    }) {
      if (columnIndex === 6) {
        console.log(row.tracing)
        if (row.tracing === "1") {
          if (row.dias > 0) {
            return "bg-success text-white";
          } else {
            return "bg-danger text-white";
          }
        }
      }
    },
    reload() {
      this.handlerFile.showLoading = true
      this.handlerFile.showPdf = false
      this.getFileCopies(this.handlerFile.code);
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
    tableComment({
      row,
      column,
      rowIndex,
      columnIndex
    }) {
      if (rowIndex === 0) {
        return "background-color: #2c3c5c;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    tableHeaderColor({
      row,
      column,
      rowIndex,
      columnIndex
    }) {
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
          cc:this.formInstrucciones.cc
        })
        .then((response) => {
          console.log("respuesta", response)
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
            "Seguimiento", {
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
                  h("i", {
                    style: "color: teal"
                  }, "Agregado!"),
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
                  h("i", {
                    style: "color: teal"
                  }, "Documento Archivado!"),
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
            console.log("Comentarios: ", response.data);
            this.total = response.data.length;
          }
        });
    },
    getAgrupador() {

      axios
        .get(this.url_list.padres)
        .then((response) => {
          const status = JSON.parse(response.status);
          const result = response.data;
          console.log("get agrupador json");
          //console.log(status);
          if (status == "200") {
            // console.log("comentarios", response.data)
            this.links= response.data;
            // console.log(this.links);
            // console.log("Padres: ", response.data);
            // console.log(this.list_response.list_padres);
            // this.total = response.data.length;
          }

          // return this.links;
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
    getTrasladoInterno(id, traslado, tracing) {
      this.interno = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.idTracing = tracing;
      this.getComentario(traslado, id);
    },

    getPadreAgrupador(documentox) {
      this.agrupador = true;
      this.formPadre.iddocumento = documentox
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

      axios.post(this.url_list.getSeguimientoDocumento, {
          code: code
        })
        .then(response => {
          console.log(response.data)
          this.handlerDialog.boleta.instrucciones_generales = response.data[0]['instruccion'];
          this.handlerDialog.boleta.instrucciones_ministro = response.data[0]['instruccion_ministro'];
          this.handlerDialog.boleta.fechaInicio = response.data[0]['fechaInicial'];
          this.handlerDialog.boleta.fechafin = response.data[0]['fechaFinal'];
          this.handlerDialog.boleta.viceministerio = response.data[0]['viceministerio'];
          this.handlerDialog.boleta.cc = response.data[0]['cc'];
        })

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
              copy: this.form.cc
            })
            .then((response) => {
              this.trasladoUsuario = false;
              this.interno = false;
              this.getLista();
              console.log("transfer true", response.data)
            })
            .catch(error => {
              console.log("errror true", error);
            })
        }
      });
    },
    asignaPadre(form) {
      const h = this.$createElement;
      this.$refs[form].validate((valid) => {
        console.log(this.formPadre.iddocumento);
        console.log(this.formPadre.id);

        if (valid) {
          this.agrupador = true;
          axios
            .put(this.url_list.asignapadres, {
              id_documento: this.formPadre.iddocumento,
              id: this.formPadre.id,
              
            
            })
            .then((response) => {
              const status = response.status
               if (status == "200") {
                 
                //  console.log("200");
                /*this.$alert('<strong>Operación exitosa </strong>', 'Agrupación. ', {
                  dangerouslyUseHTMLString: true
                });*/
                 this.$swal({
                  icon: "success",
                  title: "Asignación exitosa!",
                  showConfirmButton: false,
                  timer: 2500,
                });
                this.agrupador = false;
                this.getLista();
/*
                this.$notify.success({
                  title: 'Agrupado',
                  message: 'Operación exitosa',
                  offset: 100
                });*/
/*
                 this.$message({
                    message: h("p", null, [
                      h("i", {
                        style: "color: teal"
                      }, "Operación exitosa"),
                    ]),
                    type: "success",
                  });*/
                  // this.getLista();
               }
            })
            .catch(error => {
              console.log("errror true", error);
            })
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
                h("i", {
                  style: "color: teal"
                }, "Operación exitosa"),
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
    tableRowClassName({
      row,
      rowIndex
    }) {
      if (rowIndex % 2 == 0) {
        return "warning-row";
      } else {
        return "success-row";
      }
      return "";
    },
    preview(code, traslado, correlativo, url) {
      console.log(
        "code: ",
        code,
        " traslado: ",
        traslado,
        " correlativo: ",
        correlativo
      );
      // console.log(code);

      this.handlerDialog.preview.title = "Expediente No. " + correlativo;
      this.ruleForm.id_traslado = traslado;
      this.ruleForm.code = code;

      this.handlerDialog.preview.visible = true;
      this.datacoment.idDocumento = code;
      this.datacoment.idTraslado = traslado;
      this.datacoment.correlativo = correlativo;
      this.url = url;
      this.handlerDialog.inner.codeSearch = code;
      this.handlerFile.showLoading = false
      this.handlerFile.showPdf = true
      this.handlerFile.code = code
      // this.viewFile("primero");
      // this.getNameFiles(code);
    },

    getFileCopies(code) {

      axios.post(this.url_list.getPdfFiles, {
          document: code
        })
        .then(response => {
          console.log("file", response.data)
          if (response.data.length > 0) {
            this.url = './../files/' + response.data[0].file
            this.handlerFile.showPdf = true
            this.handlerFile.showLoading = false
          } else {
            this.handlerFile.showPdf = false
            this.handlerFile.showLoading = false
            this.handlerFile.showError = true
          }
        })
    },
    closeEvent() {
      this.controlButton.buttonWord = false;
      this.controlButton.buttonPdf = false;
      this.src = "";
      this.documentWord.url = "";
      this.ruleForm.comentario = "";
      this.url = "";
      this.handlerFile.showPdf = false
      this.handlerFile.showLoading = true
      this.handlerFile.showError = false
      this.ruleForm.id_traslado = ""
      this.ruleForm.code = ""
      this.list_response.listcomentarios = []
      this.handlerFile.dialogComent = false
      this.handlerFile.tableComent = false
    },
    formCloseDialog() {
      this.formClose.comentarioCierre = ""
      this.controlButton.buttonPdfClose = true
      this.datacoment.idDocumento = ""
      this.datacoment.correlativo = ""
    },
    closeTrasladoInterno() {
      this.form.usuario = ""
      this.form.cc = []
    },
    closeAgrupador() {
      this.formPadre.descripcion = ""
      this.formPadre.id=""
      this.agrupador=false
    },
    opened() {
      
      this.handlerFile.dialogComent = true
      
      axios
        .post(this.url_list.comentario, {
          code: this.ruleForm.id_traslado,
          documento: this.ruleForm.code,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.handlerFile.dialogComent = false
            this.handlerFile.tableComent = true
            // console.log("comentarios", response.data)
            this.list_response.listcomentarios = response.data;
            console.log("Comentarios: ", response.data);
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
