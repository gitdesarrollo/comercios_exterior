<?php
$hoy = getdate();

switch ($hoy['mon']) {
    case 1:
        $mes = 'Enero';
        break;
    case 2:
        $mes = 'Febrero';
        break;
    case 3:
        $mes = 'Marzo';
        break;
    case 4:
        $mes = 'Abril';
        break;
    case 5:
        $mes = 'Mayo';
        break;
    case 6:
        $mes = 'Junio';
        break;
    case 7:
        $mes = 'Julio';
        break;
    case 8:
        $mes = 'Agosto';
        break;
    case 9:
        $mes = 'Septiembre';
        break;
    case 10:
        $mes = 'Octubre';
        break;
    case 11:
        $mes = 'Noviembre';
        break;
    case 12:
        $mes = 'Diciembre';
        break;
    
    default:
        # code...
        break;
}

$fecha = "Guatemala, $hoy[mday] de $mes de $hoy[year] ";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
<style>
    .fecha{
        text-align: right;
        padding-right: 30% !important;
    }
</style>
</head>
<body>
    <div class="container">
        <table class="table" >
            <tbody>
                <tr>
                    <td>
                        <img src="{{ asset('pdf/img/mineco.png') }}" >
                    </td>
                </tr>
                <tr>
                    <td class="fecha"><?php echo $fecha; ?> </td>
                </tr>
            </tbody>
        </table>

    </div>
    
</body>
</html>