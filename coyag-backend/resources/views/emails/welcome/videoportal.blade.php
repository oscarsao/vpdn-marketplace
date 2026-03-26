@extends('emails.layouts.videoportal')

@section('template_css')
    <style>
        .my-button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        .espaciado-sup {
            margin-top: 30px !important;
        }
    </style>
@endsection

@section('template_text')

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 16px 0px 0px 32px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; color: #3d4852; font-size: 18px; font-weight: bold;">Estimado(a): {{ $clientFullName }},</p>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; font-size: 18px;">Agradecemos tu interés en Videoportal de Negocios, la plataforma más completa de Búsqueda de Negocios en venta en España.</p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 0px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; font-size: 18px;">Esperamos poder acompañarte y apoyarte en tu búsqueda de la mejor oportunidad de inversión en España. </p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 0px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; font-size: 18px;">A continuación te dejamos tus datos para acceder a la plataforma.</p>
            <ul style="list-style:none; font-size: 18px;">
                <li><b>Correo electrónico</b>: {{ $clientEmail }}</li>
                <li><b>Contraseña</b>: {{ $password }}</li>
            </ul>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;">Puedes ingresar haciendo click en el siguiente botón:</p>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td bgcolor="#ffffff" align="center" style="padding: 5px 15px 15px 15px;">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="border-radius: 50px;" bgcolor="#B41F26">
                                    <a href="{{ $linkWebApp }}" target="_blank" style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 0px solid #B41F26; display: inline-block;">ACCEDER A VIDEOPORTAL</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;">Si tienes cualquier duda durante el proceso puedes escribirnos directamente a nuestro Whatsapp:</p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td bgcolor="#ffffff" align="center" style="padding: 5px 15px 15px 15px;">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="border-radius: 3px;" bgcolor="#ffffff">
                                    <a href="https://wa.me/34641415190?text=Hola,%20" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 2px; border: 1px solid #ffffff; display: inline-block;">
                                        <img src="https://api.cohenyaguirre.es/logos/other_platforms/logo_whasatpp.png" width="150" style="display: block; border: 0px;" />
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>


    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;"> Por último, recuerda que puedes agendar una asesoría con un agente en el siguiente enlace:</p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td bgcolor="#ffffff" align="center" style="padding: 5px 15px 15px 15px;">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="border-radius: 3px;" bgcolor="#ffffff">
                                    <a href="https://bit.ly/vpdn-onboarding" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 2px; border: 1px solid #ffffff; display: inline-block;">
                                        <img src="https://api.cohenyaguirre.es/logos/other_platforms/logo_calendly.png" width="150" style="display: block; border: 0px;" />
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 0px 30px 15px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;">Puede copiar y pegar el siguiente enlace en su navegador para acceder a <b>{{ $serviceName }}</b>:</p>
            <p style="margin: 0;"><a target="_blank" style="color: #B41F26;">{{ $linkWebApp }}</p>
            <br>
        </td>
    </tr>

@endsection

