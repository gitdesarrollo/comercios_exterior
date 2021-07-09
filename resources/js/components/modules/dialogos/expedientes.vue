<template>
<div>
  dialogo sf
  <el-dialog :title="handlerDialog.preview.title" :visible.sync="handlerDialog.preview.visible" :width="handlerDialog.preview.width" :top="handlerDialog.preview.top"  destroy-on-close>
    <!-- <el-row :gutter="10">
      <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12"> -->
        <!-- <el-table :data="list_response.listComentarios" :header-cell-style="tableComment" :row-class-name="tableRowClassName" height="450" border>
          <el-table-column label="No." type="index"></el-table-column>
          <el-table-column label="Usuario" prop="usuario" width="180"></el-table-column>
          <el-table-column label="Comentario" prop="comentario"></el-table-column>
        </el-table> -->
        <!-- <el-form :model="ruleForm" ref="ruleForm" label-width="120px" label-position="top">
          <el-form-item label="Comentario:" prop="comentario" :rules="formRule.comentario">
            <el-input type="textarea" v-model="ruleForm.comentario" maxlength="1000" show-word-limit></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="submitComent('ruleForm')" :loading="handlerLoading.addComent">Guardar
            </el-button>
          </el-form-item>
        </el-form> -->
        <!-- <el-col :xs="4" :sm="4" :md="2" :lg="2" :xl="2">
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
        </el-col> -->
        <!-- <el-button v-if="controlButton.buttonWord" type="primary" @click="filesWord()">Archivos</el-button>
      </el-col> -->
      <!-- <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
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
      </el-col> -->
    <!-- </el-row> -->
    <!-- <el-dialog :width="handlerDialog.inner.width" :title="handlerDialog.inner.title" :visible.sync="handlerDialog.inner.innerVisible" append-to-body>
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
    </el-dialog> -->
  </el-dialog>
</div>
</template>

<script>

export default {
  name: 'dialogExpediente',
  // props: ['title', 'visible'],
  data() {
    return {
      handlerDialog: {
        preview: {
          title: "",
          visible: false,
          width: "85%",
          top: "2vh",
          ver: false,
          html: "",
        },
      }
    }
  },
  mounted() {
    console.log("modulo")
  },
}
</script>

<style lang="scss" scoped>
</style>
