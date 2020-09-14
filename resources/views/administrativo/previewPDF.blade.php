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
                    <td class="fecha">DTI-OF-68-2019  </td>
                </tr>
                <tr>
                    <td class="fecha"><?php echo $fecha; ?> </td>
                </tr>
            </tbody>
        </table>

        <p>Sra.</p>
        <p>Rosario Moreira de Giron</p>
        <p>Sub-Secretaria general</p>
        <p>Ministerio de Economia</p>


        <p>
        De manera atenta me dirijo a usted para hacer referencia al oficio 12-UIP-MINECO-2020 en donde nos solicitan el apoyo para verificar algunos inconvenientes que se le presentan a la encargada de la Unidad de Información Pública en la plataforma del portal web. Cabe mencionar que estamos reenviando nuevamente este oficio ya que el del 18 de mayo se envió con un mensajero, pero los compañeros fueron puestos en cuarentena y no sabíamos si lo habían entregado pues nos vino de vuelta de recibido.

Por esa razón, informamos que nos presentamos en el lugar donde se encuentra la Sra. Ena Herrera, y le explicamos las pruebas que se iban a realizar para que ella viera las mismas, y que este enterada en lo que en este oficio se expone.

Pruebas realizadas:

PRUEBA REALIZADA	ESTADO
Se hizo un testeo del tráfico de la red	Funciona Correctamente
Se verificó la señal que le llega al equipo, y está en carga con 14 a 16 megas, la descarga está en 2 megas	Carga bastante rápida de archivos de 1 a 8 megas
La navegación del portal web es rápida, así como también el acceso a la plataforma del usuario de la UIP	Rápido acceso y navegación en enlaces
Ena realizó prueba de carga de información con dos archivos PDF y distinto peso, uno de 5.61 megas y el otro de 8.16 Megas, la carga la hizo sin ningún inconveniente
	Carga rápida de archivos de 5 y 8 megas
Visualización de los archivos después de la carga	Se visualizan los archivos actualizados correctamente




Derivado de lo anterior, y con la Sra. Ena Herrera presente, se pudo dar cuenta que no hubo ningún inconveniente trabajar en la sección de “Transparencia” del portal web institucional, por lo que haremos unas recomendaciones para que la usuaria pueda poner en práctica y asi darle un mejor uso a la herramienta.

Recomendaciones:
•	No dejar demasiado tiempo sin trabajar en la plataforma ya que, al volver a reiniciar la labor de carga y actualizar, este no guardará los datos puesto que el usuario automáticamente se desconecta por seguridad.
•	Se le enseño a la encargada como borrar los archivos desactualizados para no cargar el servidor con archivos que ya no se utilizan puesto que eso roba espacio en disco y perjudica a la hora de cargar archivos recientes, la carga se vuelve lenta.
•	Se ha intentado cargar archivos de 25 a 70 megas, dado que el tráfico de red tiene un rango de 14 a 16 megas de carga, es lógico que no solo será muy lenta la respuesta, sino que en el transcurso de tiempo que intente subir un archivo, la plataforma no guardará y se desconectará, dado como resultado no actualizar la información.
•	No dar clic seguidos cuando el sistema este procesando una petición, al dar demasiados clics creyendo que el sistema responderá, únicamente hace que el ordenador se trabe en un proceso y deje de funcionar, incluyendo el portal web.



Atentamente;

        </p>

    </div>
    
</body>
</html>