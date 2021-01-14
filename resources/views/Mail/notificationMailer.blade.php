<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Sistema de documentos</title>
</head>
<body>
<p>Estimado Usuario:  {{ $usuarioTo }}</p>
<p>El documento de tipo {{ $typeDocument }} con número de correlativo interno {{ $internalCorrelative  }} y número de correlativo externo {{ $externalCorrelative }}</p>
<p>ha sido aceptado por {{ $receivingUser }}</p>


<a href="http://documentos.mineco.gob.gt">https://documentos.mineco.gob.gt</a>
<p>Saludos.</p>

</body>
</html>
