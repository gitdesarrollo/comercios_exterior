@component('mail::message')
# Estimado/a {{ $usuario_to }}

<p>Se te ha sido asignado un documento que contiene la siguiente Información:</p>
<ul>
    <li><b>Empresa:</b> {{ $empresa_to }} </li>
    <li><b>Correlativo:</b>  {{ $numero_to }} </li>
    <li><b>Correlativo Interno:</b>  {{ $interno }} </li>
    <li><b>Asunto:</b> {{ $asunto_to }} </li>
    <li><b>Con copia a:</b>
      <br>
    @foreach($copias as $item)
       - {{ $item['user'] }} 
    @endforeach
       <br>
    </li>
      <li><b>Instrucción:</b> {{ $instrucciones }} </li>
      <li><b>Fecha de entrega:</b> {{ $fecha }} </li>
</ul>
<p>Ingresa a tu panel del sistema de documentos</p>
<a href="http://documentos.mineco.gob.gt">https://documentos.mineco.gob.gt</a>



Saludos,<br>
{{ config('app.name') }}
@endcomponent
