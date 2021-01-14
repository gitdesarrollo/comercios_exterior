<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Recepcion de documentos</title>
</head>
<body>
    <p>Hola! {{ $usuario_to }}</p>
    <p>se te ha sido asignado un documento que contiene la siguiente información:</p>
    <ul>
        <li><b>Empresa:</b> {{ $empresa_to }} </li>
        <li><b>Correlativo:</b>  {{ $numero_to }} </li>
        <li><b>Correlativo Interno:</b>  {{ $interno }} </li>
        <li><b>Asunto:</b> {{ $asunto_to }} </li>
    </ul>
    <p>entra a tu panel del sistema de documentos y acepta la recepción</p>
    <a href="http://documentos.mineco.gob.gt">https://documentos.mineco.gob.gt</a>
    <p>Saludos.</p>

</body>
</html>