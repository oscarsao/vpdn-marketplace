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
            <p class="parrafo" style="margin: 0; color: #3d4852; font-size: 18px; font-weight: bold;">Estimado(a): {{ $object->nameClient }} {{ $object->surnameClient }},</p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            @if ( $object->notificationType == 'pre.notification')
                <p class="parrafo" style="margin: 0; font-size: 18px;">Te recordamos que tu <b>{{ $object->namePlan }}</b> tiene como fecha de vencimiento el día <b>{{ $object->planExpirationDate }}</b>.</p>
            @else
                <p class="parrafo" style="margin: 0; font-size: 18px;">Hoy es el vencimiento de tu <b>{{ $object->namePlan }}</b>.</p>
            @endif
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            @if ($object->notificationType == 'pre.notification')
                <p class="parrafo" style="margin: 0; font-size: 18px;">Asegúrate de contactar a un asesor para seguir disfrutando del servicio.</p>
            @else
                <p class="parrafo" style="margin: 0; font-size: 18px;">Debe contactar a un Asesor para renovar el servicio. Si ya se comunicó con uno, descarte este mensaje.</p>
            @endif
        </td>
    </tr>

    @if ( $object->slugPlan == 'plan.free.trial')
        <tr>
            <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                <p style="margin: 0;">Si tienes cualquier duda puedes escribirnos directamente a nuestro Whatsapp:</p>
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


    @endif

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; font-size: 18px;">Gracias por confiar en nosotros.</p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; font-size: 18px;"><b>¡Feliz día!</b></p>
        </td>
    </tr>

@endsection
