<template>
  <div>
    <div class="card">
      <div class="card-header text-white bg-primary">
        Permisos para usuarios
      </div>
      <div class="card-body">
        <el-form ref="form" :model="form" :rules="rules">
          <el-row :gutter="10" class="mt-2">
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="12">
              <el-form-item label="Rol:" prop="name">
                <el-select
                  v-model="form.name"
                  class="select_width"
                  clearable
                  filterable
                  placeholder="Seleccionar Usuario"
                >
                  <el-option
                    v-for="item in response_data.listUser"
                    :key="item.id"
                    :label="item.description"
                    :value="item.id"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="12">
            <el-form-item label="Menú:" prop="views">
              <el-select
                v-model="form.views"
                multiple
                class="select_width"
                clearable
                filterable
                placeholder="Seleccionar una Vista"
              >
                <el-option
                  v-for="item in response_data.listViews"
                  :key="item.id"
                  :label="item.description"
                  :value="item.id"
                >
                </el-option>
              </el-select>
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
          :data="
            response_data.listAll.slice(
              (currentPage - 1) * pagesize,
              currentPage * pagesize
            )
          "
          style="width: 100%"
          :header-cell-style="tableHeaderColor"
          border
          empty-text="Sin Datos"
        >
          <el-table-column label="No." type="index"></el-table-column>
          <el-table-column
            label="Usuario"
            prop="description"
          ></el-table-column>
          <el-table-column
            label="Permiso"
            prop="permiso"
          ></el-table-column>
        </el-table>
        <div style="text-align: left; margin-top: 30px">
          <el-pagination
            background
            layout="total,prev, pager, next"
            :total="total"
            @current-change="current_change"
          ></el-pagination>
        </div>
        <!-- <el-dialog
          title="Modificación"
          :visible.sync="dialogo"
          width="70%"
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
            <el-form-item label="Nombre Anterior">
              <label style="color: red">
                {{ descripcion_seleccion }}
              </label>
            </el-form-item>
            <el-form-item label="Nuevo Nombre">
              <el-input v-model="formEdit.name"></el-input>
            </el-form-item>
            <el-form-item>
              <el-button
                type="primary"
                @click="editProduct()"
                v-loading.fullscreen.lock="EditscreenLoading"
                >Guardar</el-button
              >
            </el-form-item>
          </el-form>
        </el-dialog> -->
      </div>
    </div>
  </div>
</template>

<style lang="css">
.el-table__row td {
  font-size: 12px;
}
</style>

<script>
export default {
  data() {
    return {
      url_data: {
        showUser: "getRoles",
        showViews: "getViewsUsers",
        setPermiso:"setPermiso",
        getPermisoUsuario:"getPermisoUsuario",
      },
      response_data: {
        listUser: [],
        listViews: [],
        listAll:[]
      },
      total: 0,
      currentPage: 1,
      pagesize: 10,
      form: {
        name: "",
        views:[],
      },
      formEdit: {
        name: "",

      },
      listProduct: [],
      rules: {
        name: [
          {
            required: true,
            message: "Este campo no puede ser vacio",
            trigger: "blur",
          },
        ],
        views: [
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
    this.getUser();
    this.getViews();
  },
  methods: {
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #009879;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    onSubmit(form) {
      const h = this.$createElement;
      this.$refs[form].validate((valid) => {
        if (valid) {
          this.fullscreenLoading = true;

          axios
            .post(this.url_data.setPermiso, {
              user: this.form.name,
              view: this.form.views
            })
            .then((response) => {
              const status = JSON.parse(response.status);
              if (status == "200") {
                this.$message({
                  message: h("p", null, [
                    h("i", { style: "color: teal" }, "Agregado!"),
                  ]),
                  type: "success",
                });
                this.fullscreenLoading = false;
                this.form.name = "";
                this.form.views =""
                this.getAll();
              }
            });
        }
      });
    },
    getAll() {
      axios.get(this.url_data.getPermisoUsuario).then((response) => {
        this.response_data.listAll = response.data;
        this.total = response.data.length;
      });
    },
    getUser(){
      axios.get(this.url_data.showUser)
          .then(response => {
            this.response_data.listUser = response.data;
          })
    },
    getViews(){
      axios.get(this.url_data.showViews)
          .then(response => {
            this.response_data.listViews = response.data;
          })
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
