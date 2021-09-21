<template>
  <div>
    <div class="container">
      <div class="card">
        <div class="card-header text-white bg-primary">
          Bitácora de Documentos
        </div>
        <div class="card-body">
          <el-row :gutter="10" class="mt-2">
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24">
              <el-form
                ref="form"
                :model="form"
                :rules="rules"
                label-width="150px"
                class="select_width"
                onsubmit="{form.onSubmit}"
              >
                <el-form-item label="Correlativo" prop="correlativo">
                  <el-input v-model="form.correlativo" autofocus></el-input>
                </el-form-item>
                <el-form-item>
                  <el-button
                    type="primary"
                    icon="el-icon-search"
                    @click="onSubmit('form')"
                    v-loading.fullscreen.lock="load"
                  ></el-button>
                  <el-button @click="reset('form')">Cancel</el-button>
                </el-form-item>
              </el-form>
            </el-col>
          </el-row>
        </div>
      </div>
    </div>
    <div class="container">
      <el-card shadow="always" :body-style="{ padding: '20px' }" v-if="traso">
        <div class="block">
          <el-timeline :reverse="reverse">
            <el-timeline-item  
              v-for="item in list_response.listado_bitacora"
              :key="item.id"
              :title="item.estado"
              :color="item.color"
              icon="el-icon-more"
              :size="item.size"
              :timestamp="item.fecha" placement="top">
              <el-card >
                <h4>{{ item.usuario  }}</h4>
                <p>{{ item.dependencia  }} - <b>{{ item.estado }}</b></p>
              </el-card>
            </el-timeline-item>
          </el-timeline>
        </div>
      </el-card>
      <div v-if="presente == 0">
        <el-alert
          title="No se encontro Expediente"
          type="error"
          effect="dark"
        ></el-alert>
      </div>

    </div>
  </div>
</template>

<style>

  .box-card {
    width: 480px;
  }


.scroll {
  height: 800px;
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

.timeline {
  list-style: none;
  padding: 20px 0 20px;
  position: relative;
}

.timeline:before {
  top: 0;
  bottom: 0;
  position: absolute;
  content: " ";
  width: 3px;
  background-color: #eeeeee;
  left: 50%;
  margin-left: -1.5px;
}

.timeline > li {
  margin-bottom: 20px;
  position: relative;
}

.timeline > li:before,
.timeline > li:after {
  content: " ";
  display: table;
}

.timeline > li:after {
  clear: both;
}

.timeline > li:before,
.timeline > li:after {
  content: " ";
  display: table;
}

.timeline > li:after {
  clear: both;
}

.timeline > li > .timeline-panel {
  width: 46%;
  float: left;
  border: 1px solid #d4d4d4;
  border-radius: 2px;
  padding: 20px;
  position: relative;
  -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
}

.timeline > li > .timeline-panel:before {
  position: absolute;
  top: 26px;
  right: -15px;
  display: inline-block;
  border-top: 15px solid transparent;
  border-left: 15px solid #ccc;
  border-right: 0 solid #ccc;
  border-bottom: 15px solid transparent;
  content: " ";
}

.timeline > li > .timeline-panel:after {
  position: absolute;
  top: 27px;
  right: -14px;
  display: inline-block;
  border-top: 14px solid transparent;
  border-left: 14px solid #fff;
  border-right: 0 solid #fff;
  border-bottom: 14px solid transparent;
  content: " ";
}

.timeline > li > .timeline-badge {
  color: #fff;
  width: 50px;
  height: 50px;
  line-height: 50px;
  font-size: 1.4em;
  text-align: center;
  position: absolute;
  top: 16px;
  left: 50%;
  margin-left: -25px;
  /* background-color: red; */
  background-color: #999999;
  z-index: 100;
  border-top-right-radius: 50%;
  border-top-left-radius: 50%;
  border-bottom-right-radius: 50%;
  border-bottom-left-radius: 50%;
}

.timeline > li.timeline-inverted > .timeline-panel {
  float: right;
}

.timeline > li.timeline-inverted > .timeline-panel:before {
  border-left-width: 0;
  border-right-width: 15px;
  left: -15px;
  right: auto;
}

.timeline > li.timeline-inverted > .timeline-panel:after {
  border-left-width: 0;
  border-right-width: 14px;
  left: -14px;
  right: auto;
}

.timeline-badge.primary {
  background-color: #2e6da4 !important;
}

.timeline-badge.success {
  background-color: #3f903f !important;
}

.timeline-badge.warning {
  background-color: #f0ad4e !important;
}

.timeline-badge.danger {
  background-color: #d9534f !important;
}

.timeline-badge.info {
  background-color: #5bc0de !important;
}

.timeline-title {
  margin-top: 0;
  color: inherit;
}

.timeline-body > p,
.timeline-body > ul {
  margin-bottom: 0;
}

.timeline-body > p + p {
  margin-top: 5px;
}

@media (max-width: 767px) {
  ul.timeline:before {
    left: 40px;
  }

  ul.timeline > li > .timeline-panel {
    width: calc(100% - 90px);
    width: -moz-calc(100% - 90px);
    width: -webkit-calc(100% - 90px);
  }

  ul.timeline > li > .timeline-badge {
    left: 15px;
    margin-left: 0;
    top: 16px;
  }

  ul.timeline > li > .timeline-panel {
    float: right;
  }

  ul.timeline > li > .timeline-panel:before {
    border-left-width: 0;
    border-right-width: 15px;
    left: -15px;
    right: auto;
  }

  ul.timeline > li > .timeline-panel:after {
    border-left-width: 0;
    border-right-width: 14px;
    left: -14px;
    right: auto;
  }
}
</style>

<script>
import { Control, Item, Status } from "simple-vue-timeline";

export default {
  
  data() {
    return {
     reverse: true,
      drawer: false,
      url: {
        listado: "bitacoraDocumento",
      },
      list_response: {
        listado_bitacora: [],
      },
      form: {
        correlativo: "",
      },
      traso: false,
      load: false,
      numeroActivo: 0,
      presente: 1,
      rules: {
        correlativo: [
          {
            required: true,
            message: "Ingrese Correlativo",
            trigger: "blur",
          },
        ],
      },
    };
  },
  methods: {
    onSubmit(form) {
      this.$refs[form].validate((valid) => {
        if (valid) {
          this.load = true;
          axios
            .post(this.url.listado, {
              id: this.form.correlativo,
            })
            .then((response) => {
              // limpieza de consulta
              this.list_response.listado_bitacora=[];

              // this.list_response.listado_bitacora = response.data;

              for (let index = 0; index < response.data.length; index++) {
                  this.list_response.listado_bitacora.push({
                    code: response.data[index].CODE,
                    dependencia: response.data[index].dependencia,
                    estado: response.data[index].estado,
                    fecha: response.data[index].fecha,
                    usuario: response.data[index].usuario,
                    color: '#'+(Math.random()*0xFFFFFF<<0).toString(16),
                    size: "large"
                  })   
              }
              console.log("lista:" ,this.list_response.listado_bitacora);
              console.log("respuesta: ",response.data)
              this.load = false;
              this.traso = true;
              this.drawer = true;
              this.numeroActivo = response.data.length;
              if (this.numeroActivo > 1) {
                this.numeroActivo = this.numeroActivo - 1;
              }
              this.presente = response.data.length;
              console.log(this.presente);
            });
        }
      });
    },
  },
  reset(form) {
    this.$refs[formName].resetFields();
  },
  tableRowClassName({ row, rowIndex }) {
    if (rowIndex % 2 == 0) {
      return "warning-row";
    } else {
      return "success-row";
    }
    return "";
  },
  mounted() {},
};
</script>