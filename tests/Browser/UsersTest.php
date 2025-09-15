<?php

use Illuminate\Support\Facades\Mail;
use Webkul\Security\Filament\Resources\UserResource;



it('invites a new user', function() {
    Mail::fake();
    $user = $this->makeAdminUser();
    $this->actingAs($user);

    visit(UserResource::getUrl('index'))
        ->click('Invite User')
        ->type('[data-test="pest-invite-user-email"] input', 'nuno@laravel.com')
        ->click('form:has([data-test="pest-invite-user-email"]) button[type="submit"]')
        ->assertSee('User has been invited successfully');

    Mail::assertSentCount(1);
});

it('may sign in as admin', function(){
    $user = $this->makeAdminUser();
    $this->actingAs($user);
    visit('/admin')->assertSee('Dashboard');
});