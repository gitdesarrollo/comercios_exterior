<template>
    <div> 
        <el-row :gutter="10">
            <el-col :xs="25" :sm="6" :md="8" :lg="20" :xl="20"> 
                Cuenta: 
                
                <el-select v-model="select_item" class="select_width" clearable filterable @change="getBienesAll" placeholder="Seleccionar">
                    <el-option
                        v-for="item in items_account"
                        :key="item.id_cuenta"
                        :label="item.descripcion"
                        :value="item.id_cuenta"
                        >
                    </el-option>
                </el-select>
                
            </el-col>
        </el-row>
        <div >
            <!-- <el-link :underline="false" v-bind:href="'/printer/'+select_item" 
                class=" mt-3 mb-3" target="_blank"
                >
                    <el-button type="success" icon="el-icon-printer"></el-button>
            </el-link>
            
            <el-button  class="my-5" type="success"  @click="exportExcel" >Exportar</el-button> -->
            
            <!-- <a href="./printer/" class="btn btn-primary  mt-3 mb-3" target="_blank">
                                <i class="fas fa-print"></i> 
                            </a> -->
            <!-- <el-table
                :data="bien.slice(
                                (currentPage - 1) * pagesize,
                                currentPage * pagesize
                            )"
                style="width:100%"
                
                >
                <el-table-column label="No." type="index"></el-table-column>
                <el-table-column label="Fecha" prop="fecha" width="100"></el-table-column>
                <el-table-column label="No. Bien" prop="sicoin" width="100"></el-table-column>
                <el-table-column label="Producto" prop="producto" ></el-table-column>
                <el-table-column label="Sistema" prop="cantidad" width="80"></el-table-column>
                <el-table-column label="Fisico"  prop="fisico" width="80"></el-table-column>
                <el-table-column label="Diferencia" prop="diferencia" width="90"></el-table-column>
                <el-table-column label="Lugar" prop="lugar" width="150"></el-table-column>
                <el-table-column label="Empleado" prop="empleado" width="150"></el-table-column>
            </el-table> -->
            <!-- <div style="text-align: left;margin-top: 30px;">
                <el-pagination
                    background
                    layout="total,prev, pager, next"
                    :total="total"
                    @current-change="current_change"
                ></el-pagination>
            </div> -->
            <div v-show="show_table_hidden" ref="content"> 
                <el-button  class="my-5" type="success" icon="el-icon-printer" @click="download" ></el-button>
                <table class="table table-bordered border my-2" id='exportData'>
                              <thead>
                                    <tr class="thead-dark">
                                          <th class="handler_producto">No.</th>
                                          <th class="handler_producto">Fecha</th>
                                          <th class="handler_producto">No. Bien</th>
                                          <th class="handler_Barcode">Producto</th>
                                          <th class="handler_producto">Sistema</th>
                                          <th class="handler_producto">Físico</th>
                                          <th class="handler_producto">Diferencia</th>
                                          <th class="handler_producto">Lugar</th>
                                          <th class="handler_producto">Empleado</th>
                                    </tr>
                              </thead>
                              <tbody>
                                    
                                          <tr v-for="(item, i) in bien" v-bind:key="i">
                                                <td class="handler_for_product">{{ (i+1) }}</td>
                                                <td class="handler_for_product">{{ item.fecha }}</td>
                                                <td class="handler_for_product">{{ item.sicoin }}</td>
                                                <td class="handler_for_description">{{ item.producto }}</td>
                                                <td class="handler_for_product">{{ item.cantidad }}</td>
                                                <td class="handler_for_product">{{ item.fisico}}</td>
                                                <td class="handler_for_product">{{ item.diferencia }}</td>
                                                <td class="handler_for_product">{{ item.lugar }}</td>
                                                <td class="handler_for_product">{{ item.empleado }}</td>
                                                
                                          </tr>
                                   
                              </tbody>
                        </table>
            </div>
            
        </div>
    </div>
</template>

<style lang="css">
    .handler_producto {
                  width: 5%;
                  text-align: center;
                 
            }

            .handler_Barcode{
                  width: 30%;
                  text-align: center;
            }

            .handler_for_product{
                  font-size: 12px;
                  text-align: center;
            }
            .handler_for_description{
                  font-size: 8px;
                  text-align: justify;
            }

            .handler_for_Barcode > div{
                  margin:auto;
            }
</style>
<script>
        // import FileSaver from 'file-saver'
        import XLSX from 'xlsx'
        import jsPDF from 'jspdf' 
        import html2canvas from "html2canvas"
        import domtoimage from "dom-to-image";
        import 'jspdf-autotable' 

export default {
    data() {
        return {
            total: 0,
            currentPage: 1,
            pagesize: 10,
            bien:[],
            select_item:"",
            items_account:[],
            url:{
                    getBienes: 'reportInventory',
                    getAccountInitial: 'getAccountInitial',
                },    
            show_table: true,
            show_table_hidden: false,
        }
    },
    mounted() {
        
        this.getAccount();
    },
    methods: {
        download() {
        const doc = new jsPDF();

        doc.autoTable({
            html: '#exportData',
            theme: 'striped',
            styles: {cellWidth: 'auto', fontSize: 8, halign: 'center'},
            bodyStyles: {fontSize: 8, halign: 'left'},
           
            
            
            });
        doc.save('Informe de Cuentas.pdf')

        // const contentHtml = this.$refs.content.innerHTML;
        // doc.fromHTML(contentHtml, 15, 15, {
        //     width: 170
        // });
        // doc.save("sample.pdf");
        },
        getAccount(){
            axios.get(this.url.getAccountInitial).then(response => {
                this.items_account = response.data;
            })
        },
        getBienesAll(){
            this.show_table_hidden = true;
            this.bien = [];
            axios.post(this.url.getBienes,{
                account: this.select_item
            }).then(response => {
               
                this.bien = response.data;
                this.total = response.data.length;
                
            }).catch(erro => {
                //  consola.log(erro);
            })
        },
        current_change: function(currentPage) {
            this.currentPage = currentPage;
        },

         exportExcel () {
                  var table = 'exportData';
                  var name = 'exportData';
                  var DocumentName = "exportData";
                  var link = document.createElement('a');
                  link.download = DocumentName + '.xls';
                  var uri = 'data:application/vnd.ms-excel;base64,'
                  , template = '<html  xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
                  , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                  , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                        if (!table.nodeType) table = document.getElementById(table)
                        var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                        // window.location.href = uri + base64(format(template, ctx))
                        link.href = uri + base64(format(template, ctx));
                        link.click();
                  
            },
        export2(){
            // let data = XLSX.utils.json_to_sheet(this.bien)
            const tbl = document.getElementById('exportData')
            let data = XLSX.utils.table_to_book(tbl)
            const workbook = XLSX.utils.book_new()
            const filename = 'devschile-admins'
            XLSX.utils.book_append_sheet(workbook, data, filename)
            XLSX.writeFile(workbook, `${filename}.xlsx`)
        },

        downloadWithCSS() {
            const doc = new jsPDF();
            /** WITH CSS */
            var canvasElement = document.createElement('canvas');
                html2canvas(this.$refs.content, { canvas: canvasElement 
                }).then(function (canvas) {
                const img = canvas.toDataURL("image/jpeg", 0.8);
                doc.addImage(img,'JPEG',20,20);
                doc.save("sample.pdf");
            });
        },
        downloadWithCSS2() {

        /** WITH CSS */
            domtoimage
            .toPng(this.$refs.content)
            .then(function(dataUrl) {
            var img = new Image();
            img.src = dataUrl;
            const doc = new jsPDF({
                orientation: "portrait",
                // unit: "pt",
                format: [900, 1400]
            });
            doc.addImage(img, "JPEG", 20, 20);
            const date = new Date();
            const filename =
                "timechart_" +
                date.getFullYear() +
                ("0" + (date.getMonth() + 1)).slice(-2) +
                ("0" + date.getDate()).slice(-2) +
                ("0" + date.getHours()).slice(-2) +
                ("0" + date.getMinutes()).slice(-2) +
                ("0" + date.getSeconds()).slice(-2) +
                ".pdf";
            doc.save(filename);
            })
            .catch(function(error) {
            console.error("oops, something went wrong!", error);
            });
        },
    },
}
</script>