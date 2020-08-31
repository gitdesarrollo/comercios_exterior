<template>
    <div>
        <div class="card">
            <div class="card-header text-white bg-primary">
                Sistema de SICOIN
            </div>
            <div class="card-body">
                <el-form 
                ref="form"
                :model="form"
                :rules="rules"
                label-width="150px">
                <el-form-item label="SICOIN" prop="name">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-link
                        :underline="false"
                        target="_black"
                        v-bind:href="
                            '/printCode/' +
                                form.name">
                        <el-button type="primary" icon="el-icon-search" >
                        </el-button>
                    </el-link>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
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
            urlData: {
                    searchCode: "printCode/",
                },
        }
    },
    methods: {
        onSubmit(form) {
            console.log(this.urlData.searchCode)
            const h = this.$createElement;
            this.$refs[form].validate(valid => {
                if (valid) {
                    this.fullscreenLoading = true;
                    axios
                        .get(this.urlData.searchCode+this.form.name)
                        .then(response => {
                            const status = JSON.parse(response.status); 
                            if (status == "200") {
                                // this.$message({
                                //     message: h("p", null, [
                                //         h(
                                //             "i",
                                //             { style: "color: teal" },
                                //             "Producto Agregado!"
                                //         )
                                //     ]),
                                //     type: "success"
                                // });
                                this.fullscreenLoading = false;
                                this.form.code = "";
                                
                            }
                        });
                } else {
                    this.$message.error({
                        message: h("p", null, [
                            h(
                                "i",
                                { style: "color: red" },
                                "Complete datos" 
                            )
                        ])
                    });
                    return false;
                }
            });
        },
    }
}
</script>