<template>
  <div class="card">
    <div class="card-header text-white bg-primary">Listado de Traslados</div>
    <div class="card-body">
      <el-table
        :data="list_response.documentos.slice((currentPage - 1) * pagesize, currentPage * pagesize)"
        style="width:100%"
      >
        <el-table-column label="No." type="index"></el-table-column>
        <el-table-column label="Dirigido" width="300" prop="dirigido"></el-table-column>
        <el-table-column label="Correlativo" width="150" prop="correlativo"></el-table-column>
        <el-table-column label="Direccón" prop="descripcion"></el-table-column>
        <el-table-column label="Estado" prop="estado"></el-table-column>
        <el-table-column label="Operaciones" width="180">
          <template slot-scope="scope" class="pl-3">
            <el-button
              type="danger"
              size="mini"
              icon="el-icon-search"
              plain
              @click="preview(scope.row.code,scope.row.id_dependencia)"
            ></el-button>
            <!-- v-if="trasladarBtn === scope.row.estado" -->
            <el-button
              size="mini"
              type="primary"
              icon="el-icon-right"
              plain
              @click="getTraslado(scope.row.code,scope.row.id_dependencia)"
            ></el-button>
            <el-button
              size="mini"
              type="primary"
              icon="el-icon-s-check"
              plain
              @click="getTraslado(scope.row.code,scope.row.id_dependencia)"
            ></el-button>
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
          <el-col :xs="25" :sm="6" :md="8" :lg="15" :xl="7"></el-col>
            <el-table :data="list_response.listComentarios" style="width:45%" :row-class-name="tableRowClassName">
              <el-table-column label="No." type="index"></el-table-column>
              <el-table-column label="Usuario" prop="usuario"></el-table-column>
              <el-table-column label="Comentario" prop="comentario"></el-table-column>
            </el-table>
        </el-row>
        <el-row :gutter="10">
            <el-form :model="ruleForm" :rules="rules" ref="ruleForm" class="formComentario">
              <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="24">
                <el-form-item label="Comentario:" prop="comentario">
                    <el-input  type="textarea" v-model="ruleForm.comentario">
                    </el-input>
                </el-form-item>
              </el-col>
              <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="8">
                <el-form-item >
                    <el-button type="primary" @click="submitComent('ruleForm')" >Guardar</el-button>
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

  .formComentario{
    width:100%;
  }
</style>

<script>
export default {
  data() {
    return {
      ruleForm:{
        comentario: ""
      },
      handlerDialog: {
        preview: {
          title: "Visualizar Documento",
          visible: false,
          width: "60%",
          top: "3vh",
          ver: false,
        },
      },
      src: "",
      url_list: {
        lista: "traslados",
        dependencias: "dependencias",
        trasladar: "Trasladar",
        info: "infoPDF",
        comentario: "getComentario",
      },
      list_response: {
        documentos: [],
        list_dependencia: [],
        listcomentarios: [],
      },
      total: 0,
      currentPage: 1,
      pagesize: 10,
      EditscreenLoading: false,
      ComentLoading: false,
      dialogo: false,
      idDocumento: 0,
      depActual: 0,
      trasladarBtn: "Sin Enviar",
      form: {
        departamentoId: "", 
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
      },
    };
  },
  mounted() { 
    this.getLista();
    this.selectDireccion();
  },
  methods: {
    submitComent(form) {
      const h = this.$createElement;
      this.$refs[form].validate(valid => {
        if(valid){
          this.ComentLoading = true;
          axios.post(,{
            code: 1,
            comentario: this.ruleForm.comentario
          }).then(response => {
            const status = JSON.parse(response.status);
            if(status == "200" && response.data != false){
              this.$message({
                  message: h("p", null, [
                      h("i", { style: "color: teal" }, "Agregado!")
                  ]),
                  type: "success"
              });
              this.ComentLoading = false;
              this.$refs[form].resetFields();
            }
          })
        }
      });
    },
    getComentario(code) {
      axios.post(this.url_list.comentario,{
        code: 1
      }).then(response => {
        const status = JSON.parse(response.status);
        const result = response.data;
        if(status == "200" && result != false){
          this.list_response.listComentarios = response.data;
        }
      })
    },
    tableRowClassName({row, rowIndex}) {
        if ((rowIndex / 2) === 0) {
          return 'warning-row';
        } else {
          return 'success-row';
        }
        return '';
      },
    getLista() {
      axios.get(this.url_list.lista).then((response) => {
        this.list_response.documentos = response.data;
        this.total = response.data.length;
        console.log(response.data);
      });
    },
    getTraslado(id, dependencia) {
      this.dialogo = true;
      this.idDocumento = id;
      this.depActual = dependencia;
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
      this.$refs[form].validate((valid) => {
        if (valid) {
          this.EditscreenLoading = true;
          axios
            .put(this.url_list.trasladar, {
              Documento: this.idDocumento,
              actual: this.depActual,
              idDireccionTraslado: this.form.departamentoId,
            })
            .then((response) => {
              this.EditscreenLoading = false;
              this.dialogo = false;
              // console.log(response.data);
            });
        }
      });
    },
    preview(code, dependencia) {
      this.handlerDialog.preview.visible = true;

      axios
        .post(this.url_list.info, {
          code: code,
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