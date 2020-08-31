<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Invetario</title>
      <style>

            @page{
                  margin-left: 2cm;
                  margin-right:2cm;
                  margin-top:2cm;
                  margin-bottom:2cm;
            }


            *{
                  font-size: 12px;
                  font-family: "Times New Roman";
            }
            
            .container {
                  width: 100%;    
            }
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
                  font-size: 12px;
                  text-align: justify;
            }

            .handler_for_Barcode > div{
                  margin:auto;
            }

            /* .table-bordered {
                  border: 1px solid #000;
            }

            .table-bordered th,
            .table-bordered td {
                  border: 1px solid #000 !important;
            }

            
            .table-bordered thead th,
            .table-bordered thead td {
                  border-bottom-width: 2px;
            } */

            table {
                  font-family: verdana !important;
                  font-size: 11px;
                  width: 100%;
                  border-spacing: 0;
                  border-collapse: collapse;
                  border: none;
            }

            .table-sm th,
            .table-sm td {
                  padding: 0.3rem;
            }
            
            .border {
                  border: 1px solid #000 !important;
            }
            .table-bordered {
            border: 1px solid #dee2e6;
            }
            .table-bordered th,
            .table-bordered td {
                  border: 1px solid #dee2e6;
            }
            .table-bordered thead th,
            .table-bordered thead td {
                  border-bottom-width: 2px;
            }
            .mb-2,
            .my-2 {
                  margin-top: 1.2rem !important;
            }

            .border  tr, .border th, .border td{
                border: 1px solid #000 !important;
            }
            

      </style>
</head>
<body>
      <div >
            <div >
                  <div class="container">
                        <table class="table table-bordered border my-2">
                              <thead>
                                    <tr class="thead-dark">
                                          <th class="handler_producto">No.</th>
                                          <th class="handler_producto">Fecha</th>
                                          <th class="handler_producto">No. Bien</th>
                                          <th class="handler_Barcode">Producto</th>
                                          <th class="handler_producto">Sistema</th>
                                          <th class="handler_producto">Físico</th>
                                          <th class="handler_producto">Direfencia</th>
                                    </tr>
                              </thead>
                              <tbody>
                                    @foreach( $activos as $index => $productos)
                                          <tr>
                                                <td class="handler_for_product">{{ ($index+1) }}</td>
                                                <td class="handler_for_product">{{ $productos->fecha_ingreso }}</td>
                                                <td class="handler_for_product">{{ $productos->codigo_sicoin }}</td>
                                                <td class="handler_for_description">{{ $productos->descripcion }}</td>
                                                <td class="handler_for_product">{{ $productos->cantidad }}</td>
                                                <td class="handler_for_product"></td>
                                                <td class="handler_for_product"></td>
                                                
                                          </tr>
                                    @endforeach
                              </tbody>
                        </table>
                  </div>
            </div>
      </div>
</body>
</html>

