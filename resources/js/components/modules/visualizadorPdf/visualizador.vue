<template>
  <div>
    <div class="card">
      <div class="card-header text-write bg-primary">Archivos Recibidos</div>
      <div class="card-body">
        <el-card class="mb-4">
          <div slot="header" class="clearfix">
            <span>Búsqueda</span>
          </div>
          <el-form :model="form" ref="form">
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
              <el-button type="info" plain @click="resetForm()"
                >Limpiar</el-button
              >
            </el-form-item>
          </el-form>
        </el-card>
        <b-card v-show="skeletor.search">
          <b-skeleton animation="throb" width="85%"></b-skeleton>
          <b-skeleton animation="throb" width="55%"></b-skeleton>
          <b-skeleton animation="throb" width="70%"></b-skeleton>
        </b-card>
        <div v-show="skeletor.result" class="card-columns">
          <div
            class="card mb-3"
            v-for="(index, i) in Response.get.lista"
            :key="i"
          >
            <div class="row no-gutters">
              <div class="col-md-4">
                <img :src="images.logo" class="card-img-top" />
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h6 class="card-title font-weight-bold">
                    {{ index.NOMBRE_ARCHIVO }}
                  </h6>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <el-dropdown>
                <span class="el-dropdown-link">
                  <i class="el-icon-more"></i>
                </span>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item>
                    <el-link
                      type="primary"
                      :underline="false"
                      @click="detalle(index.CODE_UPLOAD)"
                    >
                      <i class="el-icon-warning-outline"></i>Ver detalles
                    </el-link>
                  </el-dropdown-item>
                  <el-dropdown-item>
                    <el-link
                      type="primary"
                      :underline="false"
                      @click="
                        changeFile(index.CODE_UPLOAD, index.NOMBRE_ARCHIVO)
                      "
                      ><i class="el-icon-edit"></i>Cambiar archivo
                    </el-link>
                  </el-dropdown-item>
                  <el-dropdown-item>
                    <el-link
                      type="primary"
                      :underline="false"
                      @click="downloadFile(index.NOMBRE_ARCHIVO)"
                      ><i class="el-icon-download"></i>Descargar
                    </el-link>
                  </el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </div>
          </div>
        </div>
        <b-modal
          ref="modal-1"
          id="modal-1"
          no-close-on-backdrop
          centered
          :title="modal.detalle.title"
        >
          <b-container fluid>
            <b-row class="mb-1">
              <span><b>Correlativo:</b></span>
              {{ modal.detalle.name }}
            </b-row>
            <b-row class="mb-1">
              <span><b>Estado:</b></span>
              {{ modal.detalle.status }}
            </b-row>
          </b-container>
        </b-modal>
        <b-modal
          ref="change"
          id="change"
          no-close-on-backdrop
          centered
          :title="modal.detalle.title"
        >
          <b-container>
            <el-upload
              class="upload-demo mx-auto"
              drag
              action="/changeFileByCode"
              name="file[]"
              :headers="{ 'X-CSRF-TOKEN': csrf }"
              :on-preview="handlePreview"
              :on-remove="handleRemove"
              :on-success="cargaSuccess"
              :file-list="modal.change.fileList"
              :data="{
                name_file: modal.change.name,
                code_upload: modal.change.code,
              }"
            >
              <i class="el-icon-upload"></i>
              <div class="el-upload__text">
                Suelta tu archivo aquí o <em>haz clic para cargar</em>
              </div>
            </el-upload>
          </b-container>
        </b-modal>
      </div>
    </div>
  </div>
</template>

<script>
import pdf from "vue-pdf";

var loadingTask = ""
export default {
  components: {
    pdf,
  },
  props: { csrf: { type: String } },
  data() {
    return {
      form: {
        internal: "",
        direction: [],
        vice: [],
      },
      numPages: undefined,
      search: "",
      pageCount: 0,
      url: loadingTask,
      skeletor: {
        search: false,
        result: true,
      },
      modal: {
        detalle: {
          show: "",
          title: "",
          name: "",
          status: "",
        },
        change: {
          file: [],
          fileList: [],
          code: "",
          name: "",
        },
      },
      images: {
        logo: "./img/logo_pdf.png",
      },
      API: {
        get: {
          lista: "listaArchivo",
          direcciones: "unidades",
          viceministerio: "listaVice",
        },
        post: {
          getFileByFilter: "listaFiltro",
          getDetalleFile: "getDetalleFile",
          changeFileByCode: "changeFileByCode",
          download: "downloadFiles",
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
      this.showSkeletor(true);

      axios.get(this.API.get.lista).then((response) => {
        this.Response.get.lista = response.data;
        this.showSkeletor(!true);
        // console.log(response.data);
      });
    },
    getListAddresses() {
      axios.get(this.API.get.direcciones).then((response) => {
        this.Response.get.direcciones = response.data;
      });
    },
    getListVice() {
      axios.get(this.API.get.viceministerio).then((response) => {
        this.Response.get.viceministerio = response.data;
      });
    },
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #009879;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    showSkeletor(flag) {
      if (flag) {
        this.skeletor.search = true;
        this.skeletor.result = false;
      } else {
        this.skeletor.search = false;
        this.skeletor.result = true;
      }
    },
    async getListByFilter() {
      this.showSkeletor(true);
      await this.sleep(1000);
      this.Response.get.lista = [];
      axios
        .post(this.API.post.getFileByFilter, {
          direction: this.form.direction,
          vice: this.form.vice,
          internal: this.form.internal,
          multiple:true
        })
        .then((response) => {
          console.log(response.data);
          this.Response.get.lista = response.data;
          this.showSkeletor(!true);
        });
    },
    sleep(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms));
    },
    resetForm() {
      this.form.internal = "";
      this.form.direction = [];
      this.form.vice = [];
      this.getListUpload();
    },
    detalle(code) {
      console.log(code);
      axios
        .post(this.API.post.getDetalleFile, {
          code: code,
        })
        .then((response) => {
         
          this.$refs["modal-1"].show();
          this.modal.detalle.title = response.data[0]["name_file"];
          this.modal.detalle.name = response.data[0]["name"];
          this.modal.detalle.status = response.data[0]["estado"];
        });
    },
    changeFile(code, nombre) {
      this.modal.change.file = [];
      this.$refs["change"].show();
      this.modal.detalle.title = "Cambiar Archivo";
      this.modal.change.code = code;
      this.modal.change.name = nombre;
      // console.log("Change ", code, "-", nombre);
    },
    downloadFile(file) {
      axios
        .post(this.API.post.download,{
          file: file,
        },{responseType: 'blob'})
        .then((response) => {
         
          if(response.data['error']){
          this.$swal({
            position: "top-end",
            icon: "error",
            title: "No existe el archivo en el servidor",
            showConfirmButton: false,
            timer: 2500,
          });
          }else{
            const blob = new Blob([response.data], { type: 'application/pdf' })
        const link = document.createElement('a')
        link.href = URL.createObjectURL(blob)
        link.download = file
        link.click()
        URL.revokeObjectURL(link.href)
          }
          
        });
    },
    handleRemove(file) {
      // console.log(file);
    },
    handlePreview(file) {
      // console.log("files", file);
    },
    async cargaSuccess(res, file, fileList) {
      const _this = this;

      if (res["error"]) {
        if (res["reason"] == "unlink") {
          this.$swal({
            position: "top-end",
            icon: "error",
            title: "El documento se encuentra abierto",
            showConfirmButton: false,
            timer: 2500,
          });
        } else if (res["reason"] == "noFile") {
          this.$swal({
            position: "top-end",
            icon: "error",
            title: "Documento no compatible",
            showConfirmButton: false,
            timer: 2500,
          });
        } else if (res["reason"] == "doesNotExist") {
          this.$swal({
            position: "top-end",
            icon: "error",
            title: "No existe el archivo en el servidor",
            showConfirmButton: false,
            timer: 2500,
          });
        }
      } else {
        this.$swal({
          position: "top-end",
          icon: "success",
          title: "Documento Cargado",
          showConfirmButton: false,
          timer: 2500,
        });
      }
    },
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

<style>
.delete {
  font-size: 1em;
  color: #ff1a1a;
}

.font-weight-bold {
  font-size: 1em;
}

.el-dropdown-link {
  cursor: pointer;
}

.el-dropdown {
  vertical-align: top;
}
.el-dropdown + .el-dropdown {
  margin-left: 15px;
}
.el-icon-arrow-down {
  font-size: 12px;
}

.el-upload-dragger {
  width: 420px !important;
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