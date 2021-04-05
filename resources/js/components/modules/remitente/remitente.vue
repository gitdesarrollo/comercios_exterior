<template>
    <div>
        <div class="card">
            <div class="card-header text-white bg-primary">Catálogo de Remitentes</div>
            <div class="card-body">
                <el-form :model="forms.form" ref="form" :rules="rules" label-width="100px" :inline="false" size="normal">
                    <el-form-item label="Remitente:" prop="sender">
                        <el-input v-model="forms.form.sender"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="onSubmit('form')">Guardar</el-button>
                    </el-form-item>
                </el-form>    
                <el-table :data="responses.list.sender" border stripe :header-cell-style="tableHeaderColor">
                    <el-table-column type="index" label="#"></el-table-column>
                    <el-table-column prop="descripcion" label="Descripción"></el-table-column>
                    <el-table-column label="Opciones" width="100">
                        <template slot-scope="scope">
                            <el-link type="primary" :underline="false" target="_blank" icon="el-icon-delete" @click="deleteSender(scope.row.id)"></el-link>
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
                forms:{
                    form:{
                        sender:""
                    },
                },
                methods:{
                    get:{
                        getSender: 'getSender'
                    },
                    post:{
                        setSender:"setSender"
                    },
                    put:{
                        deleteSender: "deleteSender"
                    }
                },
                responses:{
                    list:{
                        sender:[]
                    }
                },
                rules:{
                    sender:[{
                        required: true, message: 'Ingrese un valor', trigger: 'blur'
                    }]
                }

            }
        },
        methods: {
            onSubmit(form) {
                const h = this.$createElement;
                this.$refs[form].validate( valid => {
                    if(valid){
                        axios.post(this.methods.post.setSender,{
                            sender: this.forms.form.sender
                        }).then( response => {
                            
                            if(response.status == 200){
                                this.$notify({
                                    title: 'Notificación',
                                    message: 'Dato agregado!',
                                    type: 'success',
                                    duration: '4500',
                                    position: 'top-right',
                                    showClose: 'true',
                                });
                                this.$refs[form].resetFields();
                                this.getSender();
                            }else{
                                }
                        })
                        .catch(error => {
                            this.$notify.error({
                                title: "Error",
                                message: "Error al ingresar el dato - " + error,
                            });
                            this.$refs[form].resetFields();
                        })
                    }
                })
            },
            getSender(){
                axios.get(this.methods.get.getSender)
                    .then(response => {
                        this.responses.list.sender = response.data;
                    } )
            },
            tableHeaderColor({ row, column, rowIndex, columnIndex }) {
                if (rowIndex === 0) {
                    return "background-color: #2c3c5c;color: #fff;font-weight: 500;text-align: center;";
                }
            },
            deleteSender(id){
                const h = this.$createElement;
                axios.put(this.methods.put.deleteSender,{
                    id: id
                })
                .then(response => {
                    if(response.data == 1){
                        this.$notify({
                            title: 'Notificación',
                            message: 'Dato Eliminado',
                            type: 'success',
                            duration: '4500',
                            position: 'top-right',
                            showClose: 'true',
                        });
                        this.getSender();
                    }else{
                        this.$notify.error({
                            title: "Error",
                            message: "Error al intentar borrar el dato",
                        });
                    }
                })
            }
        },
        mounted () {
            this.getSender();
        },
    }
</script>
