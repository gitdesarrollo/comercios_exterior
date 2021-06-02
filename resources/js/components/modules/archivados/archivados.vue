<template>
  <div>
    <b-card
      header="Archivados"
      header-bg-variant="primary"
      header-tag="header"
    >
    <el-table :data="API.response.archivos.filter(data => !search || data.remitente.toLowerCase().includes(search.toLowerCase())
      || data.correlativo.toLowerCase().includes(search.toLowerCase())
      || data.correlativo_interno.toLowerCase().includes(search.toLowerCase())
      )"  stripe :header-cell-style="tableHeaderColor">
      <el-table-column label="Remitente" prop="remitente" header-align="center"></el-table-column>
      <el-table-column label="Correlativo" prop="correlativo" header-align="center"></el-table-column>
      <el-table-column label="Interno" prop="correlativo_interno" header-align="center"></el-table-column>
      <el-table-column label="Herramienta" header-align="center" width="180">
        <template slot="header" slot-scope="scope">
          <el-input
            v-model="search"
            size="mini"
            placeholder="Buscar"/>
        </template>
        <template slot-scope="scope">
          <div>
            <el-link type="primary" :underline="false" @click="toReturn(scope.row.correlativo_interno)"><i class="fas fa-redo"></i></el-link>
          </div>
        </template>
      </el-table-column>
    </el-table>
    
    </b-card>
  </div>
</template>

<script>
export default {
  data(){
    return {
      search: "",
      API: {
        router: {
          archivos: "getArchivos",
          toReturn: "toReturn"
        },
        response: {
          archivos: []
        },
        config: {
          fields: {

          }
        }
      }
    }
  },
  mounted() {
    this.getArchivos();
  },
  methods: {
    getArchivos(){
      axios.get(this.API.router.archivos)
        .then(response => {
          this.API.response.archivos = response.data
        })
    },
    toReturn(code) {
      axios.post(this.API.router.toReturn, {code: code})
      .then(response => {
        console.log(response.data)
        if(response.data){
            this.$swal({
              icon: "success",
              title: "Recuperación exitosa!",
              showConfirmButton: false,
              timer: 2500,
            });
            this.getArchivos();
        }else{
            this.$swal({
              icon: "error",
              title: "Error al restaurar el documentos",
              showConfirmButton: false,
              timer: 2500,
            });
        }
      })
    },
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #333333;color: #fff;font-weight: 500;text-align: center;font-size:1.2em;";
      }
    },
  },
};
</script>
