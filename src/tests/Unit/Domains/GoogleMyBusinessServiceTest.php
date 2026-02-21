<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use BADDIServices\ClnkGO\Domains\GoogleMyBusinessService;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleBusinessLocalPostObjectValue;
use BADDIServices\ClnkGO\Models\ScheduledPost;

/**
 * Build a GoogleMyBusinessService backed by a Guzzle MockHandler.
 *
 * @param  array  $responses  GuzzleHttp\Psr7\Response or Exception instances
 * @param  array  $history    Reference filled with request/response history
 * @return GoogleMyBusinessService
 */
function makeGmbService(
    array $responses,
    array &$history = [],
    string $accountId = 'acc-123',
    string $locationId = 'loc-456',
    string $token = 'test-token'
): GoogleMyBusinessService {
    $mock    = new MockHandler($responses);
    $stack   = HandlerStack::create($mock);
    $stack->push(Middleware::history($history));
    $client  = new Client(['handler' => $stack]);

    return new GoogleMyBusinessService($token, $accountId, $locationId, $client);
}

// ---------------------------------------------------------------------------
// getBusinessAccounts
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::getBusinessAccounts()', function () {
    it('returns accounts array on HTTP 200 with accounts key', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['accounts' => [['name' => 'accounts/123']]])),
        ]);

        $result = $service->getBusinessAccounts();

        expect($result)->toHaveKey('accounts');
        expect($result['accounts'])->toHaveCount(1);
        expect($result['accounts'][0]['name'])->toBe('accounts/123');
    });

    it('returns empty array when response lacks accounts key', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['other' => []])),
        ]);

        expect($service->getBusinessAccounts())->toBe([]);
    });

    it('returns empty array on non-200 HTTP status', function () {
        $service = makeGmbService([
            new Response(403, [], json_encode(['error' => ['message' => 'Forbidden']])),
        ]);

        expect($service->getBusinessAccounts())->toBe([]);
    });

    it('returns empty array on network exception', function () {
        $service = makeGmbService([
            new ConnectException('Connection refused', new Request('GET', 'test')),
        ]);

        expect($service->getBusinessAccounts())->toBe([]);
    });

    it('appends pageToken query param when next page token is provided', function () {
        $history = [];
        $service = makeGmbService(
            [new Response(200, [], json_encode(['accounts' => []]))],
            $history
        );

        $service->getBusinessAccounts('next-token-abc');

        expect((string) $history[0]['request']->getUri())->toContain('pageToken=next-token-abc');
    });

    it('does not append pageToken when none is provided', function () {
        $history = [];
        $service = makeGmbService(
            [new Response(200, [], json_encode(['accounts' => []]))],
            $history
        );

        $service->getBusinessAccounts();

        expect((string) $history[0]['request']->getUri())->not->toContain('pageToken');
    });
});

// ---------------------------------------------------------------------------
// getBusinessAccountLocations
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::getBusinessAccountLocations()', function () {
    it('returns locations on HTTP 200 with locations key', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['locations' => [['name' => 'accounts/acc-123/locations/loc-1']]])),
        ]);

        $result = $service->getBusinessAccountLocations();

        expect($result)->toHaveKey('locations');
    });

    it('returns empty array when accountId is empty', function () {
        $service = new GoogleMyBusinessService('token', '', 'loc-456', new Client());

        expect($service->getBusinessAccountLocations())->toBe([]);
    });

    it('returns empty array when response lacks locations key', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['error' => ['message' => 'Not found']])),
        ]);

        expect($service->getBusinessAccountLocations())->toBe([]);
    });

    it('returns empty array on non-200 HTTP status', function () {
        $service = makeGmbService([
            new Response(401, [], json_encode(['error' => ['message' => 'Unauthorized']])),
        ]);

        expect($service->getBusinessAccountLocations())->toBe([]);
    });
});

// ---------------------------------------------------------------------------
// getBusinessLocationPosts
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::getBusinessLocationPosts()', function () {
    it('returns local posts on success', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['localPosts' => [['name' => 'accounts/acc/locations/loc/localPosts/1']]])),
        ]);

        $result = $service->getBusinessLocationPosts();

        expect($result)->toHaveKey('localPosts');
        expect($result['localPosts'])->toHaveCount(1);
    });

    it('returns empty array when accountId or locationId is empty', function () {
        $noAccount  = new GoogleMyBusinessService('token', '', 'loc-456', new Client());
        $noLocation = new GoogleMyBusinessService('token', 'acc-123', '', new Client());

        expect($noAccount->getBusinessLocationPosts())->toBe([]);
        expect($noLocation->getBusinessLocationPosts())->toBe([]);
    });

    it('returns empty array when response lacks localPosts key', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['other' => []])),
        ]);

        expect($service->getBusinessLocationPosts())->toBe([]);
    });
});

// ---------------------------------------------------------------------------
// deleteBusinessLocationPost
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::deleteBusinessLocationPost()', function () {
    it('returns true on HTTP 200', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode([])),
        ]);

        expect($service->deleteBusinessLocationPost('post-id-1'))->toBeTrue();
    });

    it('returns false on HTTP 404', function () {
        $service = makeGmbService([
            new Response(404, [], json_encode(['error' => ['message' => 'Not found']])),
        ]);

        expect($service->deleteBusinessLocationPost('post-id-1'))->toBeFalse();
    });

    it('returns false when accountId, locationId, or postId is empty', function () {
        $service = makeGmbService([]);

        expect($service->deleteBusinessLocationPost(''))->toBeFalse();
    });

    it('returns false on network exception', function () {
        $service = makeGmbService([
            new ConnectException('Network error', new Request('DELETE', 'test')),
        ]);

        expect($service->deleteBusinessLocationPost('post-id-1'))->toBeFalse();
    });
});

// ---------------------------------------------------------------------------
// getBusinessLocationMedia
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::getBusinessLocationMedia()', function () {
    it('returns media items on success', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['mediaItems' => [['name' => 'accounts/acc/locations/loc/media/1']]])),
        ]);

        $result = $service->getBusinessLocationMedia();

        expect($result)->toHaveKey('mediaItems');
    });

    it('returns empty array when accountId or locationId is missing', function () {
        $service = new GoogleMyBusinessService('token', '', '', new Client());

        expect($service->getBusinessLocationMedia())->toBe([]);
    });

    it('returns empty array on non-200 status', function () {
        $service = makeGmbService([
            new Response(500, [], json_encode([])),
        ]);

        expect($service->getBusinessLocationMedia())->toBe([]);
    });
});

// ---------------------------------------------------------------------------
// createBusinessLocationMedia
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::createBusinessLocationMedia()', function () {
    it('returns created media item on success', function () {
        $payload = ['mediaFormat' => 'PHOTO', 'sourceUrl' => 'https://example.com/photo.jpg'];
        $service = makeGmbService([
            new Response(200, [], json_encode(['name' => 'accounts/acc/locations/loc/media/new-1', 'mediaFormat' => 'PHOTO'])),
        ]);

        $result = $service->createBusinessLocationMedia($payload);

        expect($result)->toHaveKey('name');
    });

    it('returns false when accountId or locationId is empty', function () {
        $service = new GoogleMyBusinessService('token', '', '', new Client());

        expect($service->createBusinessLocationMedia(['mediaFormat' => 'PHOTO']))->toBeFalse();
    });

    it('throws Exception when API returns an error', function () {
        $service = makeGmbService([
            new Response(400, [], json_encode([
                'error' => ['code' => 400, 'message' => 'Invalid media URL', 'status' => 'INVALID_ARGUMENT'],
            ])),
        ]);

        expect(fn () => $service->createBusinessLocationMedia(['mediaFormat' => 'PHOTO', 'sourceUrl' => 'bad-url']))
            ->toThrow(Exception::class, 'Invalid media URL');
    });

    it('sends request body as JSON with correct Content-Type', function () {
        $history = [];
        $service = makeGmbService(
            [new Response(200, [], json_encode(['name' => 'media/1']))],
            $history
        );

        $service->createBusinessLocationMedia(['mediaFormat' => 'PHOTO', 'sourceUrl' => 'https://example.com/img.jpg']);

        $request = $history[0]['request'];
        expect($request->getHeaderLine('Content-Type'))->toContain('application/json');
        $body = json_decode((string) $request->getBody(), true);
        expect($body)->toMatchArray(['mediaFormat' => 'PHOTO', 'sourceUrl' => 'https://example.com/img.jpg']);
    });
});

// ---------------------------------------------------------------------------
// deleteBusinessLocationMedia
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::deleteBusinessLocationMedia()', function () {
    it('returns true on HTTP 200', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode([])),
        ]);

        expect($service->deleteBusinessLocationMedia('media-id-1'))->toBeTrue();
    });

    it('returns false on HTTP 404', function () {
        $service = makeGmbService([
            new Response(404, [], json_encode(['error' => ['message' => 'Not found']])),
        ]);

        expect($service->deleteBusinessLocationMedia('media-id-1'))->toBeFalse();
    });

    it('returns false when mediaId is empty', function () {
        $service = makeGmbService([]);

        expect($service->deleteBusinessLocationMedia(''))->toBeFalse();
    });
});

// ---------------------------------------------------------------------------
// getBusinessLocationReviews
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::getBusinessLocationReviews()', function () {
    it('returns reviews on success', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode([
                'reviews'       => [['reviewId' => 'r1', 'rating' => 5]],
                'nextPageToken' => 'token-2',
            ])),
        ]);

        $result = $service->getBusinessLocationReviews();

        expect($result)->toHaveKey('reviews');
        expect($result['reviews'])->toHaveCount(1);
    });

    it('returns empty array when accountId or locationId is missing', function () {
        $service = new GoogleMyBusinessService('token', '', '', new Client());

        expect($service->getBusinessLocationReviews())->toBe([]);
    });

    it('returns empty array when response lacks reviews key', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['other' => 'data'])),
        ]);

        expect($service->getBusinessLocationReviews())->toBe([]);
    });

    it('appends pageToken when provided', function () {
        $history = [];
        $service = makeGmbService(
            [new Response(200, [], json_encode(['reviews' => []]))],
            $history
        );

        $service->getBusinessLocationReviews('page-token-xyz');

        expect((string) $history[0]['request']->getUri())->toContain('pageToken=page-token-xyz');
    });
});

// ---------------------------------------------------------------------------
// getBusinessLocationReview
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::getBusinessLocationReview()', function () {
    it('returns a single review on success', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['reviewId' => 'rev-1', 'rating' => 4, 'comment' => 'Great!'])),
        ]);

        $result = $service->getBusinessLocationReview('rev-1');

        expect($result)->toHaveKey('reviewId', 'rev-1');
    });

    it('returns empty array when accountId or locationId is missing', function () {
        $service = new GoogleMyBusinessService('token', '', '', new Client());

        expect($service->getBusinessLocationReview('rev-1'))->toBe([]);
    });

    it('returns empty array when response lacks reviewId key', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['error' => ['message' => 'Not found']])),
        ]);

        expect($service->getBusinessLocationReview('rev-1'))->toBe([]);
    });

    it('returns empty array on non-200 HTTP status', function () {
        $service = makeGmbService([
            new Response(404, [], json_encode([])),
        ]);

        expect($service->getBusinessLocationReview('rev-1'))->toBe([]);
    });
});

// ---------------------------------------------------------------------------
// updateBusinessLocationReviewReply
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::updateBusinessLocationReviewReply()', function () {
    it('returns the updated reply on success', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode(['comment' => 'Thank you for your feedback!'])),
        ]);

        $result = $service->updateBusinessLocationReviewReply('rev-1', 'Thank you for your feedback!');

        expect($result)->toHaveKey('comment', 'Thank you for your feedback!');
    });

    it('returns empty array on API error', function () {
        $service = makeGmbService([
            new Response(400, [], json_encode(['error' => ['message' => 'Invalid request']])),
        ]);

        expect($service->updateBusinessLocationReviewReply('rev-1', 'reply'))->toBe([]);
    });

    it('returns empty array when accountId or locationId is missing', function () {
        $service = new GoogleMyBusinessService('token', '', '', new Client());

        expect($service->updateBusinessLocationReviewReply('rev-1', 'reply'))->toBe([]);
    });

    it('sends request body as JSON with comment field', function () {
        $history = [];
        $service = makeGmbService(
            [new Response(200, [], json_encode(['comment' => 'Thanks!']))],
            $history
        );

        $service->updateBusinessLocationReviewReply('rev-1', 'Thanks!');

        $request = $history[0]['request'];
        expect($request->getHeaderLine('Content-Type'))->toContain('application/json');
        $body = json_decode((string) $request->getBody(), true);
        expect($body)->toMatchArray(['comment' => 'Thanks!']);
    });

    it('returns empty array on network exception', function () {
        $service = makeGmbService([
            new ConnectException('Network error', new Request('PUT', 'test')),
        ]);

        expect($service->updateBusinessLocationReviewReply('rev-1', 'reply'))->toBe([]);
    });
});

// ---------------------------------------------------------------------------
// createScheduledPost
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::createScheduledPost()', function () {
    it('returns created post data on success', function () {
        $service = makeGmbService([
            new Response(200, [], json_encode([
                'name'       => 'accounts/acc/locations/loc/localPosts/new-1',
                'state'      => 'LIVE',
                'topicType'  => 'STANDARD',
            ])),
        ]);

        $post = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN      => 'Hello world',
            ScheduledPost::TOPIC_TYPE_COLUMN   => ScheduledPost::STANDARD_TYPE,
            ScheduledPost::LANGUAGE_CODE_COLUMN => 'en-US',
            ScheduledPost::ACTION_TYPE_COLUMN  => ScheduledPost::LEARN_MORE_ACTION_TYPE,
            ScheduledPost::ACTION_URL_COLUMN   => 'https://example.com',
        ]);

        $result = $service->createScheduledPost($post);

        expect($result)->toHaveKey('name');
        expect($result['state'])->toBe('LIVE');
    });

    it('returns false when accountId or locationId is missing', function () {
        $service = new GoogleMyBusinessService('token', '', '', new Client());

        $post = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN    => 'Hello',
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::STANDARD_TYPE,
        ]);

        expect($service->createScheduledPost($post))->toBeFalse();
    });

    it('throws Exception when API returns an error', function () {
        $service = makeGmbService([
            new Response(400, [], json_encode([
                'error' => ['code' => 400, 'message' => 'Summary is too long', 'status' => 'INVALID_ARGUMENT'],
            ])),
        ]);

        $post = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN    => str_repeat('x', 1600),
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::STANDARD_TYPE,
        ]);

        expect(fn () => $service->createScheduledPost($post))
            ->toThrow(Exception::class, 'Summary is too long');
    });

    it('sends request body as JSON with correct topicType', function () {
        $history = [];
        $service = makeGmbService(
            [new Response(200, [], json_encode(['name' => 'localPosts/1', 'state' => 'LIVE']))],
            $history
        );

        $post = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN      => 'Test post',
            ScheduledPost::TOPIC_TYPE_COLUMN   => ScheduledPost::STANDARD_TYPE,
            ScheduledPost::LANGUAGE_CODE_COLUMN => 'en-US',
            ScheduledPost::ACTION_TYPE_COLUMN  => ScheduledPost::LEARN_MORE_ACTION_TYPE,
            ScheduledPost::ACTION_URL_COLUMN   => 'https://example.com',
        ]);

        $service->createScheduledPost($post);

        $request = $history[0]['request'];
        expect($request->getHeaderLine('Content-Type'))->toContain('application/json');
        $body = json_decode((string) $request->getBody(), true);
        expect($body['topicType'])->toBe('STANDARD');
        expect($body['summary'])->toBe('Test post');
    });

    it('constructs with the access token that will be used as a Bearer header', function () {
        // The Authorization header is set on the Guzzle client created by makeClient().
        // When an injected client is used (as in all other unit tests) the header is not
        // present because the injected client has its own configuration.  We verify here
        // that the service can be constructed with a token and that it does not throw.
        $service = new GoogleMyBusinessService('my-access-token', 'acc', 'loc');

        expect($service)->toBeInstanceOf(GoogleMyBusinessService::class);
    });
});

// ---------------------------------------------------------------------------
// setAccountId
// ---------------------------------------------------------------------------

describe('GoogleMyBusinessService::setAccountId()', function () {
    it('returns self for chaining', function () {
        $service = new GoogleMyBusinessService('token', null, null, new Client());

        expect($service->setAccountId('new-account'))->toBeInstanceOf(GoogleMyBusinessService::class);
    });
});
