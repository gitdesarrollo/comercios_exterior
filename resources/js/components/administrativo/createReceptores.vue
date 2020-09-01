<template>
  <div>
    <div class="card">
      <div class="card-header text-white bg-primary">Catálogo de Receptores</div> 
      <div class="card-body">
        <el-form ref="form" :model="form" :rules="rules" label-width="20px">
            <el-row :gutter="20" class="mt-2">
                <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24">
                    <el-form-item  prop="name">
                        <el-input v-model="form.name">
                            <template slot="prepend">Nombre:</template>
                        </el-input>
                    </el-form-item>
                </el-col>
            </el-row>    
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
          <el-table-column label="Nombre" prop="descripcion"></el-table-column>
          <el-table-column label="Operaciones"  width="200">
            <template slot-scope="scope">
              <el-button
                size="mini"
                @click="
                                    handleEdit(
                                        scope.row.id,
                                        scope.row.descripcion
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

<style>
    .el-icon-error{
        color: red !important;
    }

    .el-icon-success{
        color: green !important;
    }


</style>

<script>
export default {
  data() {
    return {
        url_data: {
            show_unit: 'getReceptores',
            set_unit: 'setReceptores',
            update_unit: 'updateReceptor',

            show_unit_data: 'unidades',
            show_roles: 'getRoles',
        },
        validate_form: false, 
        list_icon: {
            iconos: '',
            init:'',
            loader: 'el-icon-loading',
            check: 'el-icon-success',
            error: 'el-icon-error'
        },
        response_data: {
            list_unit: [],
            list_unit_data: [],
            list_roles: [],
        },
        roles: {
          is_admin: 0
        },
        total: 0,
        currentPage: 1,
        pagesize: 10,
        form: {
            name: "",
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
            ],
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
      getRoles(){
        axios.get(this.url_data.show_roles).then(response => {
          this.response_data.list_roles = response.data;
         
        });
      },
      getUniti() {
        axios.get(this.url_data.show_unit_data).then(response => {
          this.response_data.list_unit_data = response.data;
         
        });
      },
      load(){
          this.list_icon.iconos = this.list_icon.loader
      },
      change_icon() {
          const h = this.$createElement;
          if(this.form.email == this.form.confirm){
              this.list_icon.iconos = this.list_icon.check
              this.validate_form = true
          }else{
              this.list_icon.iconos = this.list_icon.error
              this.$notify.error({
                    title: 'Error',
                    message: 'Correo electrónico no coincide'
                });
              
          }
      },
    onSubmit(form) {
      const h = this.$createElement;
          this.$refs[form].validate(valid => {
            if (valid) {
              this.fullscreenLoading = true;
              axios
                .post(this.url_data.set_unit, {
                  name: this.form.name
                })
                .then(response => {
                    console.log(response.data)
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
      axios.get(this.url_data.show_unit).then(response => {
        this.response_data.list_unit = response.data;
        this.total = response.data.length;
      });
    },
    editProduct() {
      const h = this.$createElement;
      
      this.EditscreenLoading = true;
      axios
        .put(this.url_data.update_unit,{
          id: this.id_seleccion,
          name: this.formEdit.name
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
