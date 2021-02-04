<template>
  <div>
    <div class="card">
      <div class="card-header text-write bg-primary">
        Mis Expedientes 
      </div>
      <div class="card-body">
        <el-table 
          :data="endPoint.response.listfiles.filter(data => !search || data.interesado.toLowerCase().includes(search.toLowerCase()) 
            || data.correlativo.toLowerCase().includes(search.toLowerCase())
            || data.correlativo_externo.toLowerCase().includes(search.toLowerCase())
            || data.fecha_ingreso.toLowerCase().includes(search.toLowerCase())
            || data.usuario.toLowerCase().includes(search.toLowerCase()))" 
          :header-cell-style="tableHeaderColor"
          border empty-text="Sin Datos"
          >
          <el-table-column label="#" type="index" width="40" align="center"></el-table-column>
          <el-table-column label="Lugar" prop="interesado" ></el-table-column>
          <el-table-column label="Correlativo" prop="correlativo"></el-table-column>
          <el-table-column label="Descripción" prop="descripcion" width="300"></el-table-column>
          <el-table-column label="Correlativo Interno" prop="correlativo_externo"></el-table-column>
          <el-table-column label="Fecha Documento" width="140">
            <template slot-scope="scope">
              <i class="el-icon-time"></i>
              <span >{{ scope.row.fecha_documento }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Fecha Recepción" width="140">
            <template slot-scope="scope">
              <i class="el-icon-time"></i>
              <span >{{ scope.row.fecha_recepcion }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Fecha Ingreso" width="190">
            <template slot-scope="scope">
              <i class="el-icon-time"></i>
              <span >{{ scope.row.fecha_ingreso }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Usuario Actual" align="center" width="250">
            <template slot="header" slot-scope="scope">
              <el-input
                v-model="search"
                size="mini"
                placeholder="Buscar"/>
            </template>
            <template slot-scope="scope">
              <div slot="reference" class="name-wrapper">
                <el-tag size="medium">{{ scope.row.usuario }}</el-tag>
                <el-button type="danger" icon="el-icon-picture" plain @click="files(scope.row.correlativo_externo)"></el-button>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
    <el-dialog
      :title="endPoint.dialogs.files.title"
      :visible.sync="endPoint.dialogs.files.active"
      :width="endPoint.dialogs.files.width"
      :center="endPoint.dialogs.files.center"
      :top="endPoint.dialogs.files.top"
      >
      
      <embed
        :src="endPoint.dialogs.files.src "
        type="application/pdf"
        width="100%"
        height="700px"
      />
    </el-dialog>
  </div>
</template>

<style lang="css">
.el-table__row td{
    font-size: 12px;
}

</style>

<script>
export default {
  data(){
    return {
      endPoint: {
        get: {
          files: "listado",
        },
        post: {
          files: "getFilesByName",
        },
        response: {
          listfiles: []
        },
        dialogs: {
          files: {
            active: false,
            title: "",
            width: "60%",
            top: "2vh",
            lockScroll: true,
            center: true,
            destroyOnclose: true,
            src:""
          }
        }

      },
      search: "",
    }
  },
  mounted() {
    this.getListFiles();
  },
  methods: {
    getListFiles(){
      axios.get(this.endPoint.get.files)
      .then(response => {
        this.endPoint.response.listfiles = response.data;
      })
    },
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return 'background-color: #009879;color: #fff;font-weight: 500;text-align: center;'
      }
    },
    files(files){

      axios.post(this.endPoint.post.files,{correlativoD: files}).then(
        response => {
          console.log(response);
          if(response.data.length > 0){
            this.endPoint.dialogs.files.src = "./../files/" + response.data[0].name + ".pdf";
            this.endPoint.dialogs.files.title = "Expediente No. " + files 
            this.endPoint.dialogs.files.active = true
          }else{
           
            this.$alert('<strong>Este expediente no contiene ningun <i>Archivo</i> adjunto</strong>', 'Expediente No. ' + files, {
              dangerouslyUseHTMLString: true
            });
          }
        }
      )
    }
  },
}
</script>