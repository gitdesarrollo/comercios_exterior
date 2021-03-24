<template>
  <div class="card">
    <div class="card-header text-write bg-primary">Expedientes</div>
    <div class="card-body">
      <el-table :data="Result.List" height="500" :header-cell-style="tableHeaderColor" border>
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
        <el-table-column fixed="right" width="180">
          <template slot="header" slot-scope="scope">
            <el-input
              v-model="utility.search"
              size="mini"
              placeholder="Buscar"
            />
          </template>
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
                  scope.row.externo
                )
              "
            ></el-button>
            <!-- <el-button
              @click="showDrawer()"
              type="primary"
              style="margin-left: 16px"
              plain
              icon="el-icon-full-screen"
            >
            </el-button> -->
          </template>
        </el-table-column>
      </el-table>
    </div>
    <el-dialog
      :title="dialog.preview.title"
      :visible.sync="dialog.preview.visible"
      :width="dialog.preview.width"
      center
      :top="dialog.preview.top"
      :destroy-on-close="dialog.preview.destroy"
    >
      <el-row :gutter="10">
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
          <el-table :data="Result.comments" height="600" :header-cell-style="tableComment" border>
            <el-table-column prop="usuario" label="Usuario"></el-table-column>
            <el-table-column prop="comentario" label="Mensaje"></el-table-column>
            <el-table-column prop="fecha" label="Fecha"></el-table-column>
          </el-table>
        </el-col>
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
          3
        </el-col>
      </el-row>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      methods: {
        List: "listDocumentAll",
        comments: "getComentario"
      },
      Result: {
        List: [],
        comments:[]

      },
      utility: {
        search: "",
      },
      dialog: {
        preview: {
          title: "",
          visible: false,
          width: "70%",
          top: "2vh",
          destroy:true
        }
      }
    };
  },
  methods: {
    getList() {
      axios
        .get(this.methods.List)
        .then((response) => {
          this.Result.List = response.data;
          console.log(response.data);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    preview(code, transfer, correlative, externalId){
      console.log(code, transfer, correlative, externalId)
      this.dialog.preview.title = "Expediente " + externalId;
      this.dialog.preview.visible = true;
      this.comments(code, transfer);
    },
    comments(document, transfer){
      axios.post(this.methods.comments,{
        code: transfer,
        documento: document
      })
      .then(response => {
        this.Result.comments = response.data;
        console.log(response.data)
      })
    },
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #009879;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    tableComment({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #009879;color: #fff;font-weight: 500;text-align: center;";
      }
    },
  },
  created() {
    this.getList();
  },
};
</script>