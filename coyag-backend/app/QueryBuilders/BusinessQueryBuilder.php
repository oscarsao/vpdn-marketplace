<?php

namespace App\QueryBuilders;

use App\Models\Business;
use App\Models\BusinessMultimedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessQueryBuilder
{
    protected Builder $query;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->query = Business::leftJoin('business_types', 'business_types.id', '=', 'businesses.business_type_id')
            ->leftJoin('neighborhoods', 'neighborhoods.id', '=', 'businesses.neighborhood_id')
            ->leftJoin('districts', 'districts.id', '=', 'businesses.district_id')
            ->leftJoin('municipalities', 'municipalities.id', '=', 'businesses.municipality_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'municipalities.province_id')
            ->leftJoin('autonomous_communities', 'autonomous_communities.id', '=', 'provinces.autonomous_community_id');
    }

    /**
     * Apply business condition filter (type of listing).
     */
    public function applyCondition(): static
    {
        if ($this->request->exists('condition')) {
            match ($this->request->condition) {
                'mistery_option' => $this->query->whereNotIn('businesses.id',
                    BusinessMultimedia::where('type', 'image')->distinct()->pluck('business_id')
                ),
                'with_analysis' => $this->query->whereIn('businesses.id',
                    BusinessMultimedia::where('type', 'video')->distinct()
                        ->whereIn('type_client', ['usuario.free', 'all'])->pluck('business_id')
                ),
                'franchise' => $this->query->where('businesses.flag_active', true)
                    ->whereIn('businesses.business_type_id', [2, 13]),
                'inmuebles' => $this->query->where('businesses.flag_active', true)
                    ->whereIn('businesses.business_type_id', [3]),
                default => $this->query->where('businesses.flag_active', true)
                    ->whereIn('businesses.business_type_id', [1]),
            };
        } else {
            $this->query->where('businesses.flag_active', true)
                ->whereIn('businesses.business_type_id', [1]);
        }

        return $this;
    }

    /**
     * Apply location filters.
     */
    public function filterByLocation(): static
    {
        $locationFilters = [
            'id_code'                => 'businesses.id_code',
            'province_id'            => 'provinces.id',
            'municipality_id'        => 'municipalities.id',
            'district_id'            => 'districts.id',
            'neighborhood_id'        => 'neighborhoods.id',
            'autonomous_community_id' => 'autonomous_communities.id',
        ];

        foreach ($locationFilters as $param => $column) {
            if ($this->request->exists($param)) {
                $this->query->where($column, $this->request->input($param));
            }
        }

        if ($this->request->exists('neighborhood')) {
            $this->query->where('neighborhoods.name', 'like', '%' . $this->request->neighborhood . '%');
        }

        return $this;
    }

    /**
     * Apply investment/price range filters.
     */
    public function filterByPriceRange(): static
    {
        if (!$this->request->exists('min_investment') && $this->request->exists('hide_priceless_biz') && $this->request->hide_priceless_biz == 1) {
            $this->query->where('businesses.investment', '>=', 100);
        }

        $rangeFilters = [
            'min_investment' => ['businesses.investment', '>='],
            'max_investment' => ['businesses.investment', '<='],
            'min_rental'     => ['businesses.rental', '>='],
            'max_rental'     => ['businesses.rental', '<='],
        ];

        foreach ($rangeFilters as $param => [$column, $operator]) {
            if ($this->request->exists($param)) {
                $this->query->where($column, $operator, $this->request->input($param));
            }
        }

        return $this;
    }

    /**
     * Apply boolean flag filters.
     */
    public function filterByFlags(): static
    {
        $booleanFlags = ['flag_sold', 'flag_smoke_outlet', 'flag_terrace', 'flag_outstanding'];

        foreach ($booleanFlags as $flag) {
            if ($this->request->exists($flag) && in_array(strval($this->request->input($flag)), ['0', '1'])) {
                $this->query->where("businesses.{$flag}", $this->request->input($flag));
            }
        }

        if ($this->request->exists('only_with_gcs') && $this->request->only_with_gcs == 1) {
            $this->query->whereNotNull('businesses.lat')->whereNotNull('businesses.lng');
        }

        return $this;
    }

    /**
     * Apply property-specific filters (rooms, bathrooms, etc.).
     */
    public function filterByPropertyFeatures(): static
    {
        $propertyRanges = [
            'minrooms'      => ['businesses.rooms', '>='],
            'maxrooms'      => ['businesses.rooms', '<='],
            'minbathrooms'  => ['businesses.bathrooms', '>='],
            'maxbathrooms'  => ['businesses.bathrooms', '<='],
        ];

        foreach ($propertyRanges as $param => [$column, $operator]) {
            if ($this->request->exists($param)) {
                $this->query->where($column, $operator, $this->request->input($param));
            }
        }

        $exactMatches = [
            'garage'      => ['businesses.garage', 'si'],
            'storage'     => ['businesses.storage', 'si'],
            'pool'        => ['businesses.pool', 'si'],
            'elevator'    => ['businesses.elevator', 'Con ascensor'],
        ];

        foreach ($exactMatches as $param => [$column, $value]) {
            if ($this->request->exists($param)) {
                $this->query->where($column, $value);
            }
        }

        if ($this->request->exists('courtyard')) {
            $this->query->where('businesses.courtyard', $this->request->courtyard);
        }
        if ($this->request->exists('new_or_used')) {
            $this->query->where('businesses.new_or_used', $this->request->new_or_used);
        }

        return $this;
    }

    /**
     * Apply text search filter.
     */
    public function filterBySearch(): static
    {
        if ($this->request->exists('name') && $this->request->name != '') {
            $search = $this->request->name;
            $this->query->where(function ($q) use ($search) {
                $q->where('businesses.name', 'like', "%{$search}%")
                  ->orWhere('businesses.description', 'like', "%{$search}%");
            });
        }

        return $this;
    }

    /**
     * Apply sector filter.
     */
    public function filterBySectors(): static
    {
        if ($this->request->exists('sectors')) {
            $this->query->whereIn('businesses.id', function ($q) {
                $q->select('business_id')
                  ->from('business_sector')
                  ->whereIn('business_sector.sector_id', explode(',', $this->request->sectors));
            });
        }

        return $this;
    }

    /**
     * Apply sale/rental type filter.
     */
    public function filterByListingType(): static
    {
        if ($this->request->exists('filterby')) {
            if ($this->request->filterby === 'VENTA') {
                $this->query->whereNull('businesses.rental')->whereNotNull('businesses.investment');
            } elseif ($this->request->filterby === 'ALQUILER') {
                $this->query->whereNull('businesses.investment')->whereNotNull('businesses.rental');
            }
        }

        return $this;
    }

    /**
     * Apply ordering.
     */
    public function applyOrdering(): static
    {
        if (!$this->request->exists('order_by')) {
            return $this;
        }

        match ($this->request->order_by) {
            'investment_asc'  => $this->query->orderBy('businesses.investment', 'asc'),
            'investment_desc' => $this->query->orderBy('businesses.investment', 'desc'),
            'date_asc'        => $this->query->orderBy('businesses.created_at', 'asc'),
            'date_desc'       => $this->query->orderBy('businesses.created_at', 'desc'),
            'most_relevant'   => $this->query->orderBy('businesses.flag_outstanding', 'desc')
                                    ->orderBy('businesses.flag_exclusive', 'desc'),
            'size_asc'        => $this->query->orderBy('businesses.size', 'asc')
                                    ->where('businesses.size', '<', 10000)
                                    ->where('businesses.size', '>', 10),
            'size_desc'       => $this->query->orderBy('businesses.size', 'desc')
                                    ->where('businesses.size', '<', 10000)
                                    ->where('businesses.size', '>', 10),
            'investment_sqm_asc' => $this->query->orderBy(DB::raw('CEIL(investment / size)'), 'asc')
                                    ->where('businesses.size', '<', 10000)
                                    ->where('businesses.size', '>', 10),
            'investment_sqm_desc' => $this->query->orderBy(DB::raw('CEIL(investment / size)'), 'desc')
                                    ->where('businesses.size', '<', 10000)
                                    ->where('businesses.size', '>', 10),
            default => null,
        };

        return $this;
    }

    /**
     * Apply all filters in sequence. This is the main entry point.
     */
    public function applyAllFilters(): static
    {
        return $this
            ->applyCondition()
            ->filterByLocation()
            ->filterByPriceRange()
            ->filterByFlags()
            ->filterByPropertyFeatures()
            ->filterBySearch()
            ->filterBySectors()
            ->filterByListingType()
            ->applyOrdering();
    }

    /**
     * Get the underlying query builder.
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }
}
