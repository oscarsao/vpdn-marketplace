<?php

namespace App\Services\Ai;

use App\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiProvider implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected int $maxTokens;
    protected string $baseUrl;

    public function __construct()
    {
        $config = config('ai.providers.openai');
        $this->apiKey = $config['api_key'] ?? '';
        $this->model = $config['model'] ?? 'gpt-4o';
        $this->maxTokens = $config['max_tokens'] ?? 4096;
        $this->baseUrl = $config['base_url'] ?? 'https://api.openai.com/v1';
    }

    public function chat(string $prompt, ?string $systemPrompt = null, array $options = []): array
    {
        $messages = [];

        if ($systemPrompt) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }

        $messages[] = ['role' => 'user', 'content' => $prompt];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type'  => 'application/json',
            ])->post("{$this->baseUrl}/chat/completions", [
                'model'      => $options['model'] ?? $this->model,
                'messages'   => $messages,
                'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
                'temperature' => $options['temperature'] ?? 0.7,
            ]);

            $data = $response->json();

            return [
                'content' => $data['choices'][0]['message']['content'] ?? '',
                'usage'   => $data['usage'] ?? [],
                'model'   => $data['model'] ?? $this->model,
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            return [
                'content' => '',
                'error'   => $e->getMessage(),
                'usage'   => [],
                'model'   => $this->model,
            ];
        }
    }

    public function analyze(array $data, string $analysisType): array
    {
        $prompt = $this->buildAnalysisPrompt($data, $analysisType);
        $systemPrompt = "Eres un analista experto de negocios e inmuebles en España. Responde siempre en español. Proporciona datos concretos y recomendaciones accionables.";

        return $this->chat($prompt, $systemPrompt, ['temperature' => 0.3]);
    }

    public function getProviderName(): string
    {
        return 'openai';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    protected function buildAnalysisPrompt(array $data, string $analysisType): string
    {
        $dataJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return match ($analysisType) {
            'business_valuation' => "Analiza este negocio y proporciona una valoración estimada con justificación:\n{$dataJson}",
            'market_comparison'  => "Compara este negocio con el mercado actual y proporciona insights:\n{$dataJson}",
            'investment_roi'     => "Calcula el ROI estimado y análisis de riesgo para esta inversión:\n{$dataJson}",
            'client_profile'     => "Analiza el perfil de este cliente y sugiere negocios que podrían interesarle:\n{$dataJson}",
            'recommendation'     => "Basándote en las preferencias del cliente, recomienda los mejores negocios:\n{$dataJson}",
            default              => "Analiza los siguientes datos y proporciona insights relevantes:\n{$dataJson}",
        };
    }
}
