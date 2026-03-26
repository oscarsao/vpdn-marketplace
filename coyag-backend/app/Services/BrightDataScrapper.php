<?php
namespace App\Services;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Smalot\PdfParser\Parser;
class BrightDataScrapper
{
    public static function ExpedienteNacionalidad($args)
    {
        return (object) array(
            'error' => true,
            'result' => "NOT AVAILABLE",
            'resultType' => 'warning',
        );
        
        /* ON HOLD */
        /*
        $client = new Client();
        $response = $client->request('POST', "https://sede.mjusticia.gob.es/eConsultas/inicioNacionalidad", [ 
            'form_params' => [
                "formuNac" => [
                    "codigoNieCompleto" => "R535808 ",
                    "numero" =>  "R535808",
                    "yearSolicitud" => "2022",
                    "fechaNacimiento" => $args['birth'],
                ],
                "gRecaptchaResponse" => "",
                "g-recaptcha-response" => "",
                "action:enviarDatosNacionalidad" => "Enviar"
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            return (object) array(
                'error' => true, 'resultType' => 'danger', 'document' => null,
                'result' => 'Ha habido un problema buscando tus resultados.'
            );
        }
        $search_page_content = $response->getBody()->getContents();
        return (object) array(
            'result' => $search_page_content,
            'resultType' => $statusCode === 200 ? 'success' : 'warning'
        );
        */
        /* ON HOLD */
    }
    public static function ProteccionInternacional($args)
    {
        $client = new Client();
        $NIE = $args['nie'];

        $SEARCHURL = "https://boe.es/notificaciones/notificaciones.php?campo%5B0%5D=DOC&dato%5B0%5D=" . $NIE . "&operador%5B0%5D=and&campo%5B1%5D=DEM&dato%5B1%5D=Ministerio+del+Interior&operador%5B1%5D=and&campo%5B2%5D=MATERIA&dato%5B2%5D=&operador%5B2%5D=and&campo%5B3%5D=NBO&dato%5B3%5D=&operador%5B4%5D=and&campo%5B4%5D=FPU&dato%5B4%5D%5B0%5D=&dato%5B4%5D%5B1%5D=&page_hits=50&sort_field%5B0%5D=FPU&sort_order%5B0%5D=desc&sort_field%5B1%5D=id&sort_order%5B1%5D=asc&accion=Buscar";

        $response = $client->request('GET', $SEARCHURL);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            return (object) array(
                'error' => true, 'resultType' => 'danger', 'document' => null,
                'result' => 'Ha habido un problema buscando tus resultados.'
            );
        }
        $search_page_content = $response->getBody()->getContents();
        
        $error_code = false;
        /**
         * CHECK IF THE RESULT EXISTS
         */
        $searchStrings_notfound = [
            'No se han encontrado documentos que satisfagan sus criterios de busqueda',
            'No se han encontrado documentos que satisfagan sus criterios de búsqueda',
            'No se han encontrado documentos que satisfagan sus criterios de b&uacute;squeda'
        ];
        $searchStrings_searchinput = [
            "Los valores de busqueda enviados son incorrectos",
            "Los valores de búsqueda enviados son incorrectos",
            'Los valores de b&uacute;squeda enviados son incorrectos'
        ];
        $searchStrings = array_merge($searchStrings_notfound, $searchStrings_searchinput);
        foreach ($searchStrings as $searchString) {
            if (strpos($search_page_content, $searchString) !== false) {
                if (in_array($searchString, $searchStrings_notfound ) ) {
                    $error_code = 'NIE_NOT_FOUND';
                } else if (in_array($searchString, $searchStrings_searchinput) ) {
                    $error_code = 'INVALID_SEARCH_SUPPORT';
                }
            }
        }
        if ($error_code == false) {
            if (preg_match('/href="\/boe_n\/(.*?)"/', $search_page_content, $matches)) {
                $PDF = 'https://boe.es/boe_n/' . $matches[1];
                $response = $client->request('GET', $PDF . "&fix_bug_chrome=foo.pdf");
                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    return (object) array(
                        'error' => true, 'resultType' => 'danger', 'document' => null,
                        'result' => 'Ha habido un problema buscando tus resultados.'
                    );
                }
                $PDF_content =  (string) $response->getBody();
                
                $parser = new Parser();
                $pdf = $parser->parseContent($PDF_content);
                $PDF_content = $pdf->getText();
                if (preg_match('/' . preg_quote($NIE) . '.*?(?=[A-Z]\d{7}[A-Z]|$)/', $PDF_content, $matches_final)) {
                    $matchedText = $matches_final[0];
                    $matchedText = preg_replace('/[A-Z]\d{7}[A-Z]\d*/', '', $matchedText);
                    $matchedText = preg_replace('/\./', '', $matchedText);
                    $matchedText = str_replace('E F', 'Expediente Familiar ', $matchedText);
                    if (strpos($matchedText, "ARCHIVO") !== false) {
                        return (object) array( 'result' => $matchedText, 'resultType' => 'warning', 'document' => $PDF );
                    } else if (strpos($matchedText, "CESAC") !== false || strpos($matchedText, "DESFAVORABLE") !== false || strpos($matchedText, "EXCLUSI") !== false) {
                        return (object) array( 'result' => $matchedText, 'resultType' => 'danger', 'document' => $PDF );
                    } else if (strpos($matchedText, "R H ART 37B") !== false) {
                        return (object) array( 'result' => "Razones Humanitarias segun Art 37B", 'resultType' => 'success', 'document' => $PDF );
                    } else if (strpos($matchedText, "PRO SUB") !== false) { 
                        return (object) array( 'result' => "Proteccion Subsidiaria", 'resultType' => 'success', 'document' => $PDF );
                    } else if (strpos($matchedText, "ASILO") !== false) {
                        return (object) array( 'result' => $matchedText, 'resultType' => 'success', 'document' => $PDF );
                    } else {
                        return (object) array(
                            'result' => "Revise manualmente el BOE correspondiente",
                            'resultType' => 'success',
                            'document' => $PDF
                        );
                    }
                } else {
                    return (object) array(
                        'result' => "Revise manualmente el siguiente BOE",
                        'resultType' => 'success',
                        'document' => $PDF
                    );
                }
            } else {
                return (object) array(
                    'resultType' => 'danger', 'document' => null,
                    'result' => 'No se encontró ningún resultado durante los ultimos 3 meses en el BOE para el NIE ingresado.'
                );
            }
        } else if ($error_code === 'NIE_NOT_FOUND') {
            return (object) array(
                'resultType' => 'danger', 'document' => null,
                'result' => 'No se encontró ningún resultado durante los ultimos 3 meses en el BOE para el NIE ingresado.'
            );
        } else if ($error_code === 'INVALID_SEARCH_SUPPORT') {
            return (object) array(
                'error' => true, 'resultType' => 'danger', 'document' => null,
                'result' => 'Los valores de busqueda enviados son incorrectos, por favor contacte a soporte técnico.'
            );
        } else {
            return (object) array(
                'error' => true, 'resultType' => 'danger', 'document' => null,
                'result' => 'Ha ocurrido un error no especifico, por favor contacte a soporte técnico.'
            );
        } 
    }
}