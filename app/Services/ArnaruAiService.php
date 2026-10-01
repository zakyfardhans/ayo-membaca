<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ArnaruAiService
{
    public function chat(string $question, array $options = [], ?string $pdfPath = null): array
    {
        $payload = array_filter([
            'question' => $question,
            'model' => $options['model'] ?? config('services.arnaru_ai.model'),
            'conversationId' => $options['conversationId'] ?? null,
            'webSearch' => $options['webSearch'] ?? false,
            'systemPrompt' => $options['systemPrompt'] ?? null,
        ], static fn ($value) => $value !== null);

        $request = $this->request();

        if ($pdfPath !== null) {
            if (! Storage::disk('public')->exists($pdfPath)) {
                throw new RuntimeException('The selected book PDF is no longer available.');
            }

            $stream = fopen(Storage::disk('public')->path($pdfPath), 'r');

            try {
                $response = $request
                    ->attach('files', $stream, basename($pdfPath), ['Content-Type' => 'application/pdf'])
                    ->post($this->endpoint(), $payload);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        } else {
            $response = $request->post($this->endpoint(), $payload);
        }

        if (! $response->successful()) {
            throw new ArnaruAiException($response->status(), 'Arnaru AI returned HTTP '.$response->status().'.');
        }

        $data = $response->json();
        $answer = $this->findString($data, ['answer', 'response', 'content', 'text', 'message', 'result', 'output']);

        if ($answer === null) {
            throw new RuntimeException('Arnaru AI returned an unsupported response format.');
        }

        return [
            'answer' => $answer,
            'conversationId' => $this->findString($data, ['conversationId', 'conversation_id']),
        ];
    }

    private function request(): PendingRequest
    {
        $request = Http::acceptJson()
            ->timeout(config('services.arnaru_ai.timeout', 120))
            ->connectTimeout(15);

        $token = config('services.arnaru_ai.token');

        return $token ? $request->withToken($token) : $request;
    }

    private function endpoint(): string
    {
        return rtrim(config('services.arnaru_ai.url'), '/').'/api/chat';
    }

    private function findString(mixed $value, array $keys): ?string
    {
        if (! is_array($value)) {
            return null;
        }

        foreach ($keys as $key) {
            if (isset($value[$key]) && is_string($value[$key]) && trim($value[$key]) !== '') {
                return trim($value[$key]);
            }
        }

        foreach ($value as $nested) {
            $result = $this->findString($nested, $keys);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }
}
