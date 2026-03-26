<?php

namespace App\Services\Ai;

use App\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected AiProviderInterface $provider;

    public function __construct()
    {
        $this->provider = $this->resolveProvider();
    }

    /**
     * Resolve the configured AI provider.
     */
    protected function resolveProvider(): AiProviderInterface
    {
        $providerName = config('ai.provider', 'openai');

        return match ($providerName) {
            'openai'    => new OpenAiProvider(),
            // 'gemini'    => new GeminiProvider(),
            // 'anthropic' => new AnthropicProvider(),
            default     => new OpenAiProvider(),
        };
    }

    /**
     * Check if AI is available and configured.
     */
    public function isAvailable(): bool
    {
        return $this->provider->isConfigured();
    }

    /**
     * Get a general AI chat response.
     */
    public function chat(string $prompt, ?string $systemPrompt = null, array $options = []): array
    {
        if (!$this->isAvailable()) {
            return ['content' => '', 'error' => 'AI provider not configured'];
        }

        return $this->provider->chat($prompt, $systemPrompt, $options);
    }

    /**
     * Analyze a business for valuation and insights.
     */
    public function analyzeBusinessValuation(array $businessData): array
    {
        if (!config('ai.features.business_analysis')) {
            return ['error' => 'Business analysis feature is disabled'];
        }

        return $this->provider->analyze($businessData, 'business_valuation');
    }

    /**
     * Get market comparison for a business.
     */
    public function compareWithMarket(array $businessData): array
    {
        return $this->provider->analyze($businessData, 'market_comparison');
    }

    /**
     * Calculate ROI estimate for an investment.
     */
    public function estimateRoi(array $investmentData): array
    {
        return $this->provider->analyze($investmentData, 'investment_roi');
    }

    /**
     * Get personalized recommendations for a client.
     */
    public function getRecommendations(array $clientProfile, array $availableBusinesses): array
    {
        if (!config('ai.features.recommendations')) {
            return ['error' => 'Recommendations feature is disabled'];
        }

        $data = [
            'client' => $clientProfile,
            'businesses' => $availableBusinesses,
        ];

        return $this->provider->analyze($data, 'recommendation');
    }

    /**
     * Generate a business description for marketing.
     */
    public function generateBusinessDescription(array $businessData): array
    {
        if (!config('ai.features.content_generation')) {
            return ['error' => 'Content generation feature is disabled'];
        }

        $prompt = "Genera una descripción atractiva y profesional para este negocio/inmueble para publicar en un portal. "
                . "La descripción debe ser persuasiva, destacar los puntos fuertes, y tener entre 150-300 palabras:\n"
                . json_encode($businessData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $systemPrompt = "Eres un copywriter experto en el sector inmobiliario y de negocios en España. "
                       . "Escribe descripciones persuasivas y profesionales en español.";

        return $this->provider->chat($prompt, $systemPrompt, ['temperature' => 0.8]);
    }

    /**
     * Get the current provider info.
     */
    public function getProviderInfo(): array
    {
        return [
            'provider'    => $this->provider->getProviderName(),
            'configured'  => $this->provider->isConfigured(),
            'features'    => config('ai.features'),
        ];
    }
}
