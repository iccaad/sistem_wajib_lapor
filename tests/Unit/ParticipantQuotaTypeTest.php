<?php

use App\Http\Requests\Admin\StoreParticipantRequest;
use App\Http\Requests\Admin\UpdateParticipantRequest;

test('StoreParticipantRequest only allows weekly quota type', function () {
    $rules = (new StoreParticipantRequest)->rules();

    expect($rules['quota_type'])->toContain('in:weekly');
    expect($rules['quota_type'])->not->toContain('in:weekly,monthly');
});

test('StoreParticipantRequest error message references only weekly', function () {
    $messages = (new StoreParticipantRequest)->messages();

    expect($messages['quota_type.in'])->toBe('Tipe kuota harus weekly.');
});

test('UpdateParticipantRequest only allows weekly quota type', function () {
    $request = new UpdateParticipantRequest;

    // Mock the route to avoid null participant issue in rules()
    $request->setRouteResolver(fn () => new class
    {
        public function parameter($name)
        {
            return null;
        }
    });

    $rules = $request->rules();

    expect($rules['quota_type'])->toContain('in:weekly');
    expect($rules['quota_type'])->not->toContain('in:weekly,monthly');
});

test('UpdateParticipantRequest error message references only weekly', function () {
    $messages = (new UpdateParticipantRequest)->messages();

    expect($messages['quota_type.in'])->toBe('Tipe kuota harus weekly.');
});
