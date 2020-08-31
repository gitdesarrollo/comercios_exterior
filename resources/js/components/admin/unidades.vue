<template>
  <div>
    <div class="card">
      <div class="card-header text-white bg-primary">Catálogo de Unidades Ejecutoras</div>
      <div class="card-body">
        <el-form ref="form" :model="form" :rules="rules" label-width="150px">
          <el-form-item label="Nombre de Unidad" prop="name">
            <el-input v-model="form.name"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button
              type="primary"
              @click="onSubmit('form')"
              v-loading.fullscreen.lock="fullscreenLoading"
            >Guardar</el-button>
          </el-form-item>
        </el-form>
        <el-table
          :data="
                        response_data.list_unit.slice(
                            (currentPage - 1) * pagesize,
                            currentPage * pagesize
                        )
                    "
          style="width:100%"
        >
          <el-table-column label="No." type="index"></el-table-column>
          <el-table-column label="Entidad" prop="entidad"></el-table-column>
          <el-table-column label="Unidad" prop="unidad"></el-table-column>
          <el-table-column label="Operaciones"  width="200">
            <template slot-scope="scope">
              <el-button
                size="mini"
                @click="
                                    handleEdit(
                                        scope.row.id_unidad,
                                        scope.row.unidad
                                    )
                                "
              >Editar</el-button>
              <!-- <el-button
                size="mini"
                type="danger"
                @click="handleDelete(scope.row.id_unidad)"
                v-loading.fullscreen.lock="EditscreenLoading"
              >Eliminar</el-button> -->
            </template>
          </el-table-column>
        </el-table>
        <div style="text-align: left;margin-top: 30px;">
          <el-pagination
            background
            layout="total,prev, pager, next"
            :total="total"
            @current-change="current_change"
          ></el-pagination>
        </div>
        <el-dialog
          title="Modificación"
          :visible.sync="dialogo"
          width="70%"
          top="3vh"
          destroy-on-close
        >
          <el-form :inline="false" :model="formEdit" ref="form" :rule="rules" label-width="150px">
            <el-form-item label="Nombre Anterior">
              <label style="color:red;">
                {{
                descripcion_seleccion
                }}
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
              >Guardar</el-button>
            </el-form-item>
          </el-form>
        </el-dialog>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
        url_data: {
            show_unit: 'unidades',
            set_unit: 'setUnidad',
        },
        response_data: {
            list_unit: []
        },
        total: 0,
        currentPage: 1,
        pagesize: 10,
        form: {
            name: ""
        },
        formEdit: {
            name: ""
        },
        listProduct: [],
        rules: {
            name: [
            {
                required: true,
                message: "Este campo no puede ser vacio",
                trigger: "blur"
            }
            ]
        },
        fullscreenLoading: false,
        EditscreenLoading: false,
        dialogo: false,
        id_seleccion: 0,
        descripcion_seleccion: ""
    };
  },
  mounted() {
    this.getAll();
  },
  methods: {
    onSubmit(form) {
      const h = this.$createElement;
      this.$refs[form].validate(valid => {
        if (valid) {
          this.fullscreenLoading = true;
          let url = "addProduct";
          axios
            .post(this.url_data.set_unit, {
              name: this.form.name
            })
            .then(response => {
              const status = JSON.parse(response.status);
              if (status == "200") {
                this.$message({
                  message: h("p", null, [
                    h("i", { style: "color: teal" }, "Agregado!")
                  ]),
                  type: "success"
                });
                this.fullscreenLoading = false;
                this.form.name = "";
                this.getAll();
              }
            });
        } else {
          this.$message.error({
            message: h("p", null, [
              h("i", { style: "color: red" }, "Ingrese un Nombre")
            ])
          });
          return false;
        }
      });
    },
    getAll() {
      let url = "allProduct";
      axios.get(this.url_data.show_unit).then(response => {
        this.response_data.list_unit = response.data;
        this.total = response.data.length;
      });
    },
    editProduct() {
      const h = this.$createElement;
      let url = "editProduct";
      this.EditscreenLoading = true;
      axios
        .post(url, {
          id: this.id_seleccion,
          new: this.formEdit.name
        })
        .then(response => {
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.$message({
              message: h("p", null, [
                h("i", { style: "color: teal" }, "Cambio realizado con exito!")
              ]),
              type: "success"
            });
            this.EditscreenLoading = false;
            this.formEdit.name = "";
            this.dialogo = false;
            this.getAll();
          }
        });
    },
    current_change: function(currentPage) {
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
          id: code
        })
        .then(response => {
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.$message({
              message: h("p", null, [
                h("i", { style: "color: teal" }, "Producto Eliminado")
              ]),
              type: "success"
            });
            this.EditscreenLoading = false;
            this.formEdit.name = "";
            this.dialogo = false;
            this.getAll();
          }
        });
    }
  }
};
</script>
