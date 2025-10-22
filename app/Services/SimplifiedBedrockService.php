<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimplifiedBedrockService
{
    private string $bearerToken;
    private string $region;

    public function __construct()
    {
        $this->bearerToken = config('aws.bearer_token_bedrock');
        $this->region = config('aws.bedrock_region', 'us-east-1');
    }

    public function generateLinkedInPost(array $parameters): string
    {
        if (empty($this->bearerToken)) {
            return $this->getFallbackContent($parameters);
        }

        $prompt = $this->buildPrompt($parameters);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'Content-Type' => 'application/json',
            ])->post("https://bedrock-runtime.{$this->region}.amazonaws.com/model/amazon.nova-lite-v1:0/invoke", [
                'inputText' => $prompt,
                'textGenerationConfig' => [
                    'maxTokenCount' => 500,
                    'temperature' => 0.7,
                    'topP' => 0.9,
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['results'][0]['outputText'] ?? $this->getFallbackContent($parameters);
            }

        } catch (\Exception $e) {
            Log::error('Bedrock API Error: ' . $e->getMessage());
        }

        return $this->getFallbackContent($parameters);
    }

    public function analyzeConnectionRequest(array $profileData): array
    {
        if (empty($this->bearerToken)) {
            return ['decision' => 'accept', 'confidence' => 0.8, 'reason' => 'Default acceptance'];
        }

        $prompt = $this->buildConnectionAnalysisPrompt($profileData);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'Content-Type' => 'application/json',
            ])->post("https://bedrock-runtime.{$this->region}.amazonaws.com/model/amazon.nova-lite-v1:0/invoke", [
                'inputText' => $prompt,
                'textGenerationConfig' => [
                    'maxTokenCount' => 200,
                    'temperature' => 0.3,
                    'topP' => 0.8,
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $analysis = $result['results'][0]['outputText'] ?? '';
                return $this->parseConnectionDecision($analysis);
            }

        } catch (\Exception $e) {
            Log::error('Bedrock Connection Analysis Error: ' . $e->getMessage());
        }

        return ['decision' => 'accept', 'confidence' => 0.8, 'reason' => 'Analysis failed, defaulting to accept'];
    }

    private function buildPrompt(array $parameters): string
    {
        $type = $parameters['type'] ?? 'medium';
        $tone = $parameters['tone'] ?? 'professional';
        $theme = $parameters['themes'][0] ?? 'industry_insights';
        $voice = $parameters['voice'] ?? 'professional';
        $diction = $parameters['diction'] ?? 'business';

        return "Generate a {$type} LinkedIn post with the following parameters:
- Tone: {$tone}
- Theme: {$theme}
- Voice: {$voice}
- Diction: {$diction}

Requirements:
- Professional and engaging content
- Include relevant hashtags
- Appropriate length for {$type} post
- Maintain {$tone} tone throughout
- Focus on {$theme} topic

Generate only the post content without additional commentary.";
    }

    private function buildConnectionAnalysisPrompt(array $profileData): string
    {
        $industry = $profileData['industry'] ?? 'Unknown';
        $location = $profileData['location'] ?? 'Unknown';
        $headline = $profileData['headline'] ?? 'Unknown';
        
        return "Analyze this LinkedIn connection request:
Industry: {$industry}
Location: {$location}
Headline: {$headline}

Determine if this connection should be accepted based on professional relevance and networking value.
Respond with: ACCEPT or REJECT followed by confidence score (0.0-1.0) and brief reason.
Format: DECISION|CONFIDENCE|REASON";
    }

    private function parseConnectionDecision(string $analysis): array
    {
        $parts = explode('|', $analysis);
        
        return [
            'decision' => strtolower(trim($parts[0] ?? 'accept')),
            'confidence' => (float) ($parts[1] ?? 0.8),
            'reason' => trim($parts[2] ?? 'No reason provided'),
        ];
    }

    private function getFallbackContent(array $parameters): string
    {
        $templates = [
            'industry_insights' => 'The future of technology continues to evolve rapidly. Staying ahead requires continuous learning and adaptation. What trends are you watching in your industry? #Technology #Innovation #ProfessionalGrowth',
            'career_tips' => 'Building meaningful professional relationships takes time and genuine interest in others. Focus on how you can add value to your network. #Networking #CareerGrowth #ProfessionalDevelopment',
            'networking' => 'Quality connections matter more than quantity. Invest in relationships that are mutually beneficial and authentic. #Networking #ProfessionalRelationships #LinkedIn',
            'motivation' => 'Every challenge is an opportunity to grow stronger and more resilient. Embrace the journey of continuous improvement. #Motivation #Growth #Success',
        ];

        $theme = $parameters['themes'][0] ?? 'industry_insights';
        return $templates[$theme] ?? $templates['industry_insights'];
    }
}