@php
    $direction = in_array(app()->getLocale(), $rtlLocales) ? 'rtl' : 'ltr';
    $locale = app()->getLocale();
@endphp

<script>
    document.documentElement.dir = '{{ $direction }}';
    document.documentElement.lang = '{{ $locale }}';
</script>
