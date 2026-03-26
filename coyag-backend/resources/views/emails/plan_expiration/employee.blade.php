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
            <p class="parrafo" style="margin: 0; color: #3d4852; font-size: 18px; font-weight: bold;">¡Hola!</p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 16px 0px 0px 32px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            @if ( $object->notificationType == 'pre.notification')
                <p class="parrafo" style="margin: 0; font-size: 18px;">Te recordamos que el <b>{{ $object->namePlan }}</b> del Cliente <b>{{ $object->nameClient }}</b> <b>{{ $object->surnameClient }}</b> (<b>{{ $object->emailClient }}</b>) tiene como fecha de vencimiento el día <b>{{ $object->planExpirationDate }}</b>.</p>
            @else
                <p class="parrafo" style="margin: 0; font-size: 18px;">Hoy es el vencimiento del <b>{{ $object->namePlan }}</b> del Cliente <b>{{ $object->nameClient }}</b> <b>{{ $object->surnameClient }}</b> (<b>{{ $object->emailClient }}</b>).</p>
            @endif
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 16px 0px 0px 32px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; font-size: 18px;">Asegúrate de contactarlo.  Si ya lo hiciste, entonces descarta este mensaje.</p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 10px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-weight: 400; line-height: 25px;">
            <p class="parrafo" style="margin: 0; font-size: 18px;"><b>¡Saludos!</b></p>
        </td>
    </tr>

@endsection

