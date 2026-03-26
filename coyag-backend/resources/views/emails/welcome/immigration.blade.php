@extends('emails.layouts.immigration')


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
        <td bgcolor="#ffffff" align="center" valign="top" style="padding: 5px 20px 5px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
            <h1 style="font-size: 24px; font-weight: 400; margin: 1;">¡Estrenamos Intranet!</h1>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 16px 0px 0px 32px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; color: #3d4852; font-size: 18px; font-weight: bold;">Estimado(a): {{ $clientFullName }},</p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; font-size: 18px;">En Cohen&Aguirre hemos desarrollado una primera versión de una aplicación para facilitar la revisión y control de la gestión de su proceso de extranjería con nosotros.</p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 0px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; font-size: 18px;">Esta nueva herramienta le permitirá:</p>
            <ul class="parrafo" style="font-size:18px;"">
                <li>Visualizar el estatus exacto de su servicio de Extranjería.</li>
                <li>Visualizar el Planning con los tiempos estimados de su proceso residencia.</li>
                <li>Visualizar la cantidad de días que restan para su llegada a España.</li>
                <li>Carga, descarga y gestión de estatus de documentos.</li>
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
                                    <a href="{{ $linkWebApp }}" target="_blank" style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 0px solid #B41F26; display: inline-block;">{{ $serviceName }}</a>
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
            <p style="margin: 0;">Sus credenciales de acceso son:</p>
            <ul style="list-style:none; font-size: 18px;">
                <li><b>Correo electrónico</b>: {{ $clientEmail }}</li>
                <li><b>Contraseña*</b>: {{ $password }}</li>
            </ul>
            <p><b>*</b> Utilice su contraseña habitual si tiene algún otro servicio contratado con nosotros.</p>
            <p>Contacte a su asesor si tiene algún problema con sus credenciales de acceso.</p>
            <p>Puede acceder desde el móvil, pero recomendamos acceder desde el ordenador para visualizar mejor el contenido.</p>
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


