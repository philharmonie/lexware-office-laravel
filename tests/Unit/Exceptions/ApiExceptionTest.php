<?php

declare(strict_types=1);

use PhilHarmonie\LexOffice\Exceptions\ApiException;

test('api exception creates with message and status code', function () {
    $exception = new ApiException('Test error', 400);

    expect($exception->getMessage())->toBe('Test error')
        ->and($exception->getStatusCode())->toBe(400)
        ->and($exception->getResponse())->toBe([]);
});

test('api exception creates with response data', function () {
    $responseData = ['error' => 'Invalid request', 'code' => 'INVALID'];
    $exception = new ApiException('Test error', 422, $responseData);

    expect($exception->getMessage())->toBe('Test error')
        ->and($exception->getStatusCode())->toBe(422)
        ->and($exception->getResponse())->toBe($responseData);
});

test('api exception creates with previous exception', function () {
    $previousException = new Exception('Previous error');
    $exception = new ApiException('Test error', 500, [], $previousException);

    expect($exception->getMessage())->toBe('Test error')
        ->and($exception->getStatusCode())->toBe(500)
        ->and($exception->getResponse())->toBe([])
        ->and($exception->getPrevious())->toBe($previousException);
});
