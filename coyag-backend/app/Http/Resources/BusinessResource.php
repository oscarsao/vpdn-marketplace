<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'id_code'       => $this->id_code,
            'name'          => $this->name,
            'description'   => $this->description,
            'investment'    => $this->investment,
            'rental'        => $this->rental,
            'size'          => $this->size,
            'address'       => $this->address,
            'lat'           => $this->lat,
            'lng'           => $this->lng,
            'flag_active'   => (bool) $this->flag_active,
            'flag_sold'     => (bool) $this->flag_sold,
            'flag_outstanding' => (bool) $this->flag_outstanding,
            'flag_exclusive'   => (bool) $this->flag_exclusive,
            'times_viewed'  => $this->times_viewed,
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),

            // Relations (loaded conditionally)
            'neighborhood'  => $this->whenLoaded('neighborhood', fn() => [
                'id'   => $this->neighborhood->id,
                'name' => $this->neighborhood->name,
            ]),
            'district'      => $this->whenLoaded('district', fn() => [
                'id'   => $this->district->id,
                'name' => $this->district->name,
            ]),
            'municipality'  => $this->whenLoaded('municipality', fn() => [
                'id'       => $this->municipality->id,
                'name'     => $this->municipality->name,
                'province' => $this->municipality->province ? [
                    'id'   => $this->municipality->province->id,
                    'name' => $this->municipality->province->name,
                ] : null,
            ]),
            'business_type' => $this->whenLoaded('business_type', fn() => [
                'id'   => $this->business_type->id,
                'name' => $this->business_type->name,
            ]),
            'employee'      => $this->whenLoaded('employee', fn() => [
                'id'      => $this->employee->id,
                'name'    => $this->employee->name,
                'surname' => $this->employee->surname,
            ]),
            'sectors'       => $this->whenLoaded('sector', fn() =>
                $this->sector->map(fn($s) => [
                    'id'   => $s->id,
                    'name' => $s->name,
                ])
            ),
            'multimedia'    => $this->whenLoaded('business_multimedia'),
        ];
    }
}
