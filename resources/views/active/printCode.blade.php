<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Document</title>
      <link rel="stylesheet" href="{{public_path('codigo_barra/customCode.css')}}">
      <script src="https://code.jquery.com/jquery-3.5.0.js" integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc=" crossorigin="anonymous"></script>
      <style>
            @page { size:2in 1in; margin: 2cm }
      </style>

</head>
<body>
      <div class="ticket">
            {{ $activos}}
            
      </div>
      <script>
            

            $(document).ready(function() {
                  window.print();
            });
      </script>
</body>
</html>



