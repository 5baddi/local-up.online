<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Ai;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use BADDIServices\ClnkGO\AppLogger;
use App\Http\Requests\AiGenerateTextRequest;
use BADDIServices\ClnkGO\Domains\OpenAIService;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class GenerateTextController extends DashboardController
{
    public function __construct(
        private readonly OpenAIService $openAIService
    ) {
        parent::__construct();
    }

    public function __invoke(AiGenerateTextRequest $request): JsonResponse
    {
        try {
            $prompt = $request->input('prompt', '');

            $prompt = in_array($prompt, OpenAIService::SUPPORTED_PROMPTS)
                ? sprintf('global.%s', $prompt)
                : $prompt;

            $choices = $this->openAIService->generateTextCompletions(
                $prompt,
                $request->input('topic', ''),
                $this->user->getId()
            );

            return response()
                ->json(['text' => $choices[0]['message']['content'] ?? '']);
        } catch (Throwable $e) {
            AppLogger::error(
                $e,
                'ai:generate-text',
                ['payload' => $request->toArray()]
            );

            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}