<?php

namespace Webkul\Support\Filament\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

trait TranslatableCreateRecord
{
    use HasTranslationFallback;
    use Translatable;

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
