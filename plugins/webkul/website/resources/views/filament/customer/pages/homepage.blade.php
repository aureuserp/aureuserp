<x-filament-panels::page class="fi-home-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="{{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}" style="font-family: {{ app()->getLocale() === 'ar' ? 'Cairo, sans-serif' : 'inherit' }};">
        {!! str($this->getContent())->sanitizeHtml() !!}
    </div>
</x-filament-panels::page>
