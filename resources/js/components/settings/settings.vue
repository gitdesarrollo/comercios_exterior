<template>
  <div>
    <div class="card">
      <div class="card-header text-white bg-primary">Configuración General</div>
      <div class="card-body">
        <el-form ref="form" :model="form" :rules="rules" label-width="20px">
          <el-row :gutter="10" class="mt-2">
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="12">
              <el-form-item prop="parametro">
                <el-input v-model="form.parametro">
                  <template slot="prepend">Nombre Parametro:</template>
                </el-input>
              </el-form-item>
            </el-col>
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="12">
              <el-form-item prop="valor">
                <el-input v-model="form.valor">
                  <template slot="prepend">Valor Parametro:</template>
                </el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-form-item>
            <el-button
              type="primary"
              @click="onSubmit('form')"
              v-loading.fullscreen.lock="fullscreenLoading"
              >Guardar</el-button
            >
          </el-form-item>
        </el-form>
        <el-table
          :data="response_data.listParametros"
          style="width: 100%"
        >
          <el-table-column label="No." type="index"></el-table-column>
          <el-table-column label="Nombre" prop="commandString"></el-table-column>
          <el-table-column label="Valor" prop="valuesString"></el-table-column>

          <el-table-column label="Operaciones" width="200">
            <template slot-scope="scope">
              <el-button
                size="mini"
                @click="handleEdit(scope.row.id, scope.row.commandString)"
                >Editar</el-button
              >
            </template>
          </el-table-column>
        </el-table>
        <el-dialog
          title="Modificar Parametros"
          :visible.sync="dialogo"
          width="30%"
          top="3vh"
          destroy-on-close
        >
          <el-form
            :inline="false"
            :model="formEdit"
            ref="form"
            :rule="rules"
            label-width="150px"
          >
            <el-form-item label="Parametro:">
              <label style="color: red">
                {{ descripcion_seleccion }}
              </label>
            </el-form-item>
            <el-form-item label="Nuevo Nombre">
              <el-input v-model="formEdit.parametro"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button
                type="primary"
                @click="editParameter()"
                v-loading.fullscreen.lock="EditscreenLoading"
                >Guardar</el-button
              >
            </el-form-item>
          </el-form>
        </el-dialog>
      </div>
    </div>
  </div>
</template>

<style>
.el-icon-error {
  color: red !important;
}

.el-icon-success {
  color: green !important;
}
</style>

<script>
export default {
  data() {
    return {
      url_data: {
        settings: "settings",
        setting: "setting",
        edit: "editParameter"
      },
      validate_form: false,
      list_icon: {
        iconos: "",
        init: "",
        loader: "el-icon-loading",
        check: "el-icon-success",
        error: "el-icon-error",
      },
      response_data: {
        listParametros: [],

        list_unit_data: [],
        list_roles: [],
      },

      total: 0,
      currentPage: 1,
      pagesize: 10,
      form: {
        parametro: "",
        valor: "",
      },
      formEdit: {
        parametro: "",
      },
      listProduct: [],
      rules: {
        parametro: [
          {
            required: true,
            message: "Este campo no puede ser vacio",
            trigger: "blur",
          },
        ],
        valor: [
          {
            required: true,
            message: "Este campo no puede ser vacio",
            trigger: "blur",
          },
        ],
      },
      fullscreenLoading: false,
      EditscreenLoading: false,
      dialogo: false,
      id_seleccion: 0,
      descripcion_seleccion: "",
    };
  },
  mounted() {
    this.getAll();
  },
  methods: {
    onSubmit(form) {
      const h = this.$createElement;

        this.$refs[form].validate((valid) => {
          if (valid) {
            this.fullscreenLoading = true;

            axios
              .post(this.url_data.setting, {
                parametro: this.form.parametro,
                valor: this.form.valor
              })
              .then((response) => {
                const status = JSON.parse(response.status);
                const data = JSON.parse(response.data);
                if (status == "200" && data) {
                  this.$message({
                    message: h("p", null, [
                      h("i", { style: "color: teal" }, "Agregado!"),
                    ]),
                    type: "success",
                  });
                  this.$refs[form].resetFields();
                  this.fullscreenLoading = false;
                  this.getAll();
                }
              });
          } else {
            this.$message.error({
              message: h("p", null, [
                h("i", { style: "color: red" }, "Complete los datos"),
              ]),
            });
            return false;
          }
        });
    },
    getAll() {
      axios.post(this.url_data.settings).then((response) => {
        this.response_data.listParametros = response.data;
        // this.total = response.data.length;
      });
    },
    editParameter() {
      const h = this.$createElement;
      this.EditscreenLoading = true;
      axios
        .post(this.url_data.edit, {
          id: this.id_seleccion,
          parametro: this.formEdit.parametro,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.$message({
              message: h("p", null, [
                h("i", { style: "color: teal" }, "Cambio realizado con exito!"),
              ]),
              type: "success",
            });
            this.EditscreenLoading = false;
            this.formEdit.parametro = "";
            this.dialogo = false;
            this.getAll();
          }
        });
    },
    current_change: function (currentPage) {
      this.currentPage = currentPage;
    },
    handleEdit(producto, desc) {
      this.dialogo = true;
      this.id_seleccion = producto;
      this.descripcion_seleccion = desc;
    },
    handleDelete(code) {
      const h = this.$createElement;
      let url = "deleteProductById";
      this.EditscreenLoading = true;
      axios
        .post(url, {
          id: code,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.$message({
              message: h("p", null, [
                h("i", { style: "color: teal" }, "Producto Eliminado"),
              ]),
              type: "success",
            });
            this.EditscreenLoading = false;
            this.formEdit.name = "";
            this.dialogo = false;
            this.getAll();
          }
        });
    },
  },
};
</script>
