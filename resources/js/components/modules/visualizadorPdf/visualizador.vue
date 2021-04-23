<template>
  <div>
    <div class="card">
      <div class="card-header text-write bg-primary">Archivos Recibidos</div>
      <div class="card-body">
        <el-card class="mb-4">
          <div slot="header" class="clearfix">
            <span>Búsqueda</span>
          </div>
          <el-form  :model="form" ref="form">
            <el-row :gutter="20">
              <el-col :xs="25" :sm="6" :md="8" :lg="8" :xl="8">
                <el-form-item label="Interno:">
                  <el-input
                    placeholder="Interno"
                    v-model="form.internal"
                  ></el-input>
                </el-form-item>
              </el-col>
              <el-col :xs="25" :sm="6" :md="8" :lg="8" :xl="8">
                <el-form-item label="Direcciones:">
                  <el-select
                    v-model="form.direction"
                    class="select_width"
                    placeholder="Direcciones"
                    clearable
                    multiple
                    filterable
                  >
                    <el-option
                      v-for="(index, x) in Response.get.direcciones"
                      :key="x"
                      :label="index.name"
                      :value="index.id"
                    ></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :xs="25" :sm="6" :md="8" :lg="8" :xl="8">            
                <el-form-item label="Viceministerio:">
                  <el-select
                    v-model="form.vice"
                    class="select_width"
                    placeholder="Viceministerio"
                    multiple
                    clearable
                    filterable
                  >
                    <el-option
                      v-for="(index, x) in Response.get.viceministerio"
                      :key="x"
                      :label="index.descripcion"
                      :value="index.id"
                    ></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
            </el-row>
            <el-form-item>
              <el-button type="primary" plain @click="getListByFilter"
                >Buscar</el-button
              >
              <el-button type="info" plain @click="resetForm('form')"
                >Limpiar</el-button
              >
            </el-form-item>
          </el-form>
        </el-card>
        <div class="card-columns">
          <div class="card" v-for="(index, i) in Response.get.lista" :key="i">
            <pdf
              :src="index.URL"
              :page="1"
              class="rounded mx-auto d-block"
            ></pdf>
            <div class="card-body">
              <h6 class="card-title font-weight-bold">
                {{ index.NOMBRE_ARCHIVO }}
              </h6>
              <!-- <p class="card-text font-weight-normal">
                {{ index.REMITENTE}}
              </p> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import pdf from "vue-pdf";
var loadingTask = pdf.createLoadingTask("./files/MINECO-DACE-3-2021.pdf");
export default {
  components: {
    pdf,
  },
  data() {
    return {
      form: {
        internal: "",
        direction: [],
        vice: [],

        assigned: "",
        type: "",
        Idate: "",
        Fdate: "",
      },
      numPages: undefined,
      search: "",
      pageCount: 0,
      url: loadingTask,
      API: {
        get: {
          lista: "listaArchivo",
          direcciones: "unidades",
          viceministerio: "listaVice",
        },
        post: {
          getFileByFilter: "listaFiltro"
        },
      },
      Response: {
        get: {
          lista: [],
          direcciones: [],
          viceministerio: [],
        },
        post: {},
      },
    };
  },
  methods: {
    getListUpload() {
      axios.get(this.API.get.lista).then((response) => {
        this.Response.get.lista = response.data;
        console.log(response.data);
      });
    },
    getListAddresses(){
      axios.get(this.API.get.direcciones)
        .then(response => {
          this.Response.get.direcciones = response.data
        })
    },
    getListVice(){
      axios.get(this.API.get.viceministerio)
        .then(response => {
          this.Response.get.viceministerio = response.data;
        })
    },
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #009879;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    getListByFilter(){
      axios.post(this.API.post.getFileByFilter,{
        direction: this.form.direction,
        vice: this.form.vice,
        internal:this.form.internal
      })
        .then(response => {
          console.log(response.data)
        })
    },
    resetForm(){},
  },
  beforeMount() {
    this.getListUpload();
    this.getListAddresses();
    this.getListVice();
  },
  mounted() {
    // this.url.promise.then((pdf) => {
    //   this.numPages = pdf.numPages;
    //   console.log(this.numPages);
    // });
  },
};
</script>

<style lang="scss" scoped>
.delete {
  font-size: 1em;
  // margin-left: 10px;
  color: #ff1a1a;
}

@media (min-width: 576px) {
  .card-columns {
    -webkit-column-count: 5;
    -moz-column-count: 5;
    column-count: 5;
    -webkit-column-gap: 1.25rem;
    -moz-column-gap: 1.25rem;
    column-gap: 1.25rem;
    orphans: 1;
    widows: 1;
  }
  .card-columns .card {
    display: inline-block;
    width: 100%;
  }
}
</style>