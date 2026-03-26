@extends('emails.layouts.app')


@section('template_title')
@endsection



@section('template_css')
    <style>
        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            transition: 0.3s;
            width: 80%;
        }

        .card:hover {
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
        }

        .container {
            padding: 2px 16px;
        }
    </style>
@endsection



@section('template_text')
    <tr>
        <td bgcolor="#ffffff" align="center" valign="top"
            style="padding: 5px 20px 5px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 1px; line-height: 48px;">
            <h1 style="font-size: 24px; font-weight: 400;">¡Hola, {{ $client->names }}!</h1>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left"
            style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;">A continuación listamos los <b>Negocios</b> que se adaptan a tus preferencias.</p>
        </td>
    </tr>

    @foreach ($businessesPerClient as $business)
        <tr>
            <td bgcolor="#ffffff" align="center"
                style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">

                <a href="https://videoportaldenegocios.es/videoportal/#/listado-general/{{ $business->id }}" target="_blank"
                    style="color: inherit;  text-decoration: inherit;">
                    <div class="card">
                        <img src="{{ $business->image_1 }}" alt="imagen_negocio" style="width:100%">
                        <div class="container">
                            <h4><b>{{ $business->name }}</b></h4>
                            <p>{{ substr($business->description, 0, 70) }} {{ $business->description ? '...' : '' }}</p>
                        </div>
                    </div>
                </a>

            </td>
        </tr>
    @endforeach

    <tr>
        <td bgcolor="#ffffff" align="left"
            style="padding: 0px 30px 0px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;">Haz click en el siguiente enlace para no seguir recibiendo <b>Recomendaciones de
                    Negocios</b></p>
            <p style="margin: 0;"><a href="https://api.cohenyaguirre.es/unsubscribe-bp?token={{ $token }}"
                    target="_blank" style="color: #B41F26;">Aquí</a></p>
            <br>
            <p style="margin: 0;">O también puedes configurar esta opción en nuestro:</p>
            <p style="margin: 0;"><a href="https://videoportaldenegocios.es/videoportal/" target="_blank"
                    style="color: #B41F26;">videoportaldenegocios.es</a></p>
        </td>
    </tr>
@endsection
