<template>
  <div class="card">
    <div class="card-header text-write bg-primary">Expedientes</div>
    <div class="card-body">
      <el-card class="mb-4">
        <div slot="header" class="clearfix">
          <span>Búsqueda</span>
        </div>
        <el-form :inline="true" :model="form" ref="form">
          <el-form-item label="Remitente:">
            <el-select v-model="form.sender" placeholder="Remitente" filterable>
              <el-option v-for="(index,x) in Result.sender" :key="x" :label="index.descripcion" :value="index.descripcion"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Correlativo:">
            <el-input placeholder="Correlativo" v-model="form.correlative"></el-input>
          </el-form-item>
          <el-form-item label="Interno:">
            <el-input placeholder="Interno" v-model="form.internal"></el-input>
          </el-form-item>
          <el-form-item label="Asignado:">
            <el-input placeholder="Asignado" v-model="form.assigned"></el-input>
          </el-form-item>
          <el-form-item label="Tipo:">
            <el-select v-model="form.type" placeholder="Tipos" filterable>
              <el-option v-for="(index,x) in Result.type" :key="x" :label="index.descripcion" :value="index.id"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Fecha:">
            <el-col :span="10">
              <el-date-picker
                type="date"
                format="yyyy-MM-dd"
                value="yyyy-MM-dd"
                placeholder="Inicio"
                v-model="form.Idate"
                style="width: 100%"
              ></el-date-picker>
            </el-col>
            <el-col class="line" :span="2"> - </el-col>
            <el-col :span="10">
              <el-date-picker
                type="date"
                format="yyyy-MM-dd"
                value="yyyy-MM-dd"
                placeholder="Fin"
                v-model="form.Fdate"
                style="width: 100%"
              ></el-date-picker>
            </el-col>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" plain @click="getListByFilter"
              >Buscar</el-button>
            <el-button type="info" plain @click="resetForm('form')"
              >Limpiar</el-button>
          </el-form-item>
          <el-form-item>
            <el-row :gutter="20">
              <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="8">
                <el-link :underline="false" @click="exportFile()"> <i class="excel fas fa-file-excel"></i></el-link> 
              </el-col>
              <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="8">
                <el-link :underline="false" @click="exportPdf"> <i class="pdf fas fa-file-pdf"></i></el-link> 
              </el-col>
            </el-row>
          </el-form-item>
        </el-form>
      </el-card>
      <el-table
        v-loading="loading"
        element-loading-text="Loading..."
        element-loading-spinner="el-icon-loading"
        element-loading-background="rgba(0, 0, 0, 0.8)"
        :data="Result.List"
        height="500"
        :header-cell-style="tableHeaderColor"
        border
        ref="elTable"
      >
        <el-table-column type="index" label="#" fixed></el-table-column>
        <el-table-column
          prop="empresa"
          header-align="center"
          label="Remitente"
        ></el-table-column>
        <el-table-column
          prop="correlativo"
          header-align="center"
          label="Correlativo"
          width="160"
        ></el-table-column>
        <el-table-column
          prop="externo"
          header-align="center"
          label="Interno"
          width="160"
        ></el-table-column>
        <el-table-column
          prop="descripcion"
          header-align="center"
          label="Asunto"
        ></el-table-column>
        <el-table-column header-align="center" label="Fecha">
          <template slot-scope="scope">
            <i class="el-icon-time"></i>
            <span style="margin-left: 10px">{{ scope.row.fecha }}</span>
          </template>
        </el-table-column>
        <el-table-column
          prop="usuario"
          header-align="center"
          label="Asignado"
        ></el-table-column>
        <el-table-column
          prop="tipo"
          header-align="center"
          label="Tipo"
        ></el-table-column>
        <el-table-column fixed="right" width="130" label="Herramienta">
          <template slot-scope="scope">
            <el-button
              type="danger"
              size="mini"
              icon="el-icon-s-comment"
              plain
              @click="
                preview(
                  scope.row.code,
                  scope.row.idTraslado,
                  scope.row.correlativo,
                  scope.row.externo,
                  scope.row.url
                )
              "
            ></el-button>
          </template>
        </el-table-column>
      </el-table>
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
            :data="Result.comments"
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


  </div>
</template>

<style >
.line {
  text-align: center;
}

.excel{
  font-size: 3em;
  margin-left: 10px;
  color: #237344;
}

.pdf{
  font-size: 3em;
  margin-left: 10px;
  color: #fa2416;
}

.text-table{
  font-size: 0.7em;
}
</style>

<script>
import jsPDF from "jspdf";
import html2canvas from "html2canvas";
import elTableExport from "el-table-export";

export default {
  components: {},
  data() {
    return {
      loading: false,
      exportType: "xls",
      url: "",
      html: "",
      methods: {
        List: "listDocumentAll",
        comments: "getComentario",
        listByFilter: "listByFilter",
        remitente:"listDocumentRemitente",
        tipos:"tipos"
      },
      Result: {
        List: [],
        comments: [],
        sender:[],
        type:[]
      },
      utility: {
        search: "",
      },
      dialog: {
        preview: {
          title: "",
          visible: false,
          width: "85%",
          top: "2vh",
          destroy: true,
        },
      },
      form: {
        sender: "",
        correlative: "",
        internal: "",
        assigned: "",
        type: "",
        Idate: "",
        Fdate: "",
      },
    };
  },

  methods: {
    getList() {
      this.loading = true
      axios
        .get(this.methods.List)
        .then((response) => {
          this.Result.List = response.data;
          console.log(response.data);
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getListByFilter() {
      this.Result.List = [];
      this.loading = true;
      axios
        .post(this.methods.listByFilter, {
          sender: this.form.sender,
          correlative: this.form.correlative,
          internal: this.form.internal,
          assigned: this.form.assigned,
          type: this.form.type,
          Idate: this.form.Idate,
          Fdate: this.form.Fdate
        })
        .then((response) => {
          this.Result.List = response.data;
          this.loading = false
          console.log(response.data);
        });
    },
    getSender(){
      axios.get(this.methods.remitente)
          .then(response => {
            this.Result.sender = response.data
          })
    },
    getTypes(){
      axios.get(this.methods.tipos)
          .then(response => {
            this.Result.type = response.data
          })
    },
    preview(code, transfer, correlative, externalId, url) {
      console.log(code, transfer, correlative, externalId);
      this.dialog.preview.title = "Expediente " + externalId;
      this.dialog.preview.visible = true;
      this.comments(code, transfer);
      this.url = url;
    },
    comments(document, transfer) {
      axios
        .post(this.methods.comments, {
          code: transfer,
          documento: document,
        })
        .then((response) => {
          this.Result.comments = response.data;
          console.log(response.data);
        });
    },
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #009879;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    tableComment({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #2c3c5c;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    handleClose() {
      this.url = "";
      this.Result.comments = [];
    },
    resetForm() {
      this.form.sender = ""
      this.form.correlative = ""
      this.form.internal = ""
      this.form.assigned = ""
      this.form.type = ""
      this.form.Idate = ""
      this.form.Fdate = ""
      this.getList();
    },
    exportPdf(){
      var vm = this;
      var columns = [
        {title: "Remitente", dataKey: "empresa", with: 150},
        {title: "Correlativo", dataKey: "correlativo" , with: 100},
        {title: "Interno", dataKey: "externo", with: 100},
        {title: "Asunto", dataKey: "descripcion", with: 170},
        {title: "Fecha", dataKey: "fecha", with: 80},
        {title: "Asignado", dataKey: "usuario", with: 80},
        {title: "Tipo", dataKey: "tipo", with: 80}
      ]

      const doc = new jsPDF('l', 'pt', 'letter');
  
      doc.autoTable(
        columns,
        vm.Result.List, {
            tableWidth: 'auto', 
            margin : {
                top : 10,
                bottom : 10,
                lef:10,
                right:10,
                horizontal : 7
            },
            tableLineWidth: 0,
            bodyStyles: {valign: 'middle'},
            styles: {fontSize: 8,overflow: 'linebreak'},
            theme: "striped",
            columnStyles: {
                0:{cellWidth: 150  }, 
                1:{cellWidth: 100  }, 
                2:{cellWidth: 100 }, 
                3:{cellWidth: 170,fontSize: 8  }, 
                4:{cellWidth: 80 }, 
                5:{cellWidth: 80  }, 
                6:{cellWidth: 80 } 
            }
         }
       
      );


      doc.save("Expedientes.pdf");

    },
    exportFile() {
        elTableExport(this.$refs.elTable, {
            fileName: "export-demo",
            type: this.exportType,
            useFormatter: true,
        })
            .then(() => {
                console.info("ok");
            })
            .catch((err) => {
                console.info(err);
            });
    },

  },
  mounted() {
    this.getSender();
    this.getList();
    this.getTypes();
  },

};
</script>