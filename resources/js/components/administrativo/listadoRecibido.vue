<template>
  <div class="card">
    <div class="card-header text-white bg-primary">Listado de Documentos Recibidos</div>
    <div class="card-body">
      <el-table
        :data="list_response.documentos.slice((currentPage - 1) * pagesize, currentPage * pagesize)"
        style="width:100%"
      >
        <el-table-column label="No." type="index"></el-table-column>
        <el-table-column label="Dirigido" prop="dirigido"></el-table-column>
        <el-table-column label="Correlativo" prop="correlativo_documento"></el-table-column>
        <el-table-column label="Direccón" width="500" prop="direccion"></el-table-column>
        <el-table-column label="Operaciones" width="200">
          <template slot-scope="scope" class="pl-3">
            <div v-if="Accept == scope.row.estado">
              <el-button
                type="danger"
                size="mini"
                icon="el-icon-check"
                plain
                @click="toAccepts(scope.row.code)"
              ></el-button>
            </div>
            <div v-else>
              <el-button
                type="danger"
                size="mini"
                icon="el-icon-search"
                plain
                @click="preview(scope.row.code,scope.row.id_dependencia,scope.row.documento, scope.row.traslado)"
              ></el-button>
              <el-button
                type="primary"
                size="mini"
                icon="el-icon-refresh-left"
                plain
                @click="getTraslado(scope.row.code)"
              ></el-button>
              <el-button
                size="mini"
                type="primary"
                icon="el-icon-s-check"
                plain
                @click="getTrasladoInterno(scope.row.code)"
              ></el-button>
            </div>
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
        title="Trasladar Documento"
        :visible.sync="dialogo"
        width="35%"
        top="3vh"
        center
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :show-close="false"
        destroy-on-close
      >
        <el-form :inline="false" :model="form" ref="form" :rule="rules" label-width="150px">
          <el-form-item label="Dirección:" prop="departamentoId">
            <el-select
              v-model="form.departamentoId"
              class="select_width"
              clearable
              filterable
              placeholder="Seleccione Dirección"
            >
              <el-option
                v-for="item in list_response.list_dependencia"
                :key="item.id_dependencia"
                :label="item.descripcion"
                :value="item.id_dependencia"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button
              type="primary"
              @click="documentTransfer('form')"
              v-loading.fullscreen.lock="EditscreenLoading"
            >Trasladar</el-button>
            <el-button @click="dialogo = false">Cancel</el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
      <el-dialog
        title="Traslado a personal interno"
        :visible.sync="interno"
        width="35%"
        top="3vh"
        center
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :show-close="false"
        destroy-on-close
      >
        <el-form :inline="false" :model="form" ref="form" label-width="150px">
          <el-form-item label="Usuario:" prop="usuario">
            <el-select
              v-model="form.usuario"
              class="select_width"
              clearable
              filterable
              placeholder="Seleccione usuario"
            >
              <el-option
                v-for="items in list_response.list_user"
                :key="items.id"
                :label="items.name"
                :value="items.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button
              type="primary"
              @click="transferUser('form')"
              v-loading.fullscreen.lock="trasladoUsuario"
            >Trasladar</el-button>
            <el-button @click="interno = false">Cancel</el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
      <el-dialog
        :title="handlerDialog.preview.title"
        :visible.sync="handlerDialog.preview.visible"
        :width="handlerDialog.preview.width"
        :top="handlerDialog.preview.top"
        center
        destroy-on-close
      >
        <el-row :gutter="10">
          <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="13">
            <embed :src="src" type="application/pdf" width="90%" height="600px" />
          </el-col>
          <!-- <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="9">
          </el-col>-->
          <el-table :data="list_response.listComentarios" :row-class-name="tableRowClassName" style="width:45%" height="550">
            <el-table-column label="No." type="index"></el-table-column>
            <el-table-column label="Usuario" prop="usuario" width="180"></el-table-column>
            <el-table-column label="Comentario" prop="comentario"></el-table-column>
          </el-table>
        </el-row>
        <el-row :gutter="10">
          <el-form :model="ruleForm" :rules="rules" ref="ruleForm" class="formComentario">
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24">
              <el-form-item label="Comentario:" prop="comentario">
                <el-input
                  type="textarea"
                  v-model="ruleForm.comentario"
                  maxlength="1000"
                  show-word-limit
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="8">
              <el-form-item>
                <el-button type="primary" @click="submitComent('ruleForm')">Guardar</el-button>
              </el-form-item>
            </el-col>
          </el-form>
        </el-row>
      </el-dialog>
    </div>
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
      datacoment: {
        idDocumento: "",
        idTraslado: "",
      },
      ruleForm: {
        comentario: "",
      },
      url_list: {
        lista: "lista",
        dependencias: "dependencias",
        trasladar: "retornar",
        message: "getMessage",
        toAccept: "Aceptar",
        getUser: "usuarios",
        TrasladoInterno: "TrasladoInterno",
        info: "infoPDF",
        comentario: "getComentario",
        setComentario: "setComentario",
      },
      list_response: {
        documentos: [],
        list_dependencia: [],
        list_user: [],
        listcomentarios: [],
      },
      total: 0,
      currentPage: 1,
      pagesize: 10,
      EditscreenLoading: false,
      dialogo: false,
      interno: false,
      trasladoUsuario: false,
      idDocumento: 0,
      depActual: 0,
      form: {
        departamentoId: "",
        usuario: "",
      },
      formUser: {
        usuario: "",
      },
      rules: {
        comentario: [
          {
            require: true,
            message: "Ingrese comentario",
            trigger: "blur",
          },
        ],
        departamentoId: [
          {
            require: true,
            message: "Seleccione dirección de traslado",
            trigger: "blur",
          },
        ],
        usuario: [
          {
            require: true,
            message: "Seleccione dirección de traslado",
            trigger: "blur",
          },
        ],
      },
      handlerDialog: {
        preview: {
          title: "Visualizar Documento",
          visible: false,
          width: "70%",
          top: "3vh",
          ver: false,
        },
      },
      src: "",
      Accept: 5,
    };
  },
  mounted() {
    this.getLista();
    this.selectDireccion();
    this.getUserTransfer();
  },
  methods: {
    submitComent(form) {
      const h = this.$createElement;
      this.$refs[form].validate((valid) => {
        if (valid) {
          this.ComentLoading = true;
          axios
            .post(this.url_list.setComentario, {
              code: this.datacoment.idDocumento,
              traslado: this.datacoment.idTraslado,
              comentario: this.ruleForm.comentario,
            })
            .then((response) => {
              const status = JSON.parse(response.status);
              if (status == "200" && response.data != false) {
                this.$message({
                  message: h("p", null, [
                    h("i", { style: "color: teal" }, "Agregado!"),
                  ]),
                  type: "success",
                });
                this.ComentLoading = false;
                this.$refs[form].resetFields();
                // this.handlerDialog.preview.visible = false;
                this.list_response.listComentarios = [];
                this.getComentario(this.datacoment.idTraslado);
              }
            });
        }
      });
    },
    getComentario(traslado) {
      axios
        .post(this.url_list.comentario, {
          code: traslado,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          const result = response.data;
          if (status == "200") {
            this.list_response.listComentarios = response.data;
            this.total = response.data.length;
          }
        });
    },
    getLista() {
      axios.get(this.url_list.message).then((response) => {
        this.list_response.documentos = response.data;
        this.total = response.data.length;
        console.log(response.data);
      });
      // axios.get(this.url_list.lista).then((response) => {
      //   this.total = response.data.length;
      //   console.log(response.data);
      // });
    },
    getTraslado(id) {
      this.dialogo = true;
      this.idDocumento = id;

      console.log(id);
      // console.log(id);
    },
    getTrasladoInterno(id, dependencia) {
      this.interno = true;
      this.idDocumento = id;
      // this.depActual = dependencia;
      // console.log(id);
    },
    current_change: function (currentPage) {
      this.currentPage = currentPage;
    },
    selectDireccion() {
      axios.get(this.url_list.dependencias).then((response) => {
        this.list_response.list_dependencia = response.data;
      });
    },
    documentTransfer(form) {
      console.log(this.form.departamentoId, this.idDocumento);
      this.$refs[form].validate((valid) => {
        if (valid) {
          this.EditscreenLoading = true;
          axios
            .put(this.url_list.trasladar, {
              Documento: this.idDocumento,
              idDireccionTraslado: this.form.departamentoId,
            })
            .then((response) => {
              this.EditscreenLoading = false;
              this.dialogo = false;
              this.getLista();
              // console.log(response.data);
            });
        }
      });
    },
    transferUser(form) {
      this.$refs[form].validate((valid) => {
        if (valid) {
          this.trasladoUsuario = true;
          axios
            .put(this.url_list.TrasladoInterno, {
              Documento: this.idDocumento,
              // actual: this.depActual,
              idUsuario: this.form.usuario,
            })
            .then((response) => {
              this.trasladoUsuario = false;
              this.interno = false;
              this.getLista();
              // console.log(response.data);
            });
        }
      });
    },
    toAccepts(id) {
      const h = this.$createElement;
      axios
        .put(this.url_list.toAccept, {
          code: id,
        })
        .then((response) => {
          console.log(response.data);
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.$message({
              message: h("p", null, [
                h("i", { style: "color: teal" }, "Operación exitosa"),
              ]),
              type: "success",
            });
            this.Accept = 1;
          }
          console.log(response.data);
        });
    },
    getUserTransfer() {
      axios.get(this.url_list.getUser).then((response) => {
        this.list_response.list_user = response.data;
      });
    },
    tableRowClassName({ row, rowIndex }) {
      if (rowIndex % 2 == 0) {
        return "warning-row";
      } else {
        return "success-row";
      }
      return "";
    },
    preview(code, dependencia, documento, traslado) {
      this.handlerDialog.preview.visible = true;
      this.datacoment.idDocumento = documento;
      this.datacoment.idTraslado = traslado;
      this.getComentario(traslado);
      axios
        .post(this.url_list.info, {
          code: documento,
        })
        .then((response) => {
          this.list_response.listInfo = response.data;
          const status = JSON.parse(response.status);
          console.log(response.data);
          if (status == "200") {
            this.src = response.data;
          }
        });
    },
  },
};
</script>