<x-mail::message>
    @lang('security::views/emails/user-invitation.body', ['app' => config('app.name')])

    <x-mail::button :url="$acceptUrl">
        @lang('security::views/emails/user-invitation.create-account')
    </x-mail::button>

    @lang('security::views/emails/user-invitation.discard-email')
</x-mail::message>
