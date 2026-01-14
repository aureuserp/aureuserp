<x-filament-panels::page>
    <div class="space-y-6">
        <form
            wire:submit="updateProfile"
            wire:key="profile-form"
            x-data="{ isProcessing: false }"
            x-on:submit="if (isProcessing) $event.preventDefault()"
            x-on:form-processing-started="isProcessing = true"
            x-on:form-processing-finished="isProcessing = false"
            class="fi-form grid gap-y-6"
        >
            <div class="flex flex-col gap-6">
                {{ $this->editProfileForm }}

                <x-filament::actions
                    :actions="$this->getUpdateProfileFormActions()"
                    :full-width="false"
                />
            </div>
        </form>

        <form
            id="form"
            wire:submit="updatePassword"
            wire:key="password-form"
            x-data="{ isProcessing: false }"
            x-on:submit="if (isProcessing) $event.preventDefault()"
            x-on:form-processing-started="isProcessing = true"
            x-on:form-processing-finished="isProcessing = false"
            class="fi-form grid gap-y-6"
        >
            <div class="flex flex-col gap-6">
                {{ $this->editPasswordForm }}

                <x-filament::actions
                    :actions="$this->getUpdatePasswordFormActions()"
                    :full-width="false"
                />
            </div>
        </form>
    </div>
</x-filament-panels::page>
