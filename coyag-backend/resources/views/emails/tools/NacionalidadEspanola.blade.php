@extends('emails.layouts.immigration')
@section('template_title')
@endsection
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
        <td bgcolor="#ffffff" align="center" valign="top" style="padding: 5px 20px 5px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 1px; line-height: 48px;">
            <h1 style="font-size: 24px; font-weight: 400;">¡Hola!</h1>
        </td>
    </tr>
    @if( isset($result) )
    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 10px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 32px;">
            <h2 style="color: {{$resultType}}">Estado de esta semana: {{$result}}</h2>
        </td>
    </tr>
    @else
    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 10px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;">Esta semana no hubo novedades en su <b>Trámite de nacionalidad Española</b>.</p>
        </td>
    </tr>
    @endif

    @if( isset($document) )
    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 10px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;"><a href="{{$document}}" target="_blank"> Descarga el BOE relacionado aqui </a></p>
        </td>
    </tr>
    @endif
   
    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 10px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p><b>Ν.Ι.Ε:</b> {{$nie}} </p>
            <p><b>Número de expediente de extranjería:</b> {{$number}} </p>
            <p><b>Ultima Actualización:</b> {{ date('Y-m-d') }}</p>
            <p>
              La información que se ofrece en este email no tiene carácter de notificación. Los trámites posteriores a la concesión se deben realizar en el Registro Civil.
            </p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 10px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;">Recuerde que puede consultar el estatus de su servicio en:</p>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 60px 30px;">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="border-radius: 3px;" bgcolor="#B41F26">
                                    <a href="https://cohenyaguirre.es/anauco" target="_blank" style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #B41F26; display: inline-block;">Servicio de Extranjería</a>
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
            <p style="margin: 0;">Si el enlace no funciona, copie y pegue esta dirección en su navegador web:</p>
            <p style="margin: 0;"><a target="_blank" style="color: #B41F26;">https://cohenyaguirre.es/anauco</p>
        </td>
    </tr>
@endsection 