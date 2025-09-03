<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament-panels::form
            wire:submit="updateProfile"
            wire:key="profile-form"
        >
            {{ $this->editProfileForm }}

            <x-filament-panels::form.actions
                :actions="$this->getUpdateProfileFormActions()"
                :full-width="false"
            />
        </x-filament-panels::form>

        <x-filament-panels::form
            wire:submit="updatePassword"
            wire:key="password-form"
        >
            {{ $this->editPasswordForm }}

            <x-filament-panels::form.actions
                :actions="$this->getUpdatePasswordFormActions()"
                :full-width="false"
            />
        </x-filament-panels::form>
    </div>
</x-filament-panels::page>
