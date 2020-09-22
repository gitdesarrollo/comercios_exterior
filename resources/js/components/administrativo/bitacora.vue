<template>
  <div>
    <div class="container">
      <div class="card">
        <div class="card-header text-white bg-primary">Bitácora de Documentos</div>
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
    <el-row :gutter="10" class="mt-2">
      <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24">
        <el-steps :active="numeroActivo" align-center v-if="traso">
          <el-step
            v-for="item in list_response.listado_bitacora"
            :key="item.id"
            :title="item.estado"
            :description='item.usuario +"  -  "+item.dependencia + "  " + item.fecha'
          ></el-step>
        </el-steps>
      </el-col>
    </el-row>
    <div v-if="presente == 0">
      <el-alert title="No se encontro Expediente" type="error" effect="dark"></el-alert>
    </div>
    <!-- <div v-show="showCard" v-loading="loading" element-loading-text="Loading..."  element-loading-spinner="el-icon-loading"  element-loading-background="rgba(0, 0, 0, 0.8)" >
            <el-row>
                <el-col :span="24" >
                    <el-card :body-style="{ padding: '0px' }">
                    
                    <div style="padding: 14px;">
                        <span class="font-weight-bold text-uppercase">Información del Bien</span>
                        <div class="bottom clearfix">
                            <div v-for="(items, i) in listProduct" :key="i">
                                <el-row :gutter="10" class="mt-3">
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="5" class="text-right">
                                        <span class="font-weight-bold">Código Sicoin:</span>
                                    </el-col>
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="7">
                                            {{ items.codigo_sicoin}}
                                    </el-col>
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="5" class="text-right">
                                        <span class="font-weight-bold">Sistema:</span>
                                    </el-col>
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="7">
                                            {{ items.cantidad}} Unidad
                                    </el-col>
                                </el-row>

                                <el-row :gutter="10" class="mt-2">
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="12">
                                        <span class="font-weight-bold">Bien:</span>
                                    </el-col>
                                </el-row>
                                <el-row :gutter="10" class="mt-2 mb-4">
                                    <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24" class="text-justify font-weight-light">
                                            {{ items.descripcion}}
                                    </el-col>
                                </el-row>

                                <el-form   ref="formInventory" :model="formInventory" :rules="rulesinv" label-width="120px"> 
                                    <el-form-item label="Físico:" prop="fisico">
                                        <el-input class="font_custom_input" v-model="formInventory.fisico" ref="form_fisico" ></el-input>
                                    </el-form-item>
                                    <el-form-item label="Lugar:" prop="lugar">
                                        <el-input class="font_custom_input" v-model="formInventory.lugar"></el-input>
                                    </el-form-item>
                                    <el-form-item label="Empleado:" prop="empleado">
                                        <el-input class="font_custom_input" v-model="formInventory.empleado"></el-input>
                                    </el-form-item>
                                    <el-row :gutter="10" v-show="visible">
                                        <el-col :span="24">
                                            <el-table :data="dataSatProvider" style="width: 100%">
                                                <el-table-column label="Nit" prop="nit"></el-table-column>
                                                <el-table-column label="Nombre" prop="name"></el-table-column>
                                                <el-table-column label="Dirección" prop="business_address"></el-table-column>
                                            </el-table>
                                        </el-col>
                                    </el-row>
                                    <el-form-item>
                                        <el-button type="primary" @click.prevent="updateInventory('formInventory')">Guardar</el-button>
                                        <el-button @click="reset">Cancel</el-button>
                                    </el-form-item>
                                </el-form>
                            </div>
                        </div>
                    </div>
                    </el-card>
                </el-col>
            </el-row>
    </div>-->
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
export default {
  data() {
    return {
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
              this.list_response.listado_bitacora = response.data;
              console.log(response.data);
              this.load = false;
              this.traso = true;
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