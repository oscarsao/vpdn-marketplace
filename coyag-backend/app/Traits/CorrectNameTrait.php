<?php

namespace App\Traits;


trait CorrectNameTrait
{
    public function provinceCorrectName($provinceN) {
        if (empty($provinceN)) {
            return null;
        }
    
      $provinceName = strtolower(trim($provinceN));
      $corrections = [
        'a coruña' => 'La Coruña',
        'alacant' => 'Alicante',
        'andaucía' => 'Andalucía',
        'araba/álava' => 'Álava',
        'balears' => 'Baleares',
        'balears (illes)' => 'Baleares',
        'illes balears' => 'Baleares',
        'illes' => 'Baleares',
        'bizkaia' => 'Vizcaya',
        'gipuzkoa' => 'Guipúzcoa',
        'girona' => 'Gerona',
        'lleida' => 'Lérida',
        'malaga' => 'Málaga',
        'ourense' => 'Orense',
        'tenerife' => 'Santa Cruz de Tenerife',
        'València' => 'Valencia',
        'valència' => 'Valencia',
      ];
      if (isset($corrections[$provinceName])) {
        return $corrections[$provinceName];
      }
      $finalN = trim(str_replace([' capital', ' Capital'], "", $provinceN));
      return $finalN;
    }

    public function municipalityCorrectName($municipalityN) {
        if(empty($municipalityN)) return null;
        
        $municipalityName = strtolower(trim($municipalityN));
        $corrections = [
            'albacete capital' => 'Albacete',
            'alacant' => 'Alicante',
            'alicante / alacant' => 'Alicante',
            'alacant / alicante' => 'Alicante',
            'alicante/alacant' => 'Alicante',
            'alacant/alicante' => 'Alicante',
            'xabia' => 'Jávea',
            'xàbia' => 'Jávea',
            'xàbia/jávea' => 'Jávea',
            'jávea/xàbia' => 'Jávea',
            'jávea/xabia' => 'Jávea',
            'xabia/jávea' => 'Jávea',
            'la salud - la salle' => 'Salud-La Salle',
            'salud - la salle' => 'Salud-La Salle',
            'badajoz capital' => 'Badajoz',
            'barcelona capital' => 'Barcelona',
            'cádiz capital' => 'Cádiz',
            'cuenca capital' => 'Cuenca',
            'girona' => 'Gerona',
            'gerona capital' => 'Gerona',
            'girona capital' => 'Gerona',
            'girona / gerona' => 'Gerona',
            'gerona / girona' => 'Gerona',
            'girona/gerona' => 'Gerona',
            'gerona/girona' => 'Gerona',
            'sant gregori' => 'San Gregorio',
            'sant gregori (municipio)' => 'San Gregorio',
            'figueres' => 'Figueras',
            'figueres/figueras' => 'Figueras',
            'figueras/figueres' => 'Figueras',
            'figueres / figueras' => 'Figueras',
            'figueras / figueres' => 'Figueras',
            'platja d\'aro' => 'Playa de Aro',
            'platja d\'aro/playa de aro' => 'Playa de Aro',
            'playa de aro/platja d\'aro' => 'Playa de Aro',
            'guadalajara capital' => 'Guadalajara',
            'huesca capital' => 'Huesca',
            'león capital' => 'León',
            'lérida capital' => 'Lérida',
            'lleida' => 'Lérida',
            'lleida capital' => 'Lérida',
            'lleida / lerida' => 'Lérida',
            'lerida / lleida' => 'Lérida',
            'lleida/lerida' => 'Lérida',
            'lerida/lleida' => 'Lérida',
            'alameda' => 'Alameda del Valle',
            'monte tesoro' => 'Algete',
            'cottolengo del padre alegre' => 'Algete',
            'mirador el alamo' => 'El Álamo',
            'virgen del puerto (el alamo)' => 'El Álamo',
            'corredor del henares' => 'Alcalá de Henares',
            'corredor de henares' => 'Alcalá de Henares',
            'urbanizacion cervantes' => 'Alcalá de Henares',
            'montepríncipe' => 'Alcorcón',
            'monteprincipe' => 'Alcorcón',
            'poligono industrial alcobendas' => 'Alcobendas',
            'parque empresarial la moraleja' => 'Alcobendas',
            'soto de la moraleja' => 'Alcobendas',
            'arganda' => 'Arganda del Rey',
            'arroyomolinos (madrid)' => 'Arroyomolinos',
            'los combos (arroyomolinos)' => 'Arroyomolinos',
            'navarrondilla' => 'Colmenarejo',
            'estacion de coslada' => 'Coslada',
            'estación de coslada' => 'Coslada',
            'santa juana cruz (convento casarrubuelos)' => 'Cubas de la Sagra',
            'santa juana de la cruz' => 'Cubas de la Sagra',
            'cerceda' => 'El Boalo',
            'estremera de tajo' => 'Estremera',
            'carretera de madrid (getafe)' => 'Getafe',
            'carretera de leganes (getafe)' => 'Getafe',
            'carretera de toledo (getafe)' => 'Getafe',
            'los nidos' => 'Griñón',
            'humanes' => 'Humanes de Madrid',
            'colonia de el angel' => 'Humanes de Madrid',
            'zarzaquemada' => 'Leganés',
            'la fortuna' => 'Leganés',
            'las rozas' => 'Las Rozas de Madrid',
            'carretera el escorial (las rozas)' => 'Las Rozas de Madrid',
            'colegio orvalle' => 'Las Rozas de Madrid',
            'lozoyuela' => 'Lozoyuela-Navas-Sieteiglesias',
            'madrid capital' => 'Madrid',
            'aeropuerto de barajas' => 'Madrid',
            'colonia san blas' => 'Madrid',
            'colonia de la colina' => 'Madrid',
            'barajas' => 'Madrid',
            'arganzuela' => 'Madrid',
            'moncloa-aravaca' => 'Madrid',
            'fuencarral-el pardo' => 'Madrid',
            'carabanchel' => 'Madrid',
            'aguas de aravaca' => 'Madrid',
            'colonia las cañadas' => 'Madrid',
            'venta feliciano' => 'Madrid',
            'cuatro caminos' => 'Madrid',
            'la chopera' => 'Madrid',
            'colonia la concepcion' => 'Madrid',
            'la piovera' => 'Madrid',
            'parque residencial nuevo club golf' => 'Madrid',
            'la ribera' => 'Madrid',
            'fincas de castillejo' => 'Madrid',
            'casa de postas' => 'Madrid',
            'los cicutares' => 'Madrid',
            'guadalajara' => 'Madrid',
            'matacerquillas' => 'Moralzarzal',
            'carretera de majadahonda' => 'Majadahonda',
            'la moraleja' => 'Moraleja de Enmedio',
            'carretera de toledo (parla)' => 'Parla',
            'rivas vaciamadrid' => 'Rivas-Vaciamadrid',
            'covibar' => 'Rivas-Vaciamadrid',
            'colonia covibar' => 'Rivas-Vaciamadrid',
            'san agustin de guadalix' => 'San Agustín del Guadalix',
            'urbanizacion valdelagua' => 'San Agustín del Guadalix',
            'valdelagua' => 'San Agustín del Guadalix',
            'san lorenzo del escorial' => 'San Lorenzo de El Escorial',
            'parque empresarial san fernando de henares' => 'San Fernando de Henares',
            'ciudalcampo' => 'San Sebastián de los Reyes',
            'tecnaton' => 'San Sebastián de los Reyes',
            'urbanizacion la granjilla' => 'San Sebastián de los Reyes',
            'club de campo' => 'San Sebastián de los Reyes',
            'los cerrillos' => 'Soto del Real',
            'mercado parque corredor del henares' => 'Torrejón de Ardoz',
            'tres canto' => 'Tres Cantos',
            'alalpardo' => 'Valdeolmos-Alalpardo',
            'san antonio' => 'Velilla de San Antonio',
            'los hueros' => 'Villalbilla',
            'villafranca del castillo' => 'Villanueva de la Cañada',
            'alhaurin de la torre' => 'Alhaurín de la Torre',
            'urbanizacion pletero' => 'Alhaurín de la Torre',
            'benahavis' => 'Benahavís',
            'las angosturas' => 'Benahavís',
            'benalmadena' => 'Benalmádena',
            'benalmadena costa' => 'Benalmádena',
            'benalmádena costa' => 'Benalmádena',
            'arroyo de la miel' => 'Benalmádena',
            'cartama' => 'Cártama',
            'malaga' => 'Málaga',
            'málaga capital' => 'Málaga',
            'malaga capital' => 'Málaga',
            'los perez' => 'Málaga',
            'los pérez' => 'Málaga',
            'churriana' => 'Málaga',
            'urbanizacion riviera del sol' => 'Mijas',
            'las lagunas de mijas' => 'Mijas',
            'urbanizacion sitio de calahonda' => 'Mijas',
            'hotel calahonda' => 'Mijas',
            'la cala de mijas' => 'Mijas',
            'velez-malaga' => 'Vélez-Málaga',
            'torre del mar' => 'Vélez-Málaga',
            'benajarafe' => 'Vélez-Málaga',
            'murcia capital' => 'Murcia',
            'iruñea' => 'Pamplona',
            'pamplona/iruñea' => 'Pamplona',
            'iruña' => 'Pamplona',
            'pamplona/iruña' => 'Pamplona',
            'iruña/pamplona' => 'Pamplona',
            'iruñea/pamplona' => 'Pamplona',
            'ourense' => 'Orense',
            'ourense capital' => 'Orense',
            'orense capital' => 'Orense',
            'ourense / orense' => 'Orense',
            'orense / ourense' => 'Orense',
            'ourense/orense' => 'Orense',
            'orense/ourense' => 'Orense',
            'tenerife' => 'Santa Cruz de Tenerife',
            'tenerife capital' => 'Santa Cruz de Tenerife',
            'santa cruz de tenerife capital' => 'Santa Cruz de Tenerife',
            'sevilla capital' => 'Sevilla',
            'toledo capital' => 'Toledo',
            'valencia capital' => 'Valencia',
            'València' => 'Valencia',
            'valència' => 'Valencia',
            'valladolid capital' => 'Valladolid'
        ];
        if(isset($corrections[$municipalityName])) {
          return $corrections[$municipalityName];
        }
        $finalN = trim(str_replace([' capital', ' Capital'], "", $municipalityN));
        return $finalN;
    }

    public function districtCorrectName($districtN) {
        if(empty($districtN)) return null;
        $districtName = strtolower(trim($districtN));
        $corrections = [
            'barrio de salamanca' => 'Salamanca',
            'san blas' => 'San Blas-Canillejas',
            'moncloa - aravaca' => 'Moncloa-Aravaca',
            'fuencarral - el pardo' => 'Fuencarral-El Pardo',
            'fuencarral' => 'Fuencarral-El Pardo',
            'la salud - la salle' => 'Salud-La Salle',
            'salud - la salle' => 'Salud-La Salle'
        ];
        if(isset($corrections[$districtName])) {
            return $corrections[$districtName];
        }
        return $districtN;
        $finalN = trim(str_replace([' capital', ' Capital'], "", $districtN));
        return $finalN;
    }

    public function neighborhoodCorrectName($neighborhoodN){
      if (empty($neighborhoodN)) {
        return null;
      }
      $neighborhoodName = strtolower(trim($neighborhoodN));
      $corrections = [
        'corralejos - campo de las naciones' => 'Corralejos',
        'cortes - huerta' => 'Cortes',
        'embajadores - lavapiés' => 'Embajadores',
        'universidad - malasaña' => 'Universidad',
        'hispanoamérica - bernabéu' => 'Hispanoamérica',
        'ríos rosas - nuevos ministerios' => 'Ríos Rosas',
        'Tres Olivos - Valverde' => 'Valverde',
        'Las Tablas' => 'Valverde',
        'conde orgaz - piovera' => 'Piovera',
        'sanchinarro' => 'Valdefuentes',
        'valdebebas - valdefuentes' => 'Valdefuentes',
        'ibiza de madrid' => 'Ibiza',
        'rosas - musas' => 'Rosas',
        'almenara -ventilla' => 'Almenara',
        'castillejos - cuzco' => 'Castillejos',
        'cuzco-castillejos' => 'Castillejos',
        'castillejos-cuzco' => 'Castillejos',
        'ambroz' => 'Casco Histórico de Vicálvaro',
        'el cañaveral - los berrocales' => 'El Cañaveral',
        'ensanche De vallecas - la gavia' => 'Ensanche De Vallecas',
      ];
      if (isset($corrections[$neighborhoodName])) {
        return $corrections[$neighborhoodName];
      }
      $finalN = trim(str_replace([' capital', ' Capital'], "", $neighborhoodN));
      return $finalN;
    }
}

