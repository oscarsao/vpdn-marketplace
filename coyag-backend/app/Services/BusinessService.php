<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessMultimedia;
use App\Models\BusinessTimeline;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BusinessService
{
    /**
     * Fillable fields for Business model mass-assignment.
     * These are all the fields that can be set via store/update.
     */
    protected array $fillableFields = [
        'neighborhood_id', 'district_id', 'description', 'address',
        'lat', 'lng', 'link_map', 'zip_code', 'size', 'age',
        'website', 'link', 'private_comment', 'data_of_interest',
        'relevant_advantages', 'monthly_billing', 'contact_name',
        'contact_landline', 'contact_mobile_phone', 'contact_email',
        'amount_requested_by_seller', 'amount_offered_by_us',
        'investment', 'royalty', 'rental', 'contract',
        'rental_contract_years', 'franchise_contract_years',
        'rental_contract_years_left', 'franchise_contract_years_left',
        'minimum_population', 'canon_of_advertising', 'canon_of_entrance',
        'flag_exclusive', 'flag_active', 'flag_sold', 'flag_outstanding',
        'return_of_investment', 'working_hours', 'full_time_employees',
        'employees_part_time', 'managers', 'gross_revenue', 'gross_profit',
        'expenses', 'net', 'owner_salary', 'good_month_revenue',
        'bad_month_revenue', 'advertiser_owner_type', 'facade_size',
        'flag_smoke_outlet', 'flag_terrace', 'profit_sale', 'profit_rent',
        'reform_price', 'source_timestamp', 'business_images_string',
        'business_videos_string',
    ];

    /**
     * Fill a Business model with validated request data,
     * replacing the 40+ individual if-exists checks.
     */
    public function fillBusinessFromRequest(Business $business, Request $request): Business
    {
        foreach ($this->fillableFields as $field) {
            if ($request->exists($field)) {
                $value = $request->input($field);
                // Handle 'null' string values
                if ($value === 'null') {
                    $value = null;
                }
                $business->{$field} = $value;
            }
        }

        // Auto-calculate price per square meter
        if (!empty($business->rental) && !empty($business->size)) {
            $business->price_per_sqm = round($business->rental / $business->size, 2);
        }

        return $business;
    }

    /**
     * Create a new business with all related data.
     */
    public function createBusiness(Request $request): array
    {
        // Validate sectors if provided
        if ($request->exists('sectors')) {
            $sectors = explode(',', $request->sectors);
            foreach ($sectors as $sectorId) {
                if (Sector::where('id', $sectorId)->count() == 0) {
                    return ['success' => false, 'errors' => "El Sector con ID {$sectorId} no existe"];
                }
            }
        }

        $business = new Business();
        $business->municipality_id = $request->municipality_id;
        $business->business_type_id = $request->business_type_id;
        $business->employee_id = Auth::user()->employee->id;
        $business->name = $request->name;
        $business->id_code = Business::max('id_code') + 1;

        $this->fillBusinessFromRequest($business, $request);

        if (!$business->save()) {
            return ['success' => false, 'errors' => 'No se pudo crear el Negocio'];
        }

        // Handle multimedia
        $errorsMultimedia = addResourceBusinessMultimedia($business, $request);
        $errorsDossiers = addDossierInBusiness($business->id, $request);
        $errorsBusiest = addBusiestInBusiness($business->id, $request);
        addBusinessTimeline($business->id, 'Business', 'create');

        // Attach sectors
        if ($request->exists('sectors')) {
            $sectors = explode(',', $request->sectors);
            foreach ($sectors as $sectorId) {
                $business->sector()->attach($sectorId);
            }
        }

        // Handle recommendations
        if ($request->exists('recommendation_clients')) {
            // This method needs to be moved from the controller trait eventually
        }

        $hasErrors = count($errorsMultimedia) > 0 || $errorsDossiers !== null || $errorsBusiest !== null;
        return [
            'success' => true,
            'errors_multimedia' => $errorsMultimedia,
            'errors_dossier' => $errorsDossiers,
            'errors_busiest' => $errorsBusiest,
            'has_errors' => $hasErrors,
        ];
    }

    /**
     * Update an existing business.
     */
    public function updateBusiness(Business $business, Request $request): array
    {
        // Handle sectors
        if ($request->exists('sectors')) {
            $sectors = explode(',', $request->sectors);
            foreach ($sectors as $sectorId) {
                if (Sector::where('id', $sectorId)->count() == 0) {
                    return ['success' => false, 'errors' => "El Sector con ID {$sectorId} no existe"];
                }
            }
            $business->sector()->detach();
            foreach ($sectors as $sectorId) {
                $business->sector()->attach($sectorId);
            }
        }

        // Handle municipality/business_type updates
        if ($request->exists('municipality_id')) {
            $business->municipality_id = $request->municipality_id;
        }
        if ($request->exists('business_type_id')) {
            $business->business_type_id = $request->business_type_id;
        }
        if ($request->exists('name')) {
            $business->name = $request->name;
        }

        $this->fillBusinessFromRequest($business, $request);

        if (!$business->save()) {
            return ['success' => false, 'errors' => 'No se pudo actualizar el Negocio'];
        }

        addBusinessTimeline($business->id, 'Business', 'update');
        return ['success' => true];
    }

    /**
     * Delete a business and all its related data.
     */
    public function deleteBusiness(Business $business): array
    {
        $businessId = $business->id;

        if (!$business->delete()) {
            return ['success' => false, 'errors' => 'No se pudo borrar el negocio'];
        }

        if (BusinessMultimedia::where('business_id', $businessId)->delete()) {
            File::deleteDirectory(public_path("files/business-multimedia/{$businessId}"));
        }

        addBusinessTimeline($businessId, 'Business', 'delete');
        BusinessTimeline::where('business_id', $businessId)->delete();

        return ['success' => true];
    }
}
