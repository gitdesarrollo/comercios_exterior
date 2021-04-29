<template>
  <div>
    <div class="card">
      <div class="card-header text-white bg-primary">
        Notificaciones de documentos
      </div>
      <div class="card-body">
        <el-table
          :data="responses.list.sender"
          border
          stripe
          :header-cell-style="tableHeaderColor"
        >
          <el-table-column type="index" label="#"></el-table-column>
          <el-table-column
            prop="formato"
            label="Correlativo"
            width="200"
          ></el-table-column>
          <el-table-column
            prop="empresa"
            label="Remitente"
            width="350"
          ></el-table-column>
          <el-table-column
            prop="descripcion"
            label="Descripción"
          ></el-table-column>
          <el-table-column label="Opciones" width="100">
            <template slot-scope="scope">
              <el-button
                type="danger"
                size="mini"
                icon="el-icon-check"
                plain
                @click="toAccepts(scope.row.idTraslado, scope.row.code)"
              ></el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      methods: {
        get: {
          getSender: "notificaciones",
        },
        post: {
          setSender: "setSender",
        },
        put: {
          deleteSender: "deleteSender",
          toAccept: "Aceptar",
        },
      },
      responses: {
        list: {
          sender: [],
        },
      },
    };
  },
  methods: {
    getSender() {
      axios.get(this.methods.get.getSender).then((response) => {
        this.responses.list.sender = response.data;
      });
    },
    toAccepts(id, documento) {
      const h = this.$createElement;
      axios
        .put(this.methods.put.toAccept, {
          code: id,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          if (status == "200") {
          this.$swal({
            position: "top-end",
            icon: "success",
            title: "Transaccion realizada con exito",
            showConfirmButton: false,
            timer: 2500,
          });
            this.getSender();
          }
        });
    },
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #2c3c5c;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    deleteSender(id) {
      const h = this.$createElement;
      axios
        .put(this.methods.put.deleteSender, {
          id: id,
        })
        .then((response) => {
          if (response.data == 1) {
            this.$notify({
              title: "Notificación",
              message: "Dato Eliminado",
              type: "success",
              duration: "4500",
              position: "top-right",
              showClose: "true",
            });
            this.getSender();
          } else {
            this.$notify.error({
              title: "Error",
              message: "Error al intentar borrar el dato",
            });
          }
        });
    },
  },
  mounted() {
    this.getSender();
  },
};
</script>
