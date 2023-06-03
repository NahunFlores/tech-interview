<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" value="{{ csrf_token() }}">
    <title>Sistema de Gestión de Tareas y Proyectos</title>

    <!-- CDNs de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- CDNs de Font-Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CDNs de googleapis -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Open+Sans:wght@300;400&display=swap"rel="stylesheet">

    <!-- CDNs de laravel mix -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ mix('css/navbar.css') }}" type="text/css">

    <!-- Icono -->
    <link rel="shortcut icon" href=" {{ asset('img/favicon.ico') }}" type="image/x-icon">
</head>
<body>

    <div id="app">
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/navbar.js') }}" type="text/javascript"></script>

</body>
</html>
