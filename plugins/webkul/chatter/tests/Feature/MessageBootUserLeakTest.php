<?php

use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Models\Message;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();
});

it('assigns causer from the user active at creation time, not the user active at boot time', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    Message::flushEventListeners();
    Message::clearBootedModels();

    Auth::login($userA);
    new Message;

    Auth::login($userB);

    $message = Message::create([
        'type'             => 'comment',
        'name'             => 'Test',
        'body'             => 'body',
        'messageable_id'   => $userA->id,
        'messageable_type' => $userA->getMorphClass(),
    ]);

    expect($message->causer_id)->toBe($userB->id);
});
