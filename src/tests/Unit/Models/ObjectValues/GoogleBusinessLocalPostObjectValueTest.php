<?php

use Carbon\Carbon;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleBusinessLocalPostObjectValue;

// ---------------------------------------------------------------------------
// toArray() – null / empty filtering
// ---------------------------------------------------------------------------

describe('GoogleBusinessLocalPostObjectValue::toArray()', function () {
    it('omits null and empty-array fields from the output', function () {
        $value = new GoogleBusinessLocalPostObjectValue(summary: 'Hello');

        $array = $value->toArray();

        // empty arrays (event, offer, media) and null (alertType) must be absent
        expect($array)->not->toHaveKey('event');
        expect($array)->not->toHaveKey('offer');
        expect($array)->not->toHaveKey('media');
        expect($array)->not->toHaveKey('alertType');
    });

    it('includes callToAction when provided', function () {
        $value = new GoogleBusinessLocalPostObjectValue(
            summary: 'Hello',
            callToAction: ['actionType' => 'LEARN_MORE', 'url' => 'https://example.com']
        );

        expect($value->toArray())->toHaveKey('callToAction');
    });
});

// ---------------------------------------------------------------------------
// fromArray() – Standard post
// ---------------------------------------------------------------------------

describe('GoogleBusinessLocalPostObjectValue::fromArray() – STANDARD post', function () {
    it('maps lowercase topicType to the GMB API uppercase enum', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN    => ScheduledPost::STANDARD_TYPE,  // 'standard'
            ScheduledPost::SUMMARY_COLUMN       => 'My post',
            ScheduledPost::ACTION_TYPE_COLUMN   => ScheduledPost::LEARN_MORE_ACTION_TYPE,
            ScheduledPost::ACTION_URL_COLUMN    => 'https://example.com',
            ScheduledPost::LANGUAGE_CODE_COLUMN => 'en-US',
        ]);

        $array = $value->toArray();

        expect($array['topicType'])->toBe('STANDARD');
    });

    it('defaults topicType to STANDARD when not provided', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN => 'Hello',
        ]);

        expect($value->toArray()['topicType'])->toBe('STANDARD');
    });

    it('maps lowercase actionType to the GMB API uppercase enum', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN  => ScheduledPost::STANDARD_TYPE,
            ScheduledPost::SUMMARY_COLUMN     => 'Shop now',
            ScheduledPost::ACTION_TYPE_COLUMN => ScheduledPost::SHOP_ACTION_TYPE,  // 'shop'
            ScheduledPost::ACTION_URL_COLUMN  => 'https://shop.example.com',
        ]);

        expect($value->toArray()['callToAction']['actionType'])->toBe('SHOP');
    });

    it('defaults actionType to LEARN_MORE when not supplied', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN => 'Hello',
        ]);

        expect($value->toArray()['callToAction']['actionType'])->toBe('LEARN_MORE');
    });

    it('does not include url in callToAction for CALL action type', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN  => ScheduledPost::STANDARD_TYPE,
            ScheduledPost::SUMMARY_COLUMN     => 'Call us',
            ScheduledPost::ACTION_TYPE_COLUMN => ScheduledPost::CALL_ACTION_TYPE,  // 'call'
        ]);

        $callToAction = $value->toArray()['callToAction'];

        expect($callToAction['actionType'])->toBe('CALL');
        expect($callToAction)->not->toHaveKey('url');
    });

    it('includes url in callToAction for non-CALL action types', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN  => ScheduledPost::STANDARD_TYPE,
            ScheduledPost::SUMMARY_COLUMN     => 'Book now',
            ScheduledPost::ACTION_TYPE_COLUMN => ScheduledPost::BOOK_ACTION_TYPE,
            ScheduledPost::ACTION_URL_COLUMN  => 'https://booking.example.com',
        ]);

        $callToAction = $value->toArray()['callToAction'];

        expect($callToAction['actionType'])->toBe('BOOK');
        expect($callToAction)->toHaveKey('url', 'https://booking.example.com');
    });

    it('sets the languageCode from attributes', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN       => 'Bonjour',
            ScheduledPost::LANGUAGE_CODE_COLUMN => 'fr-FR',
        ]);

        expect($value->toArray()['languageCode'])->toBe('fr-FR');
    });

    it('defaults languageCode to en-US when not provided', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN => 'Hello',
        ]);

        expect($value->toArray()['languageCode'])->toBe('en-US');
    });
});

// ---------------------------------------------------------------------------
// fromArray() – EVENT post
// ---------------------------------------------------------------------------

describe('GoogleBusinessLocalPostObjectValue::fromArray() – EVENT post', function () {
    it('maps topicType event to GMB EVENT enum', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN         => ScheduledPost::EVENT_TYPE,
            ScheduledPost::SUMMARY_COLUMN            => 'Grand opening',
            ScheduledPost::EVENT_TITLE_COLUMN        => 'Grand Opening',
            ScheduledPost::EVENT_START_DATETIME_COLUMN => '2024-12-25 10:00:00',
            ScheduledPost::EVENT_END_DATETIME_COLUMN   => '2024-12-25 18:00:00',
        ]);

        expect($value->toArray()['topicType'])->toBe('EVENT');
    });

    it('builds the correct event schedule structure', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN           => ScheduledPost::EVENT_TYPE,
            ScheduledPost::SUMMARY_COLUMN              => 'Grand opening',
            ScheduledPost::EVENT_TITLE_COLUMN          => 'Grand Opening',
            ScheduledPost::EVENT_START_DATETIME_COLUMN => '2024-12-25 10:30:00',
            ScheduledPost::EVENT_END_DATETIME_COLUMN   => '2024-12-26 18:00:00',
        ]);

        $event = $value->toArray()['event'];

        expect($event['title'])->toBe('Grand Opening');
        expect($event['schedule']['startDate'])->toMatchArray(['year' => 2024, 'month' => 12, 'day' => 25]);
        expect($event['schedule']['startTime'])->toMatchArray(['hours' => 10, 'minutes' => 30, 'seconds' => 0, 'nanos' => 0]);
        expect($event['schedule']['endDate'])->toMatchArray(['year' => 2024, 'month' => 12, 'day' => 26]);
        expect($event['schedule']['endTime'])->toMatchArray(['hours' => 18, 'minutes' => 0, 'seconds' => 0, 'nanos' => 0]);
    });

    it('omits event when start or end datetime is missing', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::EVENT_TYPE,
            ScheduledPost::SUMMARY_COLUMN    => 'Event without dates',
        ]);

        expect($value->toArray())->not->toHaveKey('event');
    });
});

// ---------------------------------------------------------------------------
// fromArray() – OFFER post
// ---------------------------------------------------------------------------

describe('GoogleBusinessLocalPostObjectValue::fromArray() – OFFER post', function () {
    it('maps topicType offer to GMB OFFER enum', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN              => ScheduledPost::OFFER_TYPE,
            ScheduledPost::SUMMARY_COLUMN                 => '20% off everything',
            ScheduledPost::OFFER_COUPON_CODE_COLUMN        => 'SAVE20',
            ScheduledPost::OFFER_REDEEM_ONLINE_URL_COLUMN  => 'https://shop.example.com/redeem',
            ScheduledPost::OFFER_TERMS_CONDITIONS_COLUMN   => 'Valid until Dec 31',
        ]);

        expect($value->toArray()['topicType'])->toBe('OFFER');
    });

    it('builds the correct offer structure', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN              => ScheduledPost::OFFER_TYPE,
            ScheduledPost::SUMMARY_COLUMN                 => '20% off',
            ScheduledPost::OFFER_COUPON_CODE_COLUMN        => 'SAVE20',
            ScheduledPost::OFFER_REDEEM_ONLINE_URL_COLUMN  => 'https://shop.example.com/redeem',
            ScheduledPost::OFFER_TERMS_CONDITIONS_COLUMN   => 'Valid until Dec 31',
        ]);

        $offer = $value->toArray()['offer'];

        expect($offer['couponCode'])->toBe('SAVE20');
        expect($offer['redeemOnlineUrl'])->toBe('https://shop.example.com/redeem');
        expect($offer['termsConditions'])->toBe('Valid until Dec 31');
    });

    it('does not include event or alertType fields in offer posts', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::OFFER_TYPE,
            ScheduledPost::SUMMARY_COLUMN    => 'Offer',
        ]);

        $array = $value->toArray();

        expect($array)->not->toHaveKey('event');
        expect($array)->not->toHaveKey('alertType');
    });
});

// ---------------------------------------------------------------------------
// fromArray() – ALERT post
// ---------------------------------------------------------------------------

describe('GoogleBusinessLocalPostObjectValue::fromArray() – ALERT post', function () {
    it('maps topicType alert to GMB ALERT enum', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::ALERT_TYPE,
            ScheduledPost::SUMMARY_COLUMN    => 'Important notice',
            ScheduledPost::ALERT_TYPE_COLUMN => ScheduledPost::UNSPECIFIED_ALERT_TYPE,
        ]);

        expect($value->toArray()['topicType'])->toBe('ALERT');
    });

    it('maps covid_19 alertType to GMB COVID_19 enum', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::ALERT_TYPE,
            ScheduledPost::SUMMARY_COLUMN    => 'COVID update',
            ScheduledPost::ALERT_TYPE_COLUMN => ScheduledPost::COVID_19_ALERT_TYPE,  // 'covid_19'
        ]);

        expect($value->toArray()['alertType'])->toBe('COVID_19');
    });

    it('maps unspecified alertType to ALERT_TYPE_UNSPECIFIED', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::ALERT_TYPE,
            ScheduledPost::SUMMARY_COLUMN    => 'Alert',
            ScheduledPost::ALERT_TYPE_COLUMN => ScheduledPost::UNSPECIFIED_ALERT_TYPE,
        ]);

        expect($value->toArray()['alertType'])->toBe('ALERT_TYPE_UNSPECIFIED');
    });

    it('defaults alertType to ALERT_TYPE_UNSPECIFIED when not provided', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::ALERT_TYPE,
            ScheduledPost::SUMMARY_COLUMN    => 'Alert without type',
        ]);

        expect($value->toArray()['alertType'])->toBe('ALERT_TYPE_UNSPECIFIED');
    });

    it('does not set alertType for non-ALERT post types', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::STANDARD_TYPE,
            ScheduledPost::SUMMARY_COLUMN    => 'Standard post',
        ]);

        expect($value->toArray())->not->toHaveKey('alertType');
    });
});

// ---------------------------------------------------------------------------
// fromArray() – media
// ---------------------------------------------------------------------------

describe('GoogleBusinessLocalPostObjectValue::fromArray() – media', function () {
    it('includes media array in output when provided', function () {
        $media = [
            ['mediaFormat' => 'PHOTO', 'sourceUrl' => 'https://example.com/photo.jpg'],
        ];

        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN    => 'Post with media',
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::STANDARD_TYPE,
            'media'                          => $media,
        ]);

        expect($value->toArray()['media'])->toBe($media);
    });

    it('omits media when not provided', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::SUMMARY_COLUMN    => 'Post without media',
            ScheduledPost::TOPIC_TYPE_COLUMN => ScheduledPost::STANDARD_TYPE,
        ]);

        expect($value->toArray())->not->toHaveKey('media');
    });
});

// ---------------------------------------------------------------------------
// fromArray() – normalization: controller stores uppercase API values in DB
//
// SaveScheduledPostController persists ScheduledPost::TYPES[$type] which
// produces uppercase strings ('STANDARD', 'EVENT', 'OFFER', 'ALERT') and
// similarly stores 'LEARN_MORE', 'CALL', 'COVID_19', etc.
// fromArray() must produce correct GMB payloads from these values.
// ---------------------------------------------------------------------------

describe('GoogleBusinessLocalPostObjectValue::fromArray() – uppercase DB values (from controller)', function () {
    it('correctly maps uppercase STANDARD topicType stored by the controller', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN   => 'STANDARD',   // as stored by controller
            ScheduledPost::SUMMARY_COLUMN      => 'Hello',
            ScheduledPost::ACTION_TYPE_COLUMN  => 'LEARN_MORE', // as stored by controller
            ScheduledPost::ACTION_URL_COLUMN   => 'https://example.com',
            ScheduledPost::LANGUAGE_CODE_COLUMN => 'en-US',
        ]);

        $array = $value->toArray();

        expect($array['topicType'])->toBe('STANDARD');
        expect($array['callToAction']['actionType'])->toBe('LEARN_MORE');
    });

    it('correctly maps uppercase EVENT topicType and builds event schedule', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN           => 'EVENT',
            ScheduledPost::SUMMARY_COLUMN              => 'Grand opening',
            ScheduledPost::EVENT_TITLE_COLUMN          => 'Grand Opening',
            ScheduledPost::ACTION_TYPE_COLUMN          => 'LEARN_MORE',
            ScheduledPost::ACTION_URL_COLUMN           => 'https://example.com',
            ScheduledPost::EVENT_START_DATETIME_COLUMN => '2025-12-25 10:00:00',
            ScheduledPost::EVENT_END_DATETIME_COLUMN   => '2025-12-25 18:00:00',
        ]);

        $array = $value->toArray();

        expect($array['topicType'])->toBe('EVENT');
        expect($array)->toHaveKey('event');
        expect($array['event']['title'])->toBe('Grand Opening');
    });

    it('correctly maps uppercase OFFER topicType and builds offer structure', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN              => 'OFFER',
            ScheduledPost::SUMMARY_COLUMN                 => '20% off',
            ScheduledPost::ACTION_TYPE_COLUMN             => 'GET_OFFER',
            ScheduledPost::ACTION_URL_COLUMN              => 'https://shop.example.com',
            ScheduledPost::OFFER_COUPON_CODE_COLUMN        => 'SAVE20',
            ScheduledPost::OFFER_REDEEM_ONLINE_URL_COLUMN  => 'https://redeem.example.com',
            ScheduledPost::OFFER_TERMS_CONDITIONS_COLUMN   => 'Valid until Dec 31',
        ]);

        $array = $value->toArray();

        expect($array['topicType'])->toBe('OFFER');
        expect($array)->toHaveKey('offer');
        expect($array['offer']['couponCode'])->toBe('SAVE20');
        expect($array['callToAction']['actionType'])->toBe('GET_OFFER');
    });

    it('correctly maps uppercase ALERT topicType with COVID_19 alertType', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN => 'ALERT',
            ScheduledPost::SUMMARY_COLUMN    => 'COVID update',
            ScheduledPost::ALERT_TYPE_COLUMN => 'COVID_19', // as stored by controller
            ScheduledPost::ACTION_TYPE_COLUMN => 'LEARN_MORE',
            ScheduledPost::ACTION_URL_COLUMN  => 'https://example.com',
        ]);

        $array = $value->toArray();

        expect($array['topicType'])->toBe('ALERT');
        expect($array['alertType'])->toBe('COVID_19');
    });

    it('correctly maps uppercase ALERT topicType with ALERT_TYPE_UNSPECIFIED', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN => 'ALERT',
            ScheduledPost::SUMMARY_COLUMN    => 'General alert',
            ScheduledPost::ALERT_TYPE_COLUMN => 'ALERT_TYPE_UNSPECIFIED', // as stored by controller
            ScheduledPost::ACTION_TYPE_COLUMN => 'LEARN_MORE',
            ScheduledPost::ACTION_URL_COLUMN  => 'https://example.com',
        ]);

        expect($value->toArray()['alertType'])->toBe('ALERT_TYPE_UNSPECIFIED');
    });

    it('does not add url to callToAction when CALL actionType is stored by the controller', function () {
        $value = GoogleBusinessLocalPostObjectValue::fromArray([
            ScheduledPost::TOPIC_TYPE_COLUMN  => 'STANDARD',
            ScheduledPost::SUMMARY_COLUMN     => 'Call us',
            ScheduledPost::ACTION_TYPE_COLUMN => 'CALL', // uppercase, as stored by controller
        ]);

        $callToAction = $value->toArray()['callToAction'];

        expect($callToAction['actionType'])->toBe('CALL');
        expect($callToAction)->not->toHaveKey('url');
    });
});
