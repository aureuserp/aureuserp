<div>
    {!! str($body)->sanitizeHtml() !!}

    @if (filled($senderName))
        <p>{{ $senderName }}</p>
    @endif
</div>
