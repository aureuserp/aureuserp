<?php

namespace Webkul\Support\Filament\Concerns;

use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;
use Webkul\Support\Filament\TranslatableContentDriver;

trait TranslatableViewRecord
{
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
                $translatedData[$attribute] = $record->getTranslation($attribute, $locale, useFallbackLocale: true);
            }

            if ($locale !== $this->activeLocale) {
                $this->otherLocaleData[$locale] = $this->mutateFormDataBeforeFill($translatedData);

                continue;
            }

            $this->fillFormWithDataAndCallHooks($record, $translatedData);
        }
    }
}
