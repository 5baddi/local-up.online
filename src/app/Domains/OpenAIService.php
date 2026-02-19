<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Domains;

use Throwable;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use BADDIServices\ClnkGO\AppLogger;
use BADDIServices\ClnkGO\Services\Service;

class OpenAIService extends Service
{
    public const string BASE_API_URI = 'https://api.openai.com';

    public const string TEXT_GENERATION_ENDPOINT = '/v1/chat/completions';

    public const SUPPORTED_PROMPTS = [
        'review_reply_prompt',
        'generate_gmb_post_summary_prompt',
    ];

    private Client $client;

    public function __construct()
    {
        parent::__construct();

        $this->configure();
    }

    public function generateTextCompletions(string $prompt, string $text, ?string $userId = null): array
    {
        try {
            $payload = [
                'model'         => 'gpt-4-turbo',
                'messages'      => [
                    [
                        'role'      => 'user',
                        'content'   => sprintf('%s: %s', $prompt, $text),
                    ]
                ],
                'max_tokens'    => 300,
            ];

            if (! empty($userId)) {
                $payload['user'] = $userId;
            }

            $response = $this->client->post(self::TEXT_GENERATION_ENDPOINT, ['body' => json_encode($payload)]);
            $results = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() !== 200 || ! Arr::has($results, ['choices'])) {
                AppLogger::error(
                    new Exception('Open AI text generation failed!'),
                    'open-ai:generate-text',
                    ['payload' => func_get_args(), 'response' => $results]
                );

                return [];
            }

            return $results['choices'];
        } catch (Throwable $e) {
            AppLogger::error(
                $e,
                'open-ai:generate-text',
                ['payload' => func_get_args()]
            );

            return [];
        }
    }

    private function configure(): void
    {
        $this->client = new Client([
            'base_uri'          => self::BASE_API_URI,
            'debug'             => false,
            'http_errors'       => false,
            'headers'           => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => sprintf('Bearer %s', config('openai.api.key', '')),
            ]
        ]);
    }
}