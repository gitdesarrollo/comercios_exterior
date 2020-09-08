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
        <el-table-column label="Direccón"  prop="descripcion"></el-table-column>
        <el-table-column label="Estado"  prop="estado"></el-table-column>
        <el-table-column label="Operaciones" width="180">
          <template slot-scope="scope" class="pl-3"> 
            <el-button
              type="danger"
              size="mini"
              icon="el-icon-download"
              plain
              @click="handleEdit(scope.row.id,scope.row.username)" 
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
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      url_list: {
        lista: "traslados",
        dependencias: "dependencias",
        trasladar: "Trasladar",
      },
      list_response: {
        documentos: [],
        list_dependencia: [],
      },
      total: 0,
      currentPage: 1,
      pagesize: 10,
      EditscreenLoading: false,
      dialogo: false,
      idDocumento:0,
      depActual:0,
      trasladarBtn: "Sin Enviar",
      form: {
        departamentoId: "",
      },
      rules: {
        departamentoId: [
          {
            require: true,
            message: "Seleccione dirección de traslado",
            trigger: "blur"
          }
        ]
      }
    };
  },
  mounted() {
    this.getLista();
    this.selectDireccion();
  },
  methods: {
    getLista() {
      axios.get(this.url_list.lista).then((response) => { 
        this.list_response.documentos = response.data;
        this.total = response.data.length;
        console.log(response.data);
      });
    },
    getTraslado(id,dependencia) {
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
    documentTransfer(form){
      this.$refs[form].validate(valid => {
        if(valid){
          this.EditscreenLoading = true;
          axios.put(this.url_list.trasladar,{
            Documento: this.idDocumento,
            actual: this.depActual,
            idDireccionTraslado: this.form.departamentoId
          })
          .then(response => {
            this.EditscreenLoading = false; 
            this.dialogo = false;
            // console.log(response.data);
          })
        }
      })
    }
  },
};
</script>