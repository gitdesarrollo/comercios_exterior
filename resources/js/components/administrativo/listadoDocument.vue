<template>
  <div class="card">
    <div class="card-header text-white bg-primary">Listado de Documentos</div>
    <div class="card-body">
      <el-table
        :data="list_response.documentos.slice((currentPage - 1) * pagesize, currentPage * pagesize)"
        style="width:100%"
      >
        <el-table-column label="No." type="index"></el-table-column>
        <el-table-column label="Empresa" width="300" prop="empresa"></el-table-column>
        <el-table-column label="Correlativo" width="150" prop="correlativo"></el-table-column>
        <el-table-column label="Direccón" prop="descripcion"></el-table-column>
        <el-table-column label="Fecha" prop="fecha">
          <template slot-scope="scope">
            <i class="el-icon-time"></i>
            <span style="margin-left: 10px">{{ scope.row.fecha }}</span>
          </template>
        </el-table-column>
        <!-- <el-table-column label="Estado"  prop="estado"></el-table-column> -->
        <el-table-column label="Operaciones" width="180">
          <template slot-scope="scope" class="pl-3">
            <el-button
              type="danger"
              size="mini"
              icon="el-icon-s-comment"
              plain
              @click="preview(scope.row.code,scope.row.idTraslado)"
            ></el-button>
            <!-- v-if="trasladarBtn === scope.row.estado" -->
            <!-- <el-button
              size="mini"
              type="primary"
              icon="el-icon-right"
              plain
              @click="getTraslado(scope.row.code,scope.row.id_dependencia)"
            ></el-button>-->
            <!-- <el-button
              size="mini"
              type="primary"
              icon="el-icon-s-check"
              plain
              @click="getTraslado(scope.row.code,scope.row.id_dependencia)"
            ></el-button>-->
          </template>
        </el-table-column>
      </el-table>
      <div style="text-align: left;margin-top: 30px;">
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
        <el-form :inline="false" :model="form" ref="form" :rule="rules" label-width="150px">
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
            >Trasladar</el-button>
            <el-button @click="dialogo = false">Cancel</el-button>
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
              <el-table-column label="Usuario" prop="usuario" width="250"></el-table-column>
              <el-table-column label="Comentario" prop="comentario"></el-table-column>
            </el-table>
          </el-col>
          <!-- <embed :src="src" type="application/pdf" width="90%" height="600px" /> -->
          <!-- <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="9">
          </el-col>-->
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
import jsPDF from "jspdf";
import html2canvas from "html2canvas";

import html2pdf from "html2pdf.js";
export default {
  data() {
    return {
      datacoment: {
        idDocumento: "",
        idTraslado: "",
      },
      url_list: {
        lista: "listDocumentAll",
        dependencias: "dependencias",
        trasladar: "Trasladar",
        info: "infoPDF",
        comentario: "getComentario",
      },
      list_response: {
        documentos: [],
        list_dependencia: [],
        listInfo: [],
        listComentarios: [],
      },
      total: 0,
      currentPage: 1,
      pagesize: 10,
      EditscreenLoading: false,
      dialogo: false,
      idDocumento: 0,
      depActual: 0,
      trasladarBtn: "Sin Enviar",
      form: {
        departamentoId: "",
      },
      rules: {
        departamentoId: [
          {
            require: true,
            message: "Seleccione dirección de traslado",
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
      },
      currentPagePDF: 0,
      numPages: undefined,
      pageCount: 0,
      src: "/pdf/1.pdf",
      show: true,
    };
  },
  mounted() {
    this.getLista();
    this.selectDireccion();
  },
  methods: {
    createPDF() {
      const doc = new jsPDF();
      doc.fromHTML(document.getElementById("mypdf"), 15, 15, {
        width: 170,
      });

      doc.save("hola.pdf");
      // doc.html('<p>hola</p>', {
      // callback: function (doc) {
      //   doc.save();
      //   },
      //   x: 10,
      //   y: 10
      // });
      // doc.fromHTML(document.getElementById("mypdf"), 20, 20, { width: 500 }); //<-- not good. How can I refactor this?
      // doc.save("mypdf.pdf");

      /** WITH CSS */
      // var canvasElement = document.createElement("canvas");
      // html2canvas(this.$refs.content, { canvas: canvasElement }).then(function (
      //   canvas
      // ) {
      //   const img = canvas.toDataURL("image/jpeg", 0.8);
      //   doc.addImage(img, "JPEG", 20, 20);
      //   doc.save("sample.pdf");
      // });
    },
    tableRowClassName({ row, rowIndex }) {
      if (rowIndex % 2 == 0) {
        return "warning-row";
      } else {
        return "success-row";
      }
      return "";
    },
    getLista() {
      axios.get(this.url_list.lista).then((response) => {
        this.list_response.documentos = response.data;
        this.total = response.data.length;
        console.log(response.data);
      });
    },
    getTraslado(id, dependencia) {
      this.dialogo = true;
      this.idDocumento = id;
      this.depActual = dependencia;
      // console.log(id);
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
              actual: this.depActual,
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
            console.log(response.data);
            // this.total = response.data.length;
          }
        });
    },
    preview(code, traslado) {
      this.handlerDialog.preview.visible = true;
      this.datacoment.idDocumento = code;
      this.datacoment.idTraslado = traslado;
      this.getComentario(code, traslado);
    },
  },
};
</script>