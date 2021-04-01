@component('mail::message')
# Estimado/a {{ $actual }}

<p>El expediente con número de correlativo: {{ $correlativo }}</p>
<p>Se le notifica que: <strong> {{ $message }}</strong> </p> 



Saludos,<br>
{{ config('app.name') }}
@endcomponent
