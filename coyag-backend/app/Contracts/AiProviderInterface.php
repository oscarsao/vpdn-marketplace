<?php

namespace App\Contracts;

interface AiProviderInterface
{
    /**
     * Send a chat/completion request to the AI provider.
     *
     * @param string $prompt The user prompt
     * @param string|null $systemPrompt Optional system prompt for context
     * @param array $options Additional options (temperature, etc.)
     * @return array ['content' => string, 'usage' => array, 'model' => string]
     */
    public function chat(string $prompt, ?string $systemPrompt = null, array $options = []): array;

    /**
     * Analyze structured data and return insights.
     *
     * @param array $data The data to analyze
     * @param string $analysisType Type of analysis to perform
     * @return array
     */
    public function analyze(array $data, string $analysisType): array;

    /**
     * Get the name of the current provider.
     */
    public function getProviderName(): string;

    /**
     * Check if the provider is properly configured.
     */
    public function isConfigured(): bool;
}
