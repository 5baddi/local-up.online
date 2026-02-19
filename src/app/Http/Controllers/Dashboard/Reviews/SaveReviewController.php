<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Reviews;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Domains\OpenAIService;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class SaveReviewController extends DashboardController
{
    public function __construct(
        private readonly OpenAIService $openAIService
    ) {
        parent::__construct();
    }

    public function __invoke(string $id, Request $request): RedirectResponse
    {
        if ($request->has('action') && $request->input('action') === 'generate-reply') {
            return $this->generateReviewReply($id, $request);
        }

        return $this->updateReviewReply($id, $request);
    }

    private function updateReviewReply(string $id, Request $request): RedirectResponse
    {
        $reply = $this->googleMyBusinessService->updateBusinessLocationReviewReply($id, $request->input('reply'));
        if (empty($reply)) {
            return redirect()
                ->route('dashboard.reviews.view', ['id' => $id])
                ->withInput()
                ->with(
                    'alert',
                    new Alert(trans('An error occurred while saving your review reply!'))
                );
        }

        return redirect()
            ->route('dashboard.reviews.view', ['id' => $id])
            ->withInput()
            ->with(
                'alert',
                new Alert(trans('Your review reply has been saved successfully'), 'success')
            );
    }

    private function generateReviewReply(string $id, Request $request): RedirectResponse
    {
        if (empty($request->input('review'))) {
            return redirect()
                ->route('dashboard.reviews.view', ['id' => $id])
                ->withInput()
                ->with(
                    'alert',
                    new Alert(trans('global.review_content_missing'))
                );
        }

        $choices = $this->openAIService->generateTextCompletions(
            trans('global.review_reply_prompt'),
            $request->input('review'),
            $this->user->getId()
        );

        if (! Arr::has($choices[0] ?? [], 'message')) {
            return redirect()
                ->route('dashboard.reviews.view', ['id' => $id])
                ->withInput()
                ->with(
                    'alert',
                    new Alert(trans('An error occurred while generating a reply!'))
                );
        }

        $request->merge(['reply' => $choices[0]['message']['content'] ?? '']);

        return redirect()
            ->route('dashboard.reviews.view', ['id' => $id])
            ->withInput()
            ->with(
                'alert',
                new Alert(trans('Review reply has been generated successfully. make your change then save it!'), 'success')
            );
    }
}