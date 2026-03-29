<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Client;
use App\Services\Ai\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AiController extends Controller
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get AI provider status and configuration.
     *
     * GET /api/v1/ai/status
     */
    public function status()
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->aiService->getProviderInfo(),
        ]);
    }

    /**
     * AI-powered business valuation analysis.
     *
     * POST /api/v1/ai/business-analysis
     */
    public function businessAnalysis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|numeric|exists:businesses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $business = Business::with(['neighborhood', 'district', 'municipality', 'sector', 'business_type'])
            ->find($request->business_id);

        $businessData = [
            'name'        => $business->name,
            'description' => $business->description,
            'investment'  => $business->investment,
            'rental'      => $business->rental,
            'size'        => $business->size,
            'location'    => $business->municipality?->name,
            'type'        => $business->business_type?->name,
            'sectors'     => $business->sector?->pluck('name')->toArray(),
            'age'         => $business->age,
        ];

        $result = $this->aiService->analyzeBusinessValuation($businessData);

        return response()->json([
            'status'   => isset($result['error']) ? 'error' : 'success',
            'data'     => $result,
            'business' => $businessData,
        ]);
    }

    /**
     * AI-powered personalized recommendations.
     *
     * POST /api/v1/ai/recommendations
     */
    public function recommendations(Request $request)
    {
        $user = Auth::user();
        $client = $user->client ?? null;

        if (!$client) {
            return response()->json(['error' => 'Solo clientes pueden recibir recomendaciones'], 403);
        }

        $clientProfile = [
            'id'   => $client->id,
            'name' => $client->names . ' ' . $client->surnames,
        ];

        // Get active businesses as candidates
        $businesses = Business::where('flag_active', true)
            ->where('flag_sold', false)
            ->select('id', 'name', 'investment', 'rental', 'size', 'business_type_id')
            ->limit(20)
            ->get()
            ->toArray();

        $result = $this->aiService->getRecommendations($clientProfile, $businesses);

        return response()->json([
            'status' => isset($result['error']) ? 'error' : 'success',
            'data'   => $result,
        ]);
    }

    /**
     * AI-powered description generation.
     *
     * POST /api/v1/ai/generate-description
     */
    public function generateDescription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|numeric|exists:businesses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $business = Business::with(['municipality', 'business_type', 'sector'])
            ->find($request->business_id);

        $businessData = [
            'name'        => $business->name,
            'investment'  => $business->investment,
            'rental'      => $business->rental,
            'size'        => $business->size,
            'location'    => $business->municipality?->name,
            'type'        => $business->business_type?->name,
            'sectors'     => $business->sector?->pluck('name')->toArray(),
        ];

        $result = $this->aiService->generateBusinessDescription($businessData);

        return response()->json([
            'status' => isset($result['error']) ? 'error' : 'success',
            'data'   => $result,
        ]);
    }

    /**
     * General AI chat endpoint.
     *
     * POST /api/v1/ai/chat
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $systemPrompt = "Eres un asistente experto de la plataforma COYAG (Cohen y Aguirre). "
                       . "Ayudas a los usuarios con consultas sobre negocios, inmuebles, inversiones y servicios de la plataforma. "
                       . "Responde siempre en español de forma clara y profesional.";

        $result = $this->aiService->chat($request->message, $systemPrompt);

        return response()->json([
            'status' => isset($result['error']) ? 'error' : 'success',
            'data'   => $result,
        ]);
    }

    /**
     * Find similar businesses based on sector/location/price.
     *
     * POST /api/v1/ai/similar
     */
    public function similar(Request $request)
    {
        try {
            $businessId = $request->input('business_id');
            $limit = $request->input('limit', 6);
            $business = Business::find($businessId);
            if (!$business) return response()->json(['error' => 'Business not found'], 404);

            $similar = Business::where('id', '!=', $businessId)
                ->where('business_type_id', $business->business_type_id)
                ->where('flag_active', 1)
                ->where('flag_sold', 0)
                ->when($business->municipality_id, fn($q) => $q->where('municipality_id', $business->municipality_id))
                ->orderByRaw('ABS(investment - ?) ASC', [$business->investment ?? 0])
                ->limit($limit)
                ->get(['id', 'id_code_business', 'name', 'investment', 'rental', 'size', 'lat', 'lng', 'business_images_string']);

            return response()->json(['data' => $similar]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Return market stats for a business's area.
     *
     * POST /api/v1/ai/market-context
     */
    public function marketContext(Request $request)
    {
        try {
            $businessId = $request->input('business_id');
            $business = Business::find($businessId);
            if (!$business) return response()->json(['error' => 'Business not found'], 404);

            $areaBusinesses = Business::where('municipality_id', $business->municipality_id)
                ->where('business_type_id', $business->business_type_id)
                ->where('flag_active', 1)
                ->where('flag_sold', 0)
                ->get(['investment', 'rental', 'size']);

            $context = [
                'total_in_area'   => $areaBusinesses->count(),
                'avg_investment'  => round($areaBusinesses->avg('investment'), 2),
                'avg_rental'      => round($areaBusinesses->avg('rental'), 2),
                'avg_size'        => round($areaBusinesses->avg('size'), 2),
                'min_investment'  => $areaBusinesses->min('investment'),
                'max_investment'  => $areaBusinesses->max('investment'),
                'price_position'  => $business->investment
                    ? round(($areaBusinesses->where('investment', '<', $business->investment)->count() / max($areaBusinesses->count(), 1)) * 100, 1)
                    : null,
            ];

            return response()->json(['data' => $context]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * AI-powered search.
     *
     * POST /api/v1/ai/search
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query', '');
            $businesses = Business::where('flag_active', 1)
                ->where('flag_sold', 0)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->limit(20)
                ->get(['id', 'id_code_business', 'name', 'investment', 'rental', 'size', 'lat', 'lng', 'business_images_string', 'description']);

            return response()->json(['data' => $businesses]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
