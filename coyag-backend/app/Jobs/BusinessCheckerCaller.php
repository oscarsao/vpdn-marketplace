<?php

namespace App\Jobs;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isNull;

class BusinessCheckerCaller implements ShouldQueue { 
    use Dispatchable, Queueable;

    private $apiUrl;
    private $apiKey;
    private $urls;

    /**
     * Create a new job instance.
     *
     * @param string $apiUrl BrightData API endpoint URL
     * @param string $apiKey Your BrightData API token
     * @param array $urls List of URLs to request data for
     * @return void
     */
    public function __construct() {
        $this->apiUrl = "https://api.brightdata.com/dca/trigger?collector=c_li38zq1j27wxnshbb1&queue_next=1";
        $this->apiKey = env('BRIGHT_DATA_KEY');
        $this->urls = [];

        $scripts = [
            [ 'name'=> "CIUDAD - Madrid", 'properties' => [], 'urls'=> [
                "https://www.idealista.com/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/grupo-artecasas/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/nuevadireccion/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/salesman-property-real-estate/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/negociosdehosteleria/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/grupo-portugal/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/grupo-andino/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/grupo-madriz/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/inmobiliaria-grupo-expansion/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/inmorest/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.idealista.com/pro/geyhache/alquiler-locales/madrid-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/madrid-provincia/todas-las-zonas/l",
                "https://belbex.com/locales/madrid-provincia/alquiler/caracteristicas-traspaso/",
                "https://www.milanuncios.com/traspasos-de-estancos-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-tiendas-de-alimentacion-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-peluquerias-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-talleres-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-clinicas-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-panaderias-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-tiendas-de-moda-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-farmacias-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-tiendas-de-informatica-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-gimnasios-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-hostales-y-hoteles-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-academias-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-locutorios-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-guarderias-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-tintorerias-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-cibercafes-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-residencias-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-inmobiliarias-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-bares-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-restaurantes-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/otros-traspasos-en-madrid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                ]
            ],
            [ 'name'=> "CIUDAD - Barcelona", 'properties' => [], 'urls'=> [
                "https://www.idealista.com/alquiler-locales/barcelona-barcelona/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/barcelona-provincia/todas-las-zonas/l",
                ]
            ],
            [ 'name'=> "CIUDAD - Barcelona - Milanuncios", 'properties' => [], 'urls'=> [
                "https://www.milanuncios.com/traspasos-de-estancos-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-tiendas-de-alimentacion-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-peluquerias-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-talleres-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-clinicas-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-panaderias-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-tiendas-de-moda-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-farmacias-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-tiendas-de-informatica-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-gimnasios-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-hostales-y-hoteles-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-academias-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-locutorios-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-guarderias-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-tintorerias-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-cibercafes-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-residencias-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-inmobiliarias-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-bares-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/traspasos-de-restaurantes-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                "https://www.milanuncios.com/otros-traspasos-en-barcelona-barcelona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                ]
            ],
            [ 'name'=> "CIUDAD - Valencia",    'properties' => [], 'urls'=> [
                  
                # Alicante
                "https://www.idealista.com/alquiler-locales/alicante/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/alicante-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-alicante/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",       
        
                # Castellón de la Plana
                "https://www.idealista.com/alquiler-locales/castellon/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/castellon-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-castellon-de-la-plana-castellon/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",       
        
                # Valencia
                "https://www.idealista.com/alquiler-locales/valencia-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/valencia-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-valencia-valencia/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",        
                
                ]
            ],
            [ 'name'=> "LEVANTE - Aragon y Cataluña",            'properties' => [], 'urls'=> [
                "https://www.idealista.com/alquiler-locales/girona-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/girona-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-girona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/lleida-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/lleida-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-lleida/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                
                "https://www.idealista.com/alquiler-locales/tarragona-tarragona/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/tarragona-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-tarragona/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/huesca-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/huesca-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-huesca/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",       
        
                "https://www.idealista.com/alquiler-locales/teruel-provincia/con-traspaso/",
                # "https://www.fotocasa.es/es/traspaso/locales/teruel-provincia/todas-las-zonas/l", no hay
                "https://www.milanuncios.com/traspasos-en-teruel/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",       
                
                "https://www.idealista.com/alquiler-locales/zaragoza-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/zaragoza-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-zaragoza/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                ]
            ],
            [ 'name'=> "INSULAR - Islas canarias y Baleares",    'properties' => [], 'urls'=> [
                "https://www.idealista.com/alquiler-locales/santa-cruz-de-tenerife-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/santa-cruz-de-tenerife-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-santa-cruz-de-tenerife-tenerife/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",       
                
                "https://www.idealista.com/alquiler-locales/las-palmas/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/las-palmas-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-las-palmas-de-gran-canaria-las_palmas/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",       
        
                "https://www.idealista.com/alquiler-locales/balears-illes/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/illes-balears-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-palma-de-mallorca-baleares/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                ]
            ],
            [ 'name'=> "SUR - Andalucia, Extremadura y Murcia",  'properties' => [], 'urls'=> [
                
                "https://www.idealista.com/alquiler-locales/almeria-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/almeria-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-almeria/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/cadiz-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/cadiz-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-cadiz/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/cordoba-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/cordoba-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-cordoba/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/granada-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/granada-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-granada/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/huelva-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/huelva-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-huelva/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/jaen-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/jaen-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-jaen/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                
                "https://www.idealista.com/alquiler-locales/sevilla-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/sevilla-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-sevilla-sevilla/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",       
        
                "https://www.idealista.com/alquiler-locales/malaga-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/malaga-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-malaga/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing", 
        
                #
        
                "https://www.idealista.com/alquiler-locales/badajoz-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/badajoz-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-badajoz/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/caceres-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/caceres-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-caceres/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                #
        
                "https://www.idealista.com/alquiler-locales/murcia-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/murcia-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-murcia-murcia/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                ]
            ],
            [ 'name'=> "RESTO - Asturias, Cantabria, La Rioja, Pais Vasco, Navarra, Castilla Leon y la Mancha", 'properties' => [], 'urls'=> [
                "https://www.idealista.com/alquiler-locales/asturias/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/asturias-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-asturias/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                #
        
                "https://www.idealista.com/alquiler-locales/cantabria/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/cantabria-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-santander-cantabria/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                #
        
                "https://www.idealista.com/alquiler-locales/la-rioja/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/la-rioja-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-logrono-la_rioja/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                #
        
                "https://www.idealista.com/alquiler-locales/vizcaya/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/bizkaia-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-bilbao-vizcaya/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/alava/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/araba-alava-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-vitoria%7Cgasteiz-alava/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/guipuzcoa/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/gipuzkoa-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-san-sebastian%7Cdonostia-guipuzcoa/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                #
        
                "https://www.idealista.com/alquiler-locales/navarra/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/navarra-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-pamplona-navarra/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                # 
        
                "https://www.idealista.com/alquiler-locales/a-coruna-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/a-coruna-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-a-coruna-la_coruna/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.milanuncios.com/traspasos-en-santiago-de-compostela-la_coruna/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/pontevedra-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/pontevedra-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-vigo-pontevedra/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/lugo-lugo/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/lugo-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-lugo/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/ourense-ourense/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/ourense-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-ourense/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                #
        
                "https://www.idealista.com/alquiler-locales/cuenca-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/cuenca-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-cuenca/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/guadalajara-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/guadalajara-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-guadalajara/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/avila-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/avila-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-avila/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/burgos-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/burgos-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-burgos/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/leon-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/leon-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-leon/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/salamanca-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/salamanca-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-salamanca/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/segovia-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/segovia-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-segovia/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/soria-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/soria-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-soria/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/zamora-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/zamora-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-zamora/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                  
                "https://www.idealista.com/alquiler-locales/albacete-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/albacete-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-albacete/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                
                "https://www.idealista.com/alquiler-locales/toledo-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/toledo-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-toledo/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/ciudad-real-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/ciudad-real-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-ciudad-real-ciudad_real/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
        
                "https://www.idealista.com/alquiler-locales/valladolid-provincia/con-traspaso/",
                "https://www.fotocasa.es/es/traspaso/locales/valladolid-provincia/todas-las-zonas/l",
                "https://www.milanuncios.com/traspasos-en-valladolid/?desde=100&demanda=n&orden=relevance&fromSearch=1&hitOrigin=listing",
                ]
            ],
        ];

        foreach ($scripts as $script) {
            $this->urls = array_merge($this->urls, $script['urls']);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        Log::info('=================================');
        Log::info('- Search Business Job ' . Carbon::now()->format('d-m-Y H:i:s'));
        $response = Http::withToken($this->apiKey, 'Bearer')->post(
            $this->apiUrl . "?queue=CYA-Import-" . date("Ymd"),
            [ 'url' => $this->urls, ],
            [ 'headers' => [ 'Content-Type' => 'application/json' ] ]
        );
        if ($response->ok()) {
            Log::info('Brightdata Caller - EXITO');
            Log::info($response);
        } else {
            Log::info('Brightdata Caller - FALLIDO');
            Log::info($response);
            if (is_null($this->apiKey)) Log::info('No API KEY ');
        }
        Log::info('=================================');
    }
}