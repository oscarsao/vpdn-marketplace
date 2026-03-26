<?php
/**
 * ADD A WAY TO BLOCK THE OUTLIERS IN SQM PRICE
 */
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\DB;

/**
 * ADD ACCORDING LOCATION THE MINIMUM SHARED LOCATION
 */

class StatisticsPropertiesController extends Controller {
    public function index (Request $request) {

        $property_type = $request->has('type') ? $request->input('type') : 1;
        
        if ( $request->input('neighborhood') ) {
            $location = (Object) array(
                'type' => 'neighborhood_id',
                'id' => $request->input('neighborhood')
            );
        } else if ( $request->input('district') ) {
            $location = (Object) array(
                'type' => 'district_id',
                'id' => $request->input('district')
            );
        } else if ( $request->input('municipality') ) {
            $location = (Object) array(
                'type' => 'municipality_id',
                'id' => $request->input('municipality')
            );
        } else if ( $request->input('province') ) {
            $location = (Object) array(
                'type' => 'province_id',
                'id' => $request->input('province')
            );
        } else {
            $location = false;
        }
        
        $sector = $property_type == 1 ? ($request->has('sector') ? $request->input('sector') : false) : false;

        $price_mode = $request->has('mode') ? $request->input('mode') : 'investment';

        /* * */

            $query_sectors_segment = "CASE
                WHEN sectors_segment_ids = '' THEN '30'
                WHEN sectors_segment_ids IS NULL THEN '30'
                WHEN FIND_IN_SET( '1', sectors_segment_ids) THEN  '1'
                WHEN FIND_IN_SET( '2', sectors_segment_ids) THEN  '2'
                WHEN FIND_IN_SET( '3', sectors_segment_ids) THEN  '3'
                WHEN FIND_IN_SET( '4', sectors_segment_ids) THEN  '4'
                WHEN FIND_IN_SET( '5', sectors_segment_ids) THEN  '5'
                WHEN FIND_IN_SET( '6', sectors_segment_ids) THEN  '6'
                WHEN FIND_IN_SET( '7', sectors_segment_ids) THEN  '7'
                WHEN FIND_IN_SET( '8', sectors_segment_ids) THEN  '8'
                WHEN FIND_IN_SET( '9', sectors_segment_ids) THEN  '9'
                WHEN FIND_IN_SET('10', sectors_segment_ids) THEN '10'
                WHEN FIND_IN_SET('11', sectors_segment_ids) THEN '11'
                WHEN FIND_IN_SET('12', sectors_segment_ids) THEN '12'
                WHEN FIND_IN_SET('13', sectors_segment_ids) THEN '13'
                WHEN FIND_IN_SET('14', sectors_segment_ids) THEN '14'
                WHEN FIND_IN_SET('15', sectors_segment_ids) THEN '15'
                WHEN FIND_IN_SET('16', sectors_segment_ids) THEN '16'
                WHEN FIND_IN_SET('17', sectors_segment_ids) THEN '17'
                WHEN FIND_IN_SET('18', sectors_segment_ids) THEN '18'
                WHEN FIND_IN_SET('19', sectors_segment_ids) THEN '19'
                WHEN FIND_IN_SET('20', sectors_segment_ids) THEN '20'
                WHEN FIND_IN_SET('21', sectors_segment_ids) THEN '21'
                WHEN FIND_IN_SET('22', sectors_segment_ids) THEN '22'
                WHEN FIND_IN_SET('23', sectors_segment_ids) THEN '23'
                WHEN FIND_IN_SET('24', sectors_segment_ids) THEN '24'
                WHEN FIND_IN_SET('25', sectors_segment_ids) THEN '25'
                WHEN FIND_IN_SET('26', sectors_segment_ids) THEN '26'
                WHEN FIND_IN_SET('27', sectors_segment_ids) THEN '27'
                WHEN FIND_IN_SET('28', sectors_segment_ids) THEN '28'
                WHEN FIND_IN_SET('29', sectors_segment_ids) THEN '29'
                WHEN FIND_IN_SET('30', sectors_segment_ids) THEN '30'
                WHEN FIND_IN_SET('31', sectors_segment_ids) THEN '31'
                WHEN FIND_IN_SET('32', sectors_segment_ids) THEN '32'
                WHEN FIND_IN_SET('33', sectors_segment_ids) THEN '33'
                WHEN FIND_IN_SET('34', sectors_segment_ids) THEN '34'
                WHEN FIND_IN_SET('35', sectors_segment_ids) THEN '35'
                WHEN FIND_IN_SET('36', sectors_segment_ids) THEN '36'
                WHEN FIND_IN_SET('37', sectors_segment_ids) THEN '37'
                ELSE '30'
            END as sectors_segment";

            $query_rooms_segment = 'CASE
                WHEN rooms IS NULL THEN "0"
                WHEN rooms = 0 THEN "0"
                WHEN rooms = 1 THEN "1"
                WHEN rooms = 2 THEN "2"
                WHEN rooms = 3 THEN "3"
                WHEN rooms = 4 THEN "4"
                WHEN rooms = 5 THEN "5"
                WHEN rooms = 6 THEN "6"
                WHEN rooms = 7 THEN "7"
                WHEN rooms = 8 THEN "8"
                WHEN rooms = 9 THEN "9"
                WHEN rooms >= 10 THEN "+10"
            END as rooms_segment';

            $query_investment_segment = 'CASE
                WHEN investment IS NULL                   THEN "0-50k €"
                WHEN investment BETWEEN 0 AND 50000       THEN "0-50k €"
                WHEN investment BETWEEN 50001  AND 100000 THEN "50k-100k €"
                WHEN investment BETWEEN 100001 AND 135000 THEN "100k-135k €"
                WHEN investment BETWEEN 135001 AND 165000 THEN "135k-165k €"
                WHEN investment BETWEEN 165001 AND 200000 THEN "165k-200k €"
                WHEN investment BETWEEN 200001 AND 250000 THEN "200k-250k €"
                WHEN investment BETWEEN 250001 AND 300000 THEN "250k-300k €"
                WHEN investment BETWEEN 300001 AND 350000 THEN "300k-350k €"
                WHEN investment BETWEEN 350001 AND 400000 THEN "350k-400k €"
                WHEN investment BETWEEN 400001 AND 500000 THEN "400k-500k €"
                ELSE "500k+"
            END as price_segment';

            $query_rental_segment = 'CASE
                WHEN rental IS NULL                 THEN "0-500 €"
                WHEN rental BETWEEN 0 AND 500       THEN "0-500 €"
                WHEN rental BETWEEN 501  AND 1000 THEN "500-1000 €"
                WHEN rental BETWEEN 1001 AND 1350 THEN "1000-1350 €"
                WHEN rental BETWEEN 1351 AND 1650 THEN "1350-1650 €"
                WHEN rental BETWEEN 1651 AND 2000 THEN "1650-2000 €"
                WHEN rental BETWEEN 2001 AND 2500 THEN "2000-2500 €"
                WHEN rental BETWEEN 2501 AND 3000 THEN "2500-3000 €"
                WHEN rental BETWEEN 3001 AND 3500 THEN "3000-3500 €"
                WHEN rental BETWEEN 3501 AND 4000 THEN "3500-4000 €"
                WHEN rental BETWEEN 4001 AND 5000 THEN "4000-5000 €"
                ELSE "5.000+ €"
            END as price_segment';

            $orderby_rental_segment = 'CASE
                WHEN price_segment = "0-500 €" THEN 1
                WHEN price_segment = "500-1000 €" THEN 2
                WHEN price_segment = "1000-1350 €" THEN 3
                WHEN price_segment = "1350-1650 €" THEN 4
                WHEN price_segment = "1650-2000 €" THEN 5
                WHEN price_segment = "2000-2500 €" THEN 6
                WHEN price_segment = "2500-3000 €" THEN 7
                WHEN price_segment = "3000-3500 €" THEN 8
                WHEN price_segment = "3500-4000 €" THEN 9
                WHEN price_segment = "4000-5000 €" THEN 10
                ELSE 11
            END';

            // by rental or by investment or both because fuck you
            $query_rental_sqm = 'CEIL(rental / size) as rental_sqm';
            $query_investment_sqm = 'CEIL(investment / size) as investment_sqm';

            $orderby_investment_segment = 'CASE
                WHEN price_segment = "0-50k €" THEN 1
                WHEN price_segment = "50k-100k €" THEN 2
                WHEN price_segment = "100k-135k €" THEN 3
                WHEN price_segment = "135k-165k €" THEN 4
                WHEN price_segment = "165k-200k €" THEN 5
                WHEN price_segment = "200k-250k €" THEN 6
                WHEN price_segment = "250k-300k €" THEN 7
                WHEN price_segment = "300k-350k €" THEN 8
                WHEN price_segment = "350k-400k €" THEN 9
                WHEN price_segment = "400k-500k €" THEN 10
                ELSE 11
            END';

        /* * */

        /* * */

        $group_by_sectors = Business::select(
            DB::raw($query_sectors_segment),
            DB::raw('COUNT(*) as total'),
            DB::raw('CEIL(AVG(rental)) as avg_rental'),
            DB::raw('CEIL(AVG(rental / size)) as avg_rental_sqm'),
            DB::raw('CEIL(AVG(investment)) as avg_investment'),
            DB::raw('CEIL(AVG(investment / size)) as avg_investment_sqm'),
            )
            ->whereNotNull($price_mode)
            ->whereNotNull('size')
            ->where('business_type_id', $property_type)
            ->where('investment', '<', 1000000)
            ->where('investment', '>', 5000)
            ->where('size', '<', 500)
            ->where('size', '>', 10)
            ->whereNotIn('subquery.sectors_segment_ids', [30])
            ->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sectors_segment_ids'))
                    ->from('business_sector')->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id')
            ->groupBy('sectors_segment')
            ->orderBy(DB::raw('CAST(sectors_segment AS SIGNED)'));

        $group_by_rooms = Business::select(
            DB::raw($query_rooms_segment),
            DB::raw('COUNT(*) as total'),
            DB::raw('CEIL(AVG(rental)) as avg_rental'),
            DB::raw('CEIL(AVG(rental / size)) as avg_rental_sqm'),
            DB::raw('CEIL(AVG(investment)) as avg_investment'),
            DB::raw('CEIL(AVG(investment / size)) as avg_investment_sqm'),
            )
            ->whereNotNull($price_mode)
            ->whereNotNull('size')
            ->where('business_type_id', $property_type)
            ->where('investment', '<', 1000000)
            ->where('investment', '>', 5000)
            ->where('size', '<', 500)
            ->where('size', '>', 10)
            ->groupBy('rooms_segment')
            ->orderBy(DB::raw('CAST(rooms_segment AS SIGNED)'));
        
        $group_by_rentals = Business::select(
            DB::raw($query_rental_segment),
            DB::raw('COUNT(*) as total'),
            DB::raw('CEIL(AVG(rental)) as avg_price'),
            DB::raw('CEIL(AVG(rental / size)) as avg_price_sqm'),
            DB::raw('CEIL(AVG(TIMESTAMPDIFF(MONTH, source_timestamp, NOW()))) as months_on_market'),
            )
            ->whereNotNull($price_mode)
            ->whereNotNull('size')
            ->where('business_type_id', $property_type)
            ->where('investment', '<', 1000000)
            ->where('investment', '>', 5000)
            ->where('size', '<', 500)
            ->where('size', '>', 10)
            ->groupBy('price_segment')
            ->orderByRaw($orderby_rental_segment);
        
        $group_by_investments = Business::select(
            DB::raw($query_investment_segment),
            DB::raw('COUNT(*) as total'),
            DB::raw('CEIL(AVG(investment)) as avg_price'),
            DB::raw('CEIL(AVG(investment / size)) as avg_price_sqm'),
            DB::raw('CEIL(AVG(TIMESTAMPDIFF(MONTH, source_timestamp, NOW()))) as months_on_market'),
            )
            ->whereNotNull($price_mode)
            ->whereNotNull('size')
            ->where('business_type_id', $property_type)
            ->where('investment', '<', 1000000)
            ->where('investment', '>', 5000)
            ->where('size', '<', 500)
            ->where('size', '>', 10)
            ->groupBy('price_segment')
            ->orderByRaw($orderby_investment_segment);
        
        $businesses = Business::select(
            'id',
            'id_code as idc',
            'name',
            'size',
            'rental',
            'investment',
            DB::raw($query_rental_sqm),
            DB::raw($query_investment_sqm),
            'sectors'
            )
            ->where('flag_active', true)
            ->whereNotNull($price_mode)
            ->whereNotNull('size')
            ->where('business_type_id', $property_type)
            ->where('investment', '<', 1000000)
            ->where('investment', '>', 5000)
            ->where('size', '<', 500)
            ->where('size', '>', 10)
            ->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sectors'))
                    ->from('business_sector')->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id')
            ->orderBy('investment');
        
        if ( $property_type == 1 && $sector !== false) {
            $businesses           = $businesses          ->whereIn('sectors', [$sector]);
            $group_by_sectors     = $group_by_sectors    ->whereIn('sectors_segment_ids', [$sector]);

            $group_by_rentals->whereIn('subquery.sectors_segment_ids', [$sector])->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sectors_segment_ids'))
                    ->from('business_sector')->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id');

            $group_by_investments->whereIn('subquery.sectors_segment_ids', [$sector])->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sectors_segment_ids'))
                    ->from('business_sector')->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id');
        }
        if ( $location != false) {
            if ($location->type === 'province_id') {
                $businesses          ->whereHas('municipality', function ($query) use ($location) {
                    $query->where('province_id', $location->id);
                });
                $group_by_rooms      ->whereHas('municipality', function ($query) use ($location) {
                    $query->where('province_id', $location->id);
                });
                $group_by_sectors    ->whereHas('municipality', function ($query) use ($location) {
                    $query->where('province_id', $location->id);
                });
                $group_by_rentals    ->whereHas('municipality', function ($query) use ($location) {
                    $query->where('province_id', $location->id);
                });
                $group_by_investments->whereHas('municipality', function ($query) use ($location) {
                    $query->where('province_id', $location->id);
                });
            } else {
                $businesses           = $businesses          ->where($location->type, $location->id);
                $group_by_rooms       = $group_by_rooms      ->where($location->type, $location->id);
                $group_by_sectors     = $group_by_sectors    ->where($location->type, $location->id);
                $group_by_rentals     = $group_by_rentals    ->where($location->type, $location->id);
                $group_by_investments = $group_by_investments->where($location->type, $location->id);
            }
        }
        /* * */

        $businesses = $businesses->get();
        $businesses_count = count($businesses);
        $businesses_array = $businesses->toArray();
        $targetSize = 250;
        if ($businesses_count > $targetSize) {
            $downsampledArray = [];
            $step = $businesses_count / $targetSize;
            for ($i = 0; $i < $targetSize; $i++) {
                $index = (int)($i * $step);
                $downsampledArray[] = $businesses[$index];
            }
            $businesses_array = $downsampledArray;
        }
    
        $group_by_rooms       = $property_type == 3 ? $group_by_rooms->get()   : false;
        $group_by_sectors     = $property_type == 1 ? $group_by_sectors->get() : false;
        $group_by_rentals     = $group_by_rentals->get();
        $group_by_investments = $group_by_investments->get();
        
        $statistics = array(
            'location'         =>  $location,
            'group_by_rooms'   =>  $group_by_rooms,
            'group_by_sectors' =>  $group_by_sectors,
            'group_by_rentals' =>  $group_by_rentals,
            'group_by_investments' => $group_by_investments,
            'businesses'       =>  array(
                'counts' => array(
                    'total' => $businesses_count,
                    'rental' => array(
                        'min' => $businesses->min('rental_sqm'),
                        'max' => $businesses->max('rental_sqm'),
                        'avg' => round($businesses->avg('rental_sqm')),
                        'median' => $businesses->median('rental_sqm'),
                        
                        '_min' => $businesses->min('rental'),
                        '_max' => $businesses->max('rental'),
                        '_avg' => round($businesses->avg('rental')),
                        '_median' => $businesses->median('rental'),

                        'lower_quartile' => $businesses->where('rental_sqm', '<', $businesses->median('rental_sqm'))->median('rental_sqm'),
                        'upper_quartile' => $businesses->where('rental_sqm', '>', $businesses->median('rental_sqm'))->median('rental_sqm'),
                    ),
                    'investment' => array(
                        'min' => $businesses->min('investment_sqm'),
                        'max' => $businesses->max('investment_sqm'),
                        'avg' => round($businesses->avg('investment_sqm')),
                        'median' => $businesses->median('investment_sqm'),

                        '_min' => $businesses->min('investment'),
                        '_max' => $businesses->max('investment'),
                        '_avg' => round($businesses->avg('investment')),
                        '_median' => $businesses->median('investment'),

                        'lower_quartile' => $businesses->where('investment_sqm', '<', $businesses->median('investment_sqm'))->median('investment_sqm'),
                        'upper_quartile' => $businesses->where('investment_sqm', '>', $businesses->median('investment_sqm'))->median('investment_sqm'),
                    ),
                ),
                'data' => $businesses_array
            )
        );
        return response()->json([
            'status' =>  'success',
            'mode'   =>  $price_mode,
            'type'   =>  $property_type,
            ...$statistics,
        ], 200);
    }

    public function get($business_id) {
        
        /* * */
            $query_sectors_segment = "CASE
                WHEN sectors_segment_ids = '' THEN '30'
                WHEN sectors_segment_ids IS NULL THEN '30'
                WHEN FIND_IN_SET( '1', sectors_segment_ids) THEN  '1'
                WHEN FIND_IN_SET( '2', sectors_segment_ids) THEN  '2'
                WHEN FIND_IN_SET( '3', sectors_segment_ids) THEN  '3'
                WHEN FIND_IN_SET( '4', sectors_segment_ids) THEN  '4'
                WHEN FIND_IN_SET( '5', sectors_segment_ids) THEN  '5'
                WHEN FIND_IN_SET( '6', sectors_segment_ids) THEN  '6'
                WHEN FIND_IN_SET( '7', sectors_segment_ids) THEN  '7'
                WHEN FIND_IN_SET( '8', sectors_segment_ids) THEN  '8'
                WHEN FIND_IN_SET( '9', sectors_segment_ids) THEN  '9'
                WHEN FIND_IN_SET('10', sectors_segment_ids) THEN '10'
                WHEN FIND_IN_SET('11', sectors_segment_ids) THEN '11'
                WHEN FIND_IN_SET('12', sectors_segment_ids) THEN '12'
                WHEN FIND_IN_SET('13', sectors_segment_ids) THEN '13'
                WHEN FIND_IN_SET('14', sectors_segment_ids) THEN '14'
                WHEN FIND_IN_SET('15', sectors_segment_ids) THEN '15'
                WHEN FIND_IN_SET('16', sectors_segment_ids) THEN '16'
                WHEN FIND_IN_SET('17', sectors_segment_ids) THEN '17'
                WHEN FIND_IN_SET('18', sectors_segment_ids) THEN '18'
                WHEN FIND_IN_SET('19', sectors_segment_ids) THEN '19'
                WHEN FIND_IN_SET('20', sectors_segment_ids) THEN '20'
                WHEN FIND_IN_SET('21', sectors_segment_ids) THEN '21'
                WHEN FIND_IN_SET('22', sectors_segment_ids) THEN '22'
                WHEN FIND_IN_SET('23', sectors_segment_ids) THEN '23'
                WHEN FIND_IN_SET('24', sectors_segment_ids) THEN '24'
                WHEN FIND_IN_SET('25', sectors_segment_ids) THEN '25'
                WHEN FIND_IN_SET('26', sectors_segment_ids) THEN '26'
                WHEN FIND_IN_SET('27', sectors_segment_ids) THEN '27'
                WHEN FIND_IN_SET('28', sectors_segment_ids) THEN '28'
                WHEN FIND_IN_SET('29', sectors_segment_ids) THEN '29'
                WHEN FIND_IN_SET('30', sectors_segment_ids) THEN '30'
                WHEN FIND_IN_SET('31', sectors_segment_ids) THEN '31'
                WHEN FIND_IN_SET('32', sectors_segment_ids) THEN '32'
                WHEN FIND_IN_SET('33', sectors_segment_ids) THEN '33'
                WHEN FIND_IN_SET('34', sectors_segment_ids) THEN '34'
                WHEN FIND_IN_SET('35', sectors_segment_ids) THEN '35'
                WHEN FIND_IN_SET('36', sectors_segment_ids) THEN '36'
                WHEN FIND_IN_SET('37', sectors_segment_ids) THEN '37'
                ELSE '30'
            END as sectors_segment";

            $query_rooms_segment = 'CASE
                WHEN rooms IS NULL THEN "0"
                WHEN rooms = 0 THEN "0"
                WHEN rooms = 1 THEN "1"
                WHEN rooms = 2 THEN "2"
                WHEN rooms = 3 THEN "3"
                WHEN rooms = 4 THEN "4"
                WHEN rooms = 5 THEN "5"
                WHEN rooms = 6 THEN "6"
                WHEN rooms = 7 THEN "7"
                WHEN rooms = 8 THEN "8"
                WHEN rooms = 9 THEN "9"
                WHEN rooms >= 10 THEN "+10"
            END as rooms_segment';

            $query_investment_segment = 'CASE
                WHEN investment IS NULL                   THEN "0-50k €"
                WHEN investment BETWEEN 0 AND 50000       THEN "0-50k €"
                WHEN investment BETWEEN 50001  AND 100000 THEN "50k-100k €"
                WHEN investment BETWEEN 100001 AND 135000 THEN "100k-135k €"
                WHEN investment BETWEEN 135001 AND 165000 THEN "135k-165k €"
                WHEN investment BETWEEN 165001 AND 200000 THEN "165k-200k €"
                WHEN investment BETWEEN 200001 AND 250000 THEN "200k-250k €"
                WHEN investment BETWEEN 250001 AND 300000 THEN "250k-300k €"
                WHEN investment BETWEEN 300001 AND 350000 THEN "300k-350k €"
                WHEN investment BETWEEN 350001 AND 400000 THEN "350k-400k €"
                WHEN investment BETWEEN 400001 AND 500000 THEN "400k-500k €"
                ELSE "500k+"
            END as price_segment';

            $query_rental_segment = 'CASE
                WHEN rental IS NULL                 THEN "0-500 €"
                WHEN rental BETWEEN 0 AND 500       THEN "0-500 €"
                WHEN rental BETWEEN 501  AND 1000 THEN "500-1000 €"
                WHEN rental BETWEEN 1001 AND 1350 THEN "1000-1350 €"
                WHEN rental BETWEEN 1351 AND 1650 THEN "1350-1650 €"
                WHEN rental BETWEEN 1651 AND 2000 THEN "1650-2000 €"
                WHEN rental BETWEEN 2001 AND 2500 THEN "2000-2500 €"
                WHEN rental BETWEEN 2501 AND 3000 THEN "2500-3000 €"
                WHEN rental BETWEEN 3001 AND 3500 THEN "3000-3500 €"
                WHEN rental BETWEEN 3501 AND 4000 THEN "3500-4000 €"
                WHEN rental BETWEEN 4001 AND 5000 THEN "4000-5000 €"
                ELSE "5.000+ €"
            END as price_segment';

            $orderby_rental_segment = 'CASE
                WHEN price_segment = "0-500 €" THEN 1
                WHEN price_segment = "500-1000 €" THEN 2
                WHEN price_segment = "1000-1350 €" THEN 3
                WHEN price_segment = "1350-1650 €" THEN 4
                WHEN price_segment = "1650-2000 €" THEN 5
                WHEN price_segment = "2000-2500 €" THEN 6
                WHEN price_segment = "2500-3000 €" THEN 7
                WHEN price_segment = "3000-3500 €" THEN 8
                WHEN price_segment = "3500-4000 €" THEN 9
                WHEN price_segment = "4000-5000 €" THEN 10
                ELSE 11
            END';

            // by rental or by investment or both because fuck you
            $query_rental_sqm = 'CEIL(rental / size) as rental_sqm';
            $query_investment_sqm = 'CEIL(investment / size) as investment_sqm';

            $orderby_investment_segment = 'CASE
                WHEN price_segment = "0-50k €" THEN 1
                WHEN price_segment = "50k-100k €" THEN 2
                WHEN price_segment = "100k-135k €" THEN 3
                WHEN price_segment = "135k-165k €" THEN 4
                WHEN price_segment = "165k-200k €" THEN 5
                WHEN price_segment = "200k-250k €" THEN 6
                WHEN price_segment = "250k-300k €" THEN 7
                WHEN price_segment = "300k-350k €" THEN 8
                WHEN price_segment = "350k-400k €" THEN 9
                WHEN price_segment = "400k-500k €" THEN 10
                ELSE 11
            END';

        /* * */

        if ( empty($business_id) ) return response()->json([ 'error' => 'Invalid request'], 400);

        $business = Business::select(
            'id',
            'investment',
            'rental',
            'rooms',
            'size',
            DB::raw('CEIL(TIMESTAMPDIFF(MONTH, source_timestamp, NOW())) as months_on_market'),
            DB::raw($query_rental_sqm),
            DB::raw($query_investment_sqm),
            DB::raw($query_rooms_segment),
            DB::raw($query_rental_segment . '_rental'),
            DB::raw($query_investment_segment . '_investment'),
            'municipality_id',
            'district_id',
            'neighborhood_id',
            'business_type_id',
            'sectors_segment'
            )
            ->where('id', $business_id)
            ->with(array('neighborhood' => function ($query) {
                $query->select('neighborhoods.id', 'neighborhoods.name');
            }))->with(array('district' => function ($query) {
                $query->select('districts.id', 'districts.name');
            }))->with(array('municipality' => function ($query) {
                $query->select('municipalities.id', 'municipalities.name', 'municipalities.province_id')
                    ->with(array('province' => function ($query) {
                        $query->select('provinces.id', 'provinces.name');
                    }));
            }))->leftJoinSub(function ($query) {
                $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sectors_segment'))
                    ->from('business_sector')
                    ->groupBy('business_id');
            }, 'subquery', 'subquery.business_id', '=', 'businesses.id')
        ->first();
        
        $property_type = $business->business_type_id;
        if ($business->investment === null && $business->rental !== null) {
            $price_mode = 'rental';
        } else {
            $price_mode = 'investment';
        }

        $geographic_locations = [];

        if (!empty($business->municipality_id) && !empty($business->municipality)) {
            $geographic_locations[] = (Object) array('name' => $business->municipality->name, 'type' => 'municipality_id', 'id' => $business->municipality_id);
        }
        if (!empty($business->district_id) && !empty($business->district)) {
            $geographic_locations[] = (Object) array('name' => $business->district->name, 'type' => 'district_id', 'id' => $business->district_id);
        }
        if (!empty($business->neighborhood_id) && !empty($business->neighborhood)) {
            $geographic_locations[] = (Object) array('name' => $business->neighborhood->name, 'type' => 'neighborhood_id', 'id' => $business->neighborhood_id);
        }  

        /**
         * ****************************************************************************************************
         */

        $statsByLocation = [];

        foreach ($geographic_locations as $location) {
            
            $group_by_sectors = Business::select(
                DB::raw($query_sectors_segment),
                DB::raw('COUNT(*) as total'),
                DB::raw('CEIL(AVG(rental)) as avg_rental'),
                DB::raw('CEIL(AVG(rental / size)) as avg_rental_sqm'),
                DB::raw('CEIL(AVG(investment)) as avg_investment'),
                DB::raw('CEIL(AVG(investment / size)) as avg_investment_sqm'),
                )
                ->whereNotNull($price_mode)
                ->whereNotNull('size')
                ->where('business_type_id', $property_type)
                ->where($location->type, $location->id)
                ->where('investment', '<', 1000000)
                ->where('investment', '>', 5000)
                ->where('size', '<', 500)
                ->where('size', '>', 10)
                ->whereNotIn('subquery.sectors_segment_ids', [30])
                ->leftJoinSub(function ($query) {
                    $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sectors_segment_ids'))
                        ->from('business_sector')->groupBy('business_id');
                }, 'subquery', 'subquery.business_id', '=', 'businesses.id')
                ->groupBy('sectors_segment')
                ->orderBy(DB::raw('CAST(sectors_segment AS SIGNED)'));
                
            $group_by_rooms = Business::select(
                DB::raw($query_rooms_segment),
                DB::raw('COUNT(*) as total'),
                DB::raw('CEIL(AVG(rental)) as avg_rental'),
                DB::raw('CEIL(AVG(rental / size)) as avg_rental_sqm'),
                DB::raw('CEIL(AVG(investment)) as avg_investment'),
                DB::raw('CEIL(AVG(investment / size)) as avg_investment_sqm'),
                )
                ->whereNotNull($price_mode)
                ->whereNotNull('size')
                ->where('business_type_id', $property_type)
                ->where($location->type, $location->id)
                ->where('investment', '<', 1000000)
                ->where('investment', '>', 5000)
                ->where('size', '<', 500)
                ->where('size', '>', 10)
                ->groupBy('rooms_segment')
                ->orderBy(DB::raw('CAST(rooms_segment AS SIGNED)'));
            
            $group_by_rentals = Business::select(
                DB::raw($query_rental_segment),
                DB::raw('COUNT(*) as total'),
                DB::raw('CEIL(AVG(rental)) as avg_price'),
                DB::raw('CEIL(AVG(rental / size)) as avg_price_sqm'),
                DB::raw('CEIL(AVG(TIMESTAMPDIFF(MONTH, source_timestamp, NOW()))) as months_on_market'),
                )
                ->whereNotNull($price_mode)
                ->whereNotNull('size')
                ->where('business_type_id', $property_type)
                ->where($location->type, $location->id)
                ->where('investment', '<', 1000000)
                ->where('investment', '>', 5000)
                ->where('size', '<', 500)
                ->where('size', '>', 10)
                ->groupBy('price_segment')
                ->orderByRaw($orderby_rental_segment);

            $group_by_investments = Business::select(
                DB::raw($query_investment_segment),
                DB::raw('COUNT(*) as total'),
                DB::raw('CEIL(AVG(investment)) as avg_price'),
                DB::raw('CEIL(AVG(investment / size)) as avg_price_sqm'),
                DB::raw('CEIL(AVG(TIMESTAMPDIFF(MONTH, source_timestamp, NOW()))) as months_on_market'),
                )
                ->whereNotNull($price_mode)
                ->whereNotNull('size')
                ->where('business_type_id', $property_type)
                ->where($location->type, $location->id)
                ->where('investment', '<', 1000000)
                ->where('investment', '>', 5000)
                ->where('size', '<', 500)
                ->where('size', '>', 10)
                ->groupBy('price_segment')
                ->orderByRaw($orderby_investment_segment);

            $businesses = Business::select(
                'id',
                'id_code as idc',
                'name',
                'size',
                'rental',
                'investment',
                DB::raw($query_rental_sqm),
                DB::raw($query_investment_sqm),
                'sectors'
                )
                ->where('flag_active', true)
                ->whereNotNull($price_mode)
                ->whereNotNull('size')
                ->where('business_type_id', $property_type)
                ->where($location->type, $location->id)
                ->where('investment', '<', 1000000)
                ->where('investment', '>', 5000)
                ->where('size', '<', 500)
                ->where('size', '>', 10)
                ->leftJoinSub(function ($query) {
                    $query->select('business_id', DB::raw('GROUP_CONCAT(sector_id) as sectors'))
                        ->from('business_sector')->groupBy('business_id');
                }, 'subquery', 'subquery.business_id', '=', 'businesses.id')
                ->orderBy('investment');

            /**
             * ANADIR POR LOCALIZACION LA MINIMA LOCALIZACION COMPARTIDA
             */
                
            $businesses = $businesses->get();
            $businesses_count = count($businesses);
            $businesses_array = $businesses->toArray();
            $targetSize = 250;
            if ($businesses_count > $targetSize) {
                $downsampledArray = [];
                $step = $businesses_count / $targetSize;

                for ($i = 0; $i < $targetSize; $i++) {
                    $index = (int)($i * $step);
                    $downsampledArray[] = $businesses[$index];
                }
                $businesses_array = $downsampledArray;
            }

            $group_by_rooms  = $business->business_type_id === 3   ? $group_by_rooms->get()   : false;
            $group_by_sectors = $business->business_type_id === 1  ? $group_by_sectors->get() : false;
            $group_by_rentals = $business->rental === NULL ? false : $group_by_rentals->get();
            $group_by_investments = $business->investment === NULL ? false : $group_by_investments->get();

            $statsByLocation[] = array(
                'location'         =>  $location,
                'group_by_rooms'   =>  $group_by_rooms,
                'group_by_sectors' =>  $group_by_sectors,
                'group_by_rentals' =>  $group_by_rentals,
                'group_by_investments' => $group_by_investments,
                'businesses'       =>  array(
                    'counts' => array(
                        'total' => $businesses_count,
                        'rental' => array(
                            'min' => $businesses->min('rental_sqm'),
                            'max' => $businesses->max('rental_sqm'),
                            'avg' => round($businesses->avg('rental_sqm')),
                            'median' => $businesses->median('rental_sqm'),
                            'lower_quartile' => $businesses->where('rental_sqm', '<', $businesses->median('rental_sqm'))->median('rental_sqm'),
                            'upper_quartile' => $businesses->where('rental_sqm', '>', $businesses->median('rental_sqm'))->median('rental_sqm'),
                        ),
                        'investment' => array(
                            'min' => $businesses->min('investment_sqm'),
                            'max' => $businesses->max('investment_sqm'),
                            'avg' => round($businesses->avg('investment_sqm')),
                            'median' => $businesses->median('investment_sqm'),
                            'lower_quartile' => $businesses->where('investment_sqm', '<', $businesses->median('investment_sqm'))->median('investment_sqm'),
                            'upper_quartile' => $businesses->where('investment_sqm', '>', $businesses->median('investment_sqm'))->median('investment_sqm'),
                        ),
                    ),
                    'data' => $businesses_array
                )
            );
        }

        return response()->json([
            'type'             =>  $business->business_type_id,
            'mode'             =>  $price_mode,
            'status'           =>  'success',
            'business'         =>  $business,
            'statsByLocation' =>  $statsByLocation,
        ], 200);
    }
}