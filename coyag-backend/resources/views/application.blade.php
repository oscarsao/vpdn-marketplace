<!DOCTYPE html>
<html lang="es">
<head>
    <meta property="og:site_name" content="Videoportal de Negocios | España" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Videoportal de Negocios | España</title>
    <!-- Open Graps Protocol -->
    <meta name="description" content="Somos el portal de negocios más exclusivo de España. Presentamos las mejores opciones de negocio a adquirir, con un meticuloso estudio económico y objetivo">
    <meta property="og:title" content="Videoportal de Negocios | España" />
    <meta property="og:url" content="https://www.videoportaldenegocios.es/" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="Somos el videoportal de negocios más exclusivo de España." />
    <meta property="og:image"
        content="https://videoportaldenegocios.es/wp-content/uploads/2023/01/nuevologocompleto-300x70-1.png" />
    <meta property="og:site_name" content="Videoportal de Negocios | España" />
    <meta property="og:image:url"
        content="https://videoportaldenegocios.es/wp-content/uploads/2023/01/nuevologocompleto-300x70-1.png" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="400" />
    <meta property="og:image:height" content="400" />
    <meta property="og:locale:alternate" content="es_ES" />
    <link rel="icon" href="{{ asset('images/logo/nuevoLogoCirculo.png') }}">
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');
body {
    margin: 0;
    padding: 0;
    overflow: hidden;
    font-family: "montserrat";
}
.placeholder {
    width: 100%;
    height: 100vh;;
}
.placeholder__img {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    text-align: center;
}
</style>
<body>
    <div class="placeholder">
        <div class="placeholder__img">
            <img src="{{ asset('logos/CyA-logo.png') }}" alt="Logo VideoPortal de Negocios"> <br>
            <p>Esta es la API de Cohen & Aguirre y Videportal de Negocios</p>
        </div>
    </div>
</body>
</html>
