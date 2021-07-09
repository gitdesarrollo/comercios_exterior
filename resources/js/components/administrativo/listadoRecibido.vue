<template>
<div class="card">
  <div class="card-header text-white bg-primary">
    Listado de Documentos Recibidos
  </div>
  <div class="card-body">
    <el-table :data="
          list_response.documentos
            .filter(
              (data) =>
                !search ||
                data.empresa.toLowerCase().includes(search.toLowerCase()) ||
                data.correlativo.toLowerCase().includes(search.toLowerCase()) ||
                data.formato.toLowerCase().includes(search.toLowerCase())
            )

        " :header-cell-style="tableHeaderColor" :cell-class-name="celdas" border style="width: 100%">
      <el-table-column label="No." type="index"></el-table-column>
      <el-table-column label="Remitente" prop="empresa"></el-table-column>
      <el-table-column label="Correlativo" prop="correlativo"></el-table-column>
      <el-table-column label="Correlativo Interno" prop="formato"></el-table-column>
      <el-table-column label="Asunto" width="500" prop="descripcion"></el-table-column>
      <el-table-column label="Fecha" width="90" prop="fecha"></el-table-column>
      <el-table-column prop="estado" label="Etiqueta  /  Restante" width="92" align="center">
        <template slot-scope="scope">
          <div v-if="scope.row.estado === 9">
            <el-tag :type="scope.row.estado === '9' ? 'primary' : 'warning'" disable-transitions>Externo
            </el-tag>
          </div>
        </template>
        <template slot-scope="scope" v-if="scope.row.tracing === '1'">
          {{ scope.row.dias }} dias
        </template>
      </el-table-column>
      <el-table-column label="Operaciones" width="280">
        <template slot="header" slot-scope="scope">
          <el-input v-model="search" size="mini" placeholder="Buscar" />
        </template>
        <template slot-scope="scope" class="pl-3">
          <div v-if="scope.row.estado == 2">
            <el-button type="danger" size="mini" icon="el-icon-check" plain @click="toAccepts(scope.row.idTraslado, scope.row.code)"></el-button>
          </div>
          <div v-else-if="scope.row.estado == 9">
            <el-button v-if="scope.row.rol == 4" size="mini" type="el-icon-error" icon="el-icon-error" plain @click="cierreDocumento(scope.row.code, scope.row.idTraslado)"></el-button>
          </div>
          <div v-else>
            <el-popover placement="top-start" title="Comentario" width="200" trigger="hover" content="Visualiza el documento y agrega comentarios">
              <el-button slot="reference" type="danger" size="mini" icon="el-icon-s-comment" plain @click="
                    preview(
                      scope.row.code,
                      scope.row.idTraslado,
                      scope.row.formato,
                      scope.row.url
                    )
                  "></el-button>
            </el-popover>
            <!-- <el-button
                              type="primary"
                              size="mini"
                              icon="el-icon-refresh-left"
                              plain
                              @click="getTraslado(scope.row.code)"
                            ></el-button>-->
            <el-popover placement="bottom-start" title="Traslado" width="250" trigger="hover" content="Traslado a personal interno">
              <el-button slot="reference" size="mini" type="primary" icon="el-icon-s-check" plain @click="
                    getTrasladoInterno(
                      scope.row.code,
                      scope.row.idTraslado,
                      scope.row.idTracing
                    )
                  "></el-button>
            </el-popover>
            <!-- <el-button
                size="mini"
                type="warning"
                icon="el-icon-s-promotion"
                plain
                @click="
                  getTrasladoExterno(scope.row.code, scope.row.idTraslado)
                "
              ></el-button> -->
            <el-popover placement="bottom-start" title="Cierre" width="250" trigger="hover" content="Cierre del Documento">
              <el-button slot="reference" v-if="(scope.row.rol == 4 || scope.row.rol == 1)" size="mini" type="el-icon-error" icon="el-icon-error" plain @click="
                    cierreDocumento(
                      scope.row.code,
                      scope.row.idTraslado,
                      scope.row.formato
                    )
                  "></el-button>
            </el-popover>
            <el-switch v-if="scope.row.flag === 'true' && scope.row.tracing === '1'" v-model="scope.row.tracing" active-value="1" inactive-value="0" active-color="#13ce66" inactive-color="#ff4949" @change="
                  switchControl(
                    scope.row.tracing,
                    scope.row.code,
                    scope.row.idTracing
                  )
                ">
            </el-switch>
            <el-switch v-else-if="scope.row.flag === 'false' && scope.row.tracing === '1'" v-model="scope.row.tracing" active-value="1" inactive-value="0" active-color="#13ce66" inactive-color="#ff4949" disabled @change="
                  switchControl(
                    scope.row.tracing,
                    scope.row.code,
                    scope.row.idTracing
                  )
                ">
            </el-switch>
            <el-switch v-else v-model="scope.row.tracing" active-value="1" inactive-value="0" active-color="#13ce66" inactive-color="#ff4949" @change="
                  switchControl(
                    scope.row.tracing,
                    scope.row.code,
                    scope.row.idTracing
                  )
                ">
            </el-switch>
            <!-- v-if="(scope.row.rol == 4 || scope.row.rol == 1)" -->
            <el-popover placement="bottom-start" title="Boleta" width="250" trigger="hover" content="Impresión de Boleta">
              <el-button slot="reference" v-if="scope.row.flag === 'true' && scope.row.tracing === '1'" size="mini" type="warning" icon="el-icon-c-scale-to-original" plain @click="
                    boleta(
                      scope.row.formato,
                      scope.row.correlativo,
                      scope.row.fecha,
                      scope.row.empresa,
                      scope.row.descripcion,
                      scope.row.code
                    )
                  "></el-button>
            </el-popover>
            <el-popover placement="bottom-start" title="Boleta" width="250" trigger="hover" content="Impresión de Boleta">
              <el-button slot="reference" v-if="scope.row.flag === 'false' && scope.row.tracing === '1'" size="mini" type="warning" icon="el-icon-c-scale-to-original" plain @click="
                    boleta(
                      scope.row.formato,
                      scope.row.correlativo,
                      scope.row.fecha,
                      scope.row.empresa,
                      scope.row.descripcion,
                      scope.row.code
                    )
                  "></el-button>
            </el-popover>

          </div>
        </template>
      </el-table-column>
    </el-table>
    <!-- <div style="text-align: left; margin-top: 30px">
        <el-pagination
          background
          layout="total,prev, pager, next"
          :total="total"
          @current-change="current_change"
        ></el-pagination>
      </div> -->
    <el-dialog title="Trasladar Documento" :visible.sync="dialogo" width="35%" top="3vh" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" destroy-on-close>
      <el-form :inline="false" :model="form" ref="form" :rule="rules" label-width="150px">
        <el-form-item label="Dirección:" prop="departamentoId">
          <el-select v-model="form.departamentoId" class="select_width" clearable filterable placeholder="Seleccione Dirección">
            <el-option v-for="item in list_response.list_dependencia" :key="item.id_dependencia" :label="item.descripcion" :value="item.id_dependencia"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="documentTransfer('form')" v-loading.fullscreen.lock="EditscreenLoading">Trasladar
          </el-button>
          <el-button @click="dialogo = false">Cancel</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <el-dialog :title="handlerDialog.previewClose.title" :visible.sync="handlerDialog.previewClose.visible" :width="handlerDialog.previewClose.width" :top="handlerDialog.previewClose.top" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" destroy-on-close @close="formCloseDialog()">
      <el-form :inline="false" :model="formClose" ref="formClose" :rules="rulesClose" id="formClose" label-width="150px">
        <el-form-item label="Comentario:" prop="comentarioCierre">
          <el-input type="textarea" v-model="formClose.comentarioCierre" maxlength="1000" :autosize="{ minRows: 7, maxRows: 13 }" show-word-limit></el-input>
        </el-form-item>
        <el-form-item>
          <el-col :span="3">
            <el-form-item v-show="controlButton.buttonPdfClose">
              <el-upload class="upload-demo" :action="'/upload'" name="file[]" :data="{
                    id_documento: datacoment.idDocumento,
                    correlativo: datacoment.correlativo,
                    count: datacoment.numberFiles,
                    type: datacoment.typePdf,
                  }" :headers="{ 'X-CSRF-TOKEN': csrf }" :on-preview="handlePreview" :on-remove="handleRemove" :show-file-list="false" :on-success="cargaSuccessClose" :file-list="fileList" accept=".pdf">
                <el-button size="medium" type="danger"><i class="fas fa-file-pdf"></i></el-button>
              </el-upload>
            </el-form-item>
          </el-col>
          <el-col :span="5">
            <el-form-item>
              <el-button v-if="controlButton.buttonClose" type="primary" @click="archivarDocument('formClose')" v-loading.fullscreen.lock="trasladoUsuario">Archivar
              </el-button>
            </el-form-item>
          </el-col>
          <el-col :span="11">
            <el-form-item>
              <el-button @click="cierreClose('formClose')">Cancel</el-button>
            </el-form-item>
          </el-col>
        </el-form-item>
      </el-form>
    </el-dialog>
    <el-dialog :title="handlerDialog.boleta.title" :visible.sync="handlerDialog.boleta.visible" :width="handlerDialog.boleta.width" :top="handlerDialog.boleta.top" :close-on-click-modal="true" :close-on-press-escape="false" :show-close="false" destroy-on-close>

      <div class="constancia" id="printTable">
        <table>
          <tbody>
            <tr>
              <td>
                <img :src="handlerDialog.boleta.img" class="constancia-img" height="120px" width="5%">
              </td>
              <td colspan="3" class="titulo_1">
                DELEGACIÓN DE CORRESPONDENCIA DESPACHO SUPERIOR
              </td>
            </tr>
            <tr>
              <td class="titulo">No. Documento:</td>
              <td>{{ handlerDialog.boleta.correlativo }}</td>
              <td class="titulo">Correlativo Mineco:</td>
              <td>{{ handlerDialog.boleta.formato }}</td>
            </tr>
            <tr>
              <td class="titulo">Fecha de Recepción:</td>
              <td>{{ handlerDialog.boleta.fecha }}</td>
              <td class="titulo">Fecha de Impresión:</td>
              <td>{{ event_at }}</td>
            </tr>
            <tr>
              <td class="titulo">Delegado:</td>
              <td colspan="3">
                {{ handlerDialog.boleta.viceministerio }}
              </td>
            </tr>
            <tr>
              <td class="titulo">Cc:</td>
              <td colspan="3">

              </td>
            </tr>
            <tr>
              <td class="titulo">Fecha de entrega a Despacho:</td>
              <td>{{ handlerDialog.boleta.fechafin }}</td>
              <td class="titulo">Fecha Limite Envío:</td>
              <td>{{ handlerDialog.boleta.fechafin }}</td>
            </tr>
            <tr>
              <td class="titulo">Remitente Organizacional:</td>
              <td>{{ handlerDialog.boleta.empresa }}</td>
              <td class="titulo">No. Oficio:</td>
              <td>{{ handlerDialog.boleta.correlativo }}</td>
            </tr>
            <tr>
              <td class="titulo">Tema:</td>
              <td colspan="3">
                {{ handlerDialog.boleta.descripcion }}
              </td>
            </tr>
            <tr>
              <td class="titulo">Instruccion Ministro:</td>
              <td colspan="3">
                {{ handlerDialog.boleta.instrucciones_ministro }}
              </td>
            </tr>
            <tr>
              <td class="titulo">Seguimiento:</td>
              <td colspan="3">
                {{ handlerDialog.boleta.instrucciones_generales }}
              </td>
            </tr>
          </tbody>
        </table>

      </div>

      <el-row :gutter="20" class="mt-5">
        <el-col :span="12" :offset="0">
          <el-button type="warning" size="default" @click="imprimir">Imprimir</el-button>

        </el-col>
      </el-row>
    </el-dialog>
    <el-dialog title="Traslado Externo" :visible.sync="externo" width="35%" top="3vh" center :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" destroy-on-close>
      <el-form :inline="false" :model="formExterno" ref="formExterno" label-width="150px">
        <el-form-item label="Lugar:">
          <el-input v-model="formExterno.lugar"></el-input>
        </el-form-item>
        <el-form-item label="Correlativo:">
          <el-input v-model="formExterno.correlativo"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="transferExterno()" v-loading.fullscreen.lock="trasladoUsuario">Trasladar
          </el-button>
          <el-button @click="externo = false">Cancel</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <el-dialog title="Traslado a personal interno" :visible.sync="interno" width="35%" top="3vh" center :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" destroy-on-close @close="closeTrasladoInterno">
      <el-form :inline="false" :model="form" ref="form" label-width="150px">
        <el-form-item label="Usuario:" prop="usuario">
          <el-select v-model="form.usuario" class="select_width" clearable filterable placeholder="Seleccione usuario">
            <el-option v-for="items in list_response.list_user" :key="items.id" :label="items.name" :value="items.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Copia:">
          <el-select v-model="form.cc" class="select_width" clearable filterable placeholder="Seleccione usuario" popper-class="item_data" multiple>
            <el-option v-for="items in list_response.list_user" :key="items.id" :label="items.name" :value="items.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="transferUser('form')" v-loading.fullscreen.lock="trasladoUsuario">Trasladar
          </el-button>
          <el-button @click="interno = false">Cancel</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <el-dialog :title="handlerDialog.preview.title" :visible.sync="handlerDialog.preview.visible" :width="handlerDialog.preview.width" :top="handlerDialog.preview.top" @close="closeEvent()" @open="openEvent()" @opened="opened" destroy-on-close>
      
      <el-row :gutter="10">
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
            <div v-show="handlerFile.dialogComent">
              <RotateSquare2></RotateSquare2>
            </div>
            <!-- <b-alert v-show="handlerFile.showError" show variant="danger">Sin documento Adjunto</b-alert> -->

            <el-table v-if="handlerFile.tableComent" :data="list_response.listcomentarios" :header-cell-style="tableComment" :row-class-name="tableRowClassName" height="450" border>
              <el-table-column label="No." type="index"></el-table-column>
              <el-table-column label="Usuario" prop="usuario" width="180"></el-table-column>
              <el-table-column label="Comentario" prop="comentario"></el-table-column>
            </el-table>

          <el-form :model="ruleForm" ref="ruleForm" label-width="120px" label-position="top">
            <el-form-item label="Comentario:" prop="comentario" :rules="formRule.comentario">
              <el-input type="textarea" v-model="ruleForm.comentario" maxlength="1000" show-word-limit></el-input>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="submitComent('ruleForm')" :loading="handlerLoading.addComent">Guardar
              </el-button>
            </el-form-item>
          </el-form>
          <el-col :xs="4" :sm="4" :md="2" :lg="2" :xl="2">
            <el-upload class="upload-demo" :action="'/upload'" name="file[]" :data="{
                  id_documento: datacoment.idDocumento,
                  correlativo: datacoment.correlativo,
                  count: datacoment.numberFiles,
                  type: datacoment.typePdf,
                }" :headers="{ 'X-CSRF-TOKEN': csrf }" :on-preview="handlePreview" :on-remove="handleRemove" :show-file-list="false" :on-success="cargaSuccess" :file-list="fileList" accept=".pdf">
              <el-link :underline="false">
                <i class="pdf fas fa-file-pdf"></i></el-link>
            </el-upload>
          </el-col>
          <el-col :xs="4" :sm="4" :md="2" :lg="2" :xl="2">
            <el-upload class="upload-demo" :action="'/uploadWord'" name="file[]" :data="{
                  id_documento: datacoment.idDocumento,
                  correlativo: datacoment.correlativo,
                  count: datacoment.numberFiles,
                  type: datacoment.typeWord,
                }" :headers="{ 'X-CSRF-TOKEN': csrf }" :on-preview="handlePreview" :on-remove="handleRemove" :show-file-list="false" :on-success="cargaSuccess" :on-exceed="handleExceed" :file-list="fileList" accept=".docx,.doc">
              <el-link :underline="false">
                <i class="word fas fa-file-word"></i></el-link>
            </el-upload>
          </el-col>
          <el-col :xs="4" :sm="4" :md="2" :lg="2" :xl="2">
            <el-upload class="upload-demo" :action="'/uploadExcel'" name="file[]" :data="{
                  id_documento: datacoment.idDocumento,
                  correlativo: datacoment.correlativo,
                  count: datacoment.numberFiles,
                  type: datacoment.typeExcel,
                }" :headers="{ 'X-CSRF-TOKEN': csrf }" :on-preview="handlePreview" :on-remove="handleRemove" :show-file-list="false" :on-success="cargaSuccess" :on-exceed="handleExceed" :file-list="fileList" accept=".xlsx,.xls">
              <el-link :underline="false">
                <i class="excel fas fa-file-excel"></i></el-link>
            </el-upload>
          </el-col>
          <el-button v-if="controlButton.buttonWord" type="primary" @click="filesWord()">Archivos</el-button>
        </el-col>
        <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
          <div v-show="handlerFile.showLoading">
            <RotateSquare2></RotateSquare2>
          </div>
          <b-alert v-show="handlerFile.showError" show variant="danger">Sin documento Adjunto</b-alert>
          <div class="reload-div pb-3" v-show="handlerFile.showPdf">
            <el-link type="primary" :underline="false" @click="reload">
              <i class="fas fa-sync reload"></i>
            </el-link>
          </div>
          <embed :src="url" type="application/pdf" width="100%" height="600" ref="viewPDf" v-show="handlerFile.showPdf" />

        </el-col>
      </el-row>
      <el-dialog :width="handlerDialog.inner.width" :title="handlerDialog.inner.title" :visible.sync="handlerDialog.inner.innerVisible" append-to-body>
        <el-row :gutter="10">
          <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <el-table :data="list_response.getFileWord" :header-cell-style="tableComment" height="450" border>
              <el-table-column label="No." type="index"></el-table-column>
              <el-table-column label="Archivo" prop="file"></el-table-column>
              <el-table-column label="Fecha" prop="fecha" width="150"></el-table-column>
              <el-table-column label="Operaciones" width="100" align="center">
                <template slot-scope="scope">
                  <el-link :href="scope.row.url" :underline="false"><i class="donwloadFile fas fa-download"></i></el-link>
                </template>
              </el-table-column>
            </el-table>
          </el-col>
        </el-row>
      </el-dialog>
    </el-dialog>
  </div>
  <el-dialog :title="message.title" :visible.sync="message.visible" :width="message.width" center destroy-on-close :close-on-click-modal="message.closeModal" :close-on-press-escape="message.closeModal" @close="closeDate">
    <div class="block">
      <b><span class="textBlock">Fecha Limite para Seguimiento:</span></b>
      <el-date-picker class="select_width" v-model="message.dialog.fecha.vModelSeguimiento" type="datetime" :placeholder="message.dialog.fecha.PlaceHolder" default-time="12:00:00" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" @change="confirm()">
      </el-date-picker>
      <el-form :model="formInstrucciones" :inline="false" size="normal" class="mt-3">
        <el-form-item label="Instrucciones:">
          <el-input v-model="formInstrucciones.instruccion"></el-input>
        </el-form-item>
        <el-form-item label="Instrucciones Ministro:">
          <el-input v-model="formInstrucciones.ministro"></el-input>
        </el-form-item>
        <el-form-item label="Viceministerio:">
          <el-select v-model="formInstrucciones.viceministerio" placeholder="Seleccione" clearable filterable class="select_width">
            <el-option v-for="item in list_response.listaVice" :key="item.id" :label="item.descripcion" :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>

    </div>

    <span slot="footer" class="dialog-footer">
      <el-button @click="closeDate">Cancelar</el-button>
      <el-button type="primary" @click="setActiveMonitoring" :disabled="message.button">Activar</el-button>
    </span>
  </el-dialog>
</div>
</template>

<style>
.doc {
  width: 100%;
  height: 500px;
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

.block {
  padding: 30px 0;
  text-align: left;
  flex: 1;
}

.textBlock {
  display: block;
  font-size: 14px;
  margin-bottom: 20px;
}

.donwloadFile {
  font-size: 1.5em;
}

.deleteFile {
  font-size: 1.5em;
  color: red;
  margin-left: 5px;
}
</style>
<!--this.src = './../files/' + response.data[0].name + '.pdf';-->

<script>
import dialogExpediente from '../modules/dialogos/expedientes.vue'
import {
  RotateSquare2,
  RotateSquare5
} from "vue-loading-spinner";
import JQuery from 'jquery'
import moment from "moment";
export default {
  components: {
    RotateSquare2,
    RotateSquare5,
    dialogExpediente

  },
  props: {
    csrf: {
      type: String
    }
  },
  data() {
    return {
      publicPath: __dirname,
      event_at: "",
      handlerFile: {
        showPdf: false,
        showLoading: true,
        showError: false,
        code: "",
        dialogComent: false,
        tableComent: false
      },
      search: "",
      message: {
        title: "",
        visible: false,
        width: "",
        bodyText: "",
        closeModal: false,
        button: true,
        dialog: {
          fecha: {
            vModelSeguimiento: "",
            type: "datatime",
            PlaceHolder: "Ingrese fecha de seguimiento",
            defaultTime: "12:00:00",
            file: "",
            tracing: "",
          },
          ministro: "",
          viceministerio: "",
          instruccion: ""
        },
      },
      switchFalse: false,
      drawer: false,
      drawerWord: false,
      limitNumber: 1,
      controlButton: {
        loading: false,
        icon: "",
        disabled: false,
        buttonWord: false,
        buttonPdf: false,
        buttonClose: true,
        buttonPdfClose: true,
      },
      fileList: [],
      datacoment: {
        idDocumento: "",
        idTraslado: "",
        correlativo: "",
        typePdf: "pdf",
        typeExcel: "excel",
        typeWord: "word",
        numberFiles: 0,
      },
      ruleForm: {
        comentario: "",
        id_traslado: "",
        code: ""
      },
      formInstrucciones: {
        instruccion: "",
        ministro: "",
        viceministerio: ""
      },
      documentWord: {
        url: "",
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
        closeDocumento: "closeDocumento",
        uploadFile: "Uploadfile",
        getFiles: "getNameFiles",
        url: "url",
        exists: "exists",
        tracing: "tracingsFiles",
        inactiveTracingFile: "inactiveTracingFile",
        getFileWord: "getFileWord",
        deleteWord: "deleteWord",
        getDireccionesByUser: "getDireccionesByUser",
        getPdfFiles: "getPdfFiles",
        getSeguimientoDocumento: "getSeguimientoDocumento",
        makeBoleta: "makeBoleta",
        listaVice: "listaVice"
      },
      list_response: {
        documentos: [],
        list_dependencia: [],
        list_user: [],
        listcomentarios: [],
        listUrl: [],
        getFileWord: [],
        getDireccionesByUser: [],
        listaVice: [],
        prueba: []
      },
      total: 0,
      currentPage: 1,
      pagesize: 10,
      EditscreenLoading: false,
      dialogo: false,
      interno: false,
      externo: false,
      trasladoUsuario: false,
      idDocumento: 0,
      depActual: 0,
      idTracing: 0,
      form: {
        departamentoId: "",
        usuario: "",
        cc: []
      },
      formExterno: {
        lugar: "",
        correlativo: "",
      },
      formUser: {
        usuario: "",
      },
      formClose: {
        comentarioCierre: "",
      },
      formRule: {
        comentario: [{
          require: true,
          message: "Ingrese comentario",
          trigger: "blur",
        }, ],
      },
      rules: {
        departamentoId: [{
          require: true,
          message: "Seleccione dirección de traslado",
          trigger: "blur",
        }, ],
        usuario: [{
          require: true,
          message: "Seleccione dirección de traslado",
          trigger: "blur",
        }, ],
      },
      rulesClose: {
        comentarioCierre: [{
          require: true,
          message: "Ingrese el comentario",
          trigger: "blur",
        }, ],
      },
      handlerDialog: {
        preview: {
          title: "",
          visible: false,
          width: "85%",
          top: "2vh",
          ver: false,
          html: "",
        },
        inner: {
          title: "Listado de Archivos",
          innerVisible: false,
          width: "50%",
          codeSearch: "",
        },
        previewClose: {
          title: "Cierre del Documento",
          visible: false,
          width: "35%",
          top: "3vh",
          ver: false,
        },
        boleta: {
          // title: "Boleta de delegación",
          visible: false,
          width: "75%",
          top: "3vh",
          ver: false,
          img: './imagenes/gobierno.jpg',
          img2: '/imagenes/gobierno.jpg',
          formato: "",
          correlativo: "",
          fecha: "",
          empresa: "",
          descripcion: "",
          code: "",
          instrucciones_generales: "",
          instrucciones_ministro: "",
          fechaInicio: "",
          fechafin: "",
          viceministerio: ""
        },
      },
      src: "",
      url: "",
      Accept: 2,
      handlerLoading: {
        addComent: false,
      },
    };
  },
  mounted() {
    this.getLista();
    this.selectDireccion();
    this.getUserTransfer();
    this.event_at = moment(new Date()).format('D/MM/YYYY');
    this.getListaVice();

  },
  methods: {
    getListaVice() {
      axios.get(this.url_list.listaVice)
        .then(response => {
          this.list_response.listaVice = response.data
        })
    },
    imprimir() {
      axios.post(this.url_list.makeBoleta, {
          img: this.handlerDialog.boleta.img2,
          correlativo: this.handlerDialog.boleta.correlativo,
          mineco: this.handlerDialog.boleta.formato,
          fecha: this.handlerDialog.boleta.fecha,
          fechaFin: this.handlerDialog.boleta.fechafin,
          descripcion: this.handlerDialog.boleta.descripcion,
          empresa: this.handlerDialog.boleta.empresa,
          ministro: this.handlerDialog.boleta.instrucciones_ministro,
          general: this.handlerDialog.boleta.instrucciones_generales,
          viceministerio: this.handlerDialog.boleta.viceministerio
        }, {
          responseType: 'blob'
        })
        .then(response => {
          var fileURL = window.URL.createObjectURL(new Blob([response.data]));
          console.log(fileURL);
          var fileLink = document.createElement('a');
          fileLink.href = fileURL;
          fileLink.setAttribute('download', 'file.pdf');
          document.body.appendChild(fileLink);
          fileLink.click();
        })

    },
    celdas({
      row,
      column,
      rowIndex,
      columnIndex
    }) {
      if (columnIndex === 6) {
        console.log(row.tracing)
        if (row.tracing === "1") {
          if (row.dias > 0) {
            return "bg-success text-white";
          } else {
            return "bg-danger text-white";
          }
        }
      }
    },
    reload() {
      this.handlerFile.showLoading = true
      this.handlerFile.showPdf = false
      this.getFileCopies(this.handlerFile.code);
    },

    getDireccionesByUser() {
      axios.get(this.url_list.getDireccionesByUser)
        .then(response => {
          this.list_response.getDireccionesByUser = response.data;
        })
    },
    viewFile(valor) {
      console.log(valor);

      this.handlerDialog.preview.html =
        '<body style="margin:0px;"><object data="' +
        this.url +
        '" type="application/pdf" width="100%" height="600"><iframe src="' +
        this.url +
        '" scrolling="no" width="100%" height="100%" frameborder="0" ></iframe></object></body>';
    },

    deleteWord(name, id) {
      console.log(name, id);
      axios
        .put(this.url_list.deleteWord, {
          files: name,
          id: id,
        })
        .then((response) => {
          console.log(response);
          this.getWord();
        });
    },
    filesWord() {
      this.handlerDialog.inner.innerVisible = true;
      this.getWord();
    },
    getWord() {
      axios
        .post(this.url_list.getFileWord, {
          documento: this.handlerDialog.inner.codeSearch,
        })
        .then((response) => {
          console.log(response);
          this.list_response.getFileWord = response.data;
        });
    },
    tableComment({
      row,
      column,
      rowIndex,
      columnIndex
    }) {
      if (rowIndex === 0) {
        return "background-color: #2c3c5c;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    tableHeaderColor({
      row,
      column,
      rowIndex,
      columnIndex
    }) {
      if (rowIndex === 0) {
        return "background-color: #009879;color: #fff;font-weight: 500;text-align: center;";
      }
    },
    closeDate() {
      this.message.visible = false;
      this.message.dialog.fecha.vModelSeguimiento = "";
      // this.message.dialog.instruccion = "";
      // this.message.dialog.ministro = "";
      // this.message.dialog.viceministerio = "";
      this.getLista();
    },
    setActiveMonitoring() {
      axios
        .post(this.url_list.tracing, {
          documento: this.message.dialog.fecha.file,
          tracing: this.message.dialog.fecha.tracing,
          fechaF: this.message.dialog.fecha.vModelSeguimiento,
          instruccion: this.formInstrucciones.instruccion,
          ministro: this.formInstrucciones.ministro,
          viceministerio: this.formInstrucciones.viceministerio
        })
        .then((response) => {
          console.log("respuesta", response)
          this.closeDate();
          // this.getLista();
        });
    },
    confirm() {
      this.message.button = false;
    },
    switchControl(flag, file, tracing) {
      const h = this.$createElement;

      if (flag == "1") {
        this.message.title = "Seguimiento de Expediente";
        this.message.visible = true;
        this.message.width = "30%";
        this.message.dialog.fecha.file = file;
        this.message.dialog.fecha.tracing = tracing;
      } else {
        this.$confirm(
            "¿Desea inactivar el seguimiento de expediente?",
            "Seguimiento", {
              confirmButtonText: "Inactivar",
              cancelButtonText: "Cancelar",
              type: "Warning",
            }
          )
          .then(() => {
            axios
              .post(this.url_list.inactiveTracingFile, {
                documento: file,
                tracing: tracing,
              })
              .then((response) => {
                this.getLista();
              });
          })
          .catch(() => {
            this.getLista();
          });
      }
    },
    verDocumento(flag, type) {
      // this.controlButton.disabled = true;
      // this.controlButton.icon ="el-icon-loading";

      // axios.post(this.url_list.url,{
      //     id: this.datacoment.idDocumento,
      //     formato: type
      // })
      // .then(response => {
      //     const status = JSON.parse(response.status);
      //     const length = JSON.parse(response.data.length);
      //     console.log(response);
      //     if(status == '200' && length > 0){
      //         this.src = './../files/' + response.data[0].file;
      this.drawer = flag;
      //         this.controlButton.disabled = false;
      //         this.controlButton.icon ="";
      //     }

      // })
    },
    submitComent(ruleForm) {
      const h = this.$createElement;
      // console.log(this.$refs[ruleForm].$el[0].onfocus());
      if (this.ruleForm.comentario != "") {
        this.handlerLoading.addComent = true;
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
              this.handlerLoading.addComent = false;
              this.$message({
                message: h("p", null, [
                  h("i", {
                    style: "color: teal"
                  }, "Agregado!"),
                ]),
                type: "success",
              });
              this.ComentLoading = false;
              this.ruleForm.comentario = "";
              // this.handlerDialog.preview.visible = false;

              this.getComentario(
                this.datacoment.idTraslado,
                this.datacoment.idDocumento
              );
            }
          });
      } else {
        this.$notify.error({
          title: "Error",
          message: "Complete el campo de comentario",
        });
        this.$refs[ruleForm].$el[0].focus();
      }
      // });
    },
    archivarDocument(form) {
      const h = this.$createElement;
      if (this.formClose.comentarioCierre != "") {
        // console.log("adentro");
        this.trasladoUsuario = true;
        axios
          .put(this.url_list.closeDocumento, {
            code: this.idDocumento,
            traslado: this.depActual,
            comentario: this.formClose.comentarioCierre,
          })
          .then((response) => {
            const status = JSON.parse(response.status);
            if (status == "200" && response.data != false) {
              this.$message({
                message: h("p", null, [
                  h("i", {
                    style: "color: teal"
                  }, "Documento Archivado!"),
                ]),
                type: "success",
              });
              this.trasladoUsuario = false;
              this.handlerDialog.previewClose.visible = false;
              this.getLista();
              // this.$refs[form].resetFields();
              // this.handlerDialog.preview.visible = false;
              // this.list_response.listComentarios = [];
              // this.getComentario(this.datacoment.idTraslado);
            }
          });
      } else {
        this.$message({
          showClose: true,
          message: "Ingrese comentario",
          type: "error",
        });
        this.$refs[form].$el[0].autofocus = true;
        this.$refs.formClose.$el[0].placeholder =
          "Ingrese Comentario para continuar";
      }
    },
    cierreClose(form) {
      this.handlerDialog.previewClose.visible = false;
      this.$refs[form].resetFields();
    },
    getComentario(traslado, documento) {

      axios
        .post(this.url_list.comentario, {
          code: traslado,
          documento: documento,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          const result = response.data;
          if (status == "200") {
            // console.log("comentarios", response.data)
            this.list_response.listComentarios = response.data;
            console.log("Comentarios: ", response.data);
            this.total = response.data.length;
          }
        });
    },
    getLista() {
      axios.get(this.url_list.message).then((response) => {
        this.list_response.documentos = response.data;
        this.total = response.data.length;
        console.log("lista", response.data);
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
    getTrasladoInterno(id, traslado, tracing) {
      this.interno = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.idTracing = tracing;
      this.getComentario(traslado, id);
    },
    getTrasladoExterno(id, traslado) {
      this.externo = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.getComentario(traslado, id);
    },
    cierreDocumento(id, traslado, formato) {
      this.handlerDialog.previewClose.visible = true;
      this.idDocumento = id;
      this.depActual = traslado;
      this.datacoment.idDocumento = id;
      this.datacoment.idTraslado = traslado;
      this.datacoment.correlativo = formato;
      this.getNameFiles(id);
    },
    boleta(formato, correlativo, fecha, empresa, descripcion, code) {
      this.handlerDialog.boleta.visible = true;
      this.handlerDialog.boleta.formato = formato;
      this.handlerDialog.boleta.correlativo = correlativo;
      this.handlerDialog.boleta.fecha = fecha;
      this.handlerDialog.boleta.empresa = empresa;
      this.handlerDialog.boleta.descripcion = descripcion;
      this.handlerDialog.boleta.code = code;

      axios.post(this.url_list.getSeguimientoDocumento, {
          code: code
        })
        .then(response => {
          console.log(response.data)
          this.handlerDialog.boleta.instrucciones_generales = response.data[0]['instruccion'];
          this.handlerDialog.boleta.instrucciones_ministro = response.data[0]['instruccion_ministro'];
          this.handlerDialog.boleta.fechaInicio = response.data[0]['fechaInicial'];
          this.handlerDialog.boleta.fechafin = response.data[0]['fechaFinal'];
          this.handlerDialog.boleta.viceministerio = response.data[0]['viceministerio'];
        })

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
              Traslado: this.depActual,
              idUsuario: this.form.usuario,
              externo: false,
              tracing: this.idTracing,
              copy: this.form.cc
            })
            .then((response) => {
              this.trasladoUsuario = false;
              this.interno = false;
              this.getLista();
              console.log("transfer true", response.data)
            })
            .catch(error => {
              console.log("errror true", error);
            })
        }
      });
    },
    transferExterno() {
      this.trasladoUsuario = true;
      axios
        .put(this.url_list.TrasladoInterno, {
          Documento: this.idDocumento,
          Traslado: this.depActual,
          idUsuario: this.form.usuario,
          externo: true,
          destino: this.formExterno.lugar,
          correlativoEx: this.formExterno.correlativo,
        })
        .then((response) => {
          this.trasladoUsuario = false;
          this.externo = false;
          this.getLista();
        });
    },
    toAccepts(id, documento) {
      this.getComentario(documento, id);
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
                h("i", {
                  style: "color: teal"
                }, "Operación exitosa"),
              ]),
              type: "success",
            });
            this.Accept = 1;
            this.getLista();
          }
        });
    },
    getUserTransfer() {
      axios.get(this.url_list.getUser).then((response) => {
        this.list_response.list_user = response.data;
      });
    },
    tableRowClassName({
      row,
      rowIndex
    }) {
      if (rowIndex % 2 == 0) {
        return "warning-row";
      } else {
        return "success-row";
      }
      return "";
    },
    preview(code, traslado, correlativo, url) {
      console.log(
        "code: ",
        code,
        " traslado: ",
        traslado,
        " correlativo: ",
        correlativo
      );
      // console.log(code);

      this.handlerDialog.preview.title = "Expediente No. " + correlativo;
      this.ruleForm.id_traslado = traslado;
      this.ruleForm.code = code;

      this.handlerDialog.preview.visible = true;
      this.datacoment.idDocumento = code;
      this.datacoment.idTraslado = traslado;
      this.datacoment.correlativo = correlativo;
      this.url = url;
      this.handlerDialog.inner.codeSearch = code;
      this.handlerFile.showLoading = false
      this.handlerFile.showPdf = true
      this.handlerFile.code = code
      // this.viewFile("primero");
      // this.getNameFiles(code);
    },

    getFileCopies(code) {

      axios.post(this.url_list.getPdfFiles, {
          document: code
        })
        .then(response => {
          console.log("file", response.data)
          if (response.data.length > 0) {
            this.url = './../files/' + response.data[0].file
            this.handlerFile.showPdf = true
            this.handlerFile.showLoading = false
          } else {
            this.handlerFile.showPdf = false
            this.handlerFile.showLoading = false
            this.handlerFile.showError = true
          }
        })
    },
    closeEvent() {
      this.controlButton.buttonWord = false;
      this.controlButton.buttonPdf = false;
      this.src = "";
      this.documentWord.url = "";
      this.ruleForm.comentario = "";
      this.url = "";
      this.handlerFile.showPdf = false
      this.handlerFile.showLoading = true
      this.handlerFile.showError = false
      this.ruleForm.id_traslado = ""
      this.ruleForm.code = ""
      this.list_response.listcomentarios = []
      this.handlerFile.dialogComent = false
      this.handlerFile.tableComent = false
    },
    formCloseDialog() {
      this.formClose.comentarioCierre = ""
      this.controlButton.buttonPdfClose = true
      this.datacoment.idDocumento = ""
      this.datacoment.correlativo = ""
    },
    closeTrasladoInterno() {
      this.form.usuario = ""
      this.form.cc = []
    },
    opened() {
      
      this.handlerFile.dialogComent = true
      
      axios
        .post(this.url_list.comentario, {
          code: this.ruleForm.id_traslado,
          documento: this.ruleForm.code,
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          if (status == "200") {
            this.handlerFile.dialogComent = false
            this.handlerFile.tableComent = true
            // console.log("comentarios", response.data)
            this.list_response.listcomentarios = response.data;
            console.log("Comentarios: ", response.data);
            this.total = response.data.length;
          }
        });
    },
    openEvent() {
      // this.getComentario(this.ruleForm.id_traslado, this.ruleForm.code);


      axios
        .post(this.url_list.exists, {
          id: this.datacoment.idDocumento,
          formato: "word",
        })
        .then((response) => {
          const result = response.data;
          if (result != false) {
            // this.documentWord.url = "./../files/" + response.data[0].file;
            this.controlButton.buttonWord = true;
          } else {
            this.controlButton.buttonWord = false;
          }
        });

      axios
        .post(this.url_list.url, {
          id: this.datacoment.idDocumento,
          formato: "pdf",
        })
        .then((response) => {
          const status = JSON.parse(response.status);
          const length = JSON.parse(response.data.length);
          if (status == "200" && length > 0) {
            this.src = "./../files/" + response.data[0].file;
            // this.drawer = flag;
            // this.controlButton.disabled = false;
            // this.controlButton.icon ="";
            this.controlButton.buttonPdf = true;
          } else {
            this.controlButton.buttonPdf = false;
          }
        });
    },
    handleRemove(file, fileList) {
      let vm = this;
      axios
        .delete("/upload/" + file.uid)
        .then(function () {
          let index = _.findIndex(vm.fileList, ["uid", file.uid]);
          vm.$delete(vm.fileList, index);
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    handlePreview(file) {
      console.log("files", file);
    },
    handleExceed(files, fileList) {
      console.log("error", files, fileList);
      this.$message.warning(
        `El límite es 1, haz seleccionado ${
          files.length
        } archivos esta vez, añade hasta ${files.length + fileList.length}`
      );
    },
    cargaSuccess(res, file, fileList) {
      console.log("resultados ", res, " files ", file, " lista ", fileList);
      const _this = this;

      if (res == false) {
        _this.$notify.error({
          title: "Error",
          message: "Documento no permitido/documento corrupto",
        });
      } else {
        this.$notify.success({
          title: "Carga de Archivo",
          message: "Documento cargado!",
          showClose: false,
        });
        // console.log(res[0][0].file);
        // console.log(this.$refs.viewPDf)
        // this.viewFile("segundo");

        if (res[0][0].formato == "pdf") {
          this.url = "./../files/" + res[0][0].file;
          this.$refs.viewPDf.src = "";
          this.$refs.viewPDf.src = this.url;
          // this.controlButton.buttonPdf = true;
        } else {
          this.controlButton.buttonWord = true;
          this.documentWord.url = "./../files/" + res[0][0].file;
        }
      }
    },
    cargaSuccessClose(res, file, filters) {
      console.log("res ", res, " file ", file, " filters ", filters);
      const _this = this;
      if (res == false) {
        _this.$message({
          message: res.desc,
          type: "warning",
        });
      } else {
        this.$notify.success({
          title: "Carga de Archivo",
          message: "Documento cargado!",
          showClose: false,
        });
        console.log(res[0][0].file);
        this.controlButton.buttonClose = true;
        this.controlButton.buttonPdfClose = false;
      }
    },
    getNameFiles(correlativo) {
      axios
        .post(this.url_list.getFiles, {
          correlativoD: correlativo,
        })
        .then((response) => {
          this.fileList = response.data;

          if (response.data.length > 0) {
            this.limitNumber = 2;

            this.datacoment.numberFiles = response.data.length;

            this.src = "./../files/" + response.data[0].name + ".pdf";
          } else {
            this.datacoment.numberFiles = 0;
          }
        });
    },
  },
};
</script>
