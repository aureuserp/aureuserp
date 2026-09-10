<?php

namespace Webkul\Support\Filament\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use Webkul\Support\Filament\TranslatableContentDriver;

trait TranslatableEditRecord
{
    use HasTranslationFallback;
    use Translatable;

    public function getFilamentTranslatableContentDriver(): ?string
    {
        return TranslatableContentDriver::class;
    }

    protected function fillForm(): void
    {
        $this->activeLocale ??= $this->getStoredActiveLocale() ?? $this->getDefaultTranslatableLocale();

        $record = $this->getRecord();

        $translatableAttributes = static::getResource()::getTranslatableAttributes();

        foreach ($this->getTranslatableLocales() as $locale) {
            $translatedData = [];

            foreach ($translatableAttributes as $attribute) {
                $translatedData[$attribute] = $record->getTranslation(
                    $attribute,
                    $locale,
                    useFallbackLocale: $locale === $this->activeLocale
                );
            }

            if ($locale !== $this->activeLocale) {
                $this->otherLocaleData[$locale] = $this->mutateFormDataBeforeFill($translatedData);

                continue;
            }

            $this->fillFormWithDataAndCallHooks($record, $translatedData);
        }
    }

    public function updatedActiveLocale(): void
    {
        if (filament('spatie-translatable')->getPersistLocale()) {
            session()->put('spatie_translatable_active_locale', $this->activeLocale);
        }

        if (blank($this->oldActiveLocale)) {
            return;
        }

        $this->resetValidation();

        $translatableAttributes = static::getResource()::getTranslatableAttributes();

        try {
            $this->otherLocaleData[$this->oldActiveLocale] = Arr::only(
                $this->form->getState(),
                $translatableAttributes
            );

            $this->form->fill([
                ...Arr::except(
                    $this->form->getState(),
                    $translatableAttributes
                ),
                ...$this->applyTranslationFallback(
                    $this->otherLocaleData[$this->activeLocale] ?? [],
                    $translatableAttributes
                ),
            ]);

            unset($this->otherLocaleData[$this->activeLocale]);
        } catch (ValidationException $e) {
            $this->activeLocale = $this->oldActiveLocale;

            throw $e;
        }
    }
}
