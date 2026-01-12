<x-support::emails.layout>
    <div class="breadcrumb">
        <a
            href="{{ $payload['record_url'] }}"
            class="view-button"
        >
            {{ __('chatter::views/mail/follower-mail.view', ['model_name' => $payload['model_name']]) }}
        </a>

        <p>{{ $payload['record_name'] }}</p>
    </div>

    <div class="notification">
        @isset($payload['content'])
            <p>{!! str($payload['content'])->sanitizeHtml() !!}</p>
        @endisset

        <p>{{ $payload['from']['name'] }}</p>
    </div>

    @isset($payload['from']['company'])
        <div class="company-info">
            <div class="company-name">{{ $payload['from']['company']['name'] }}</div>
            <p class="company-details">
                {{ $payload['from']['company']['phone'] }} | {{ $payload['from']['company']['email'] }} | <a href="{{ $payload['from']['company']['website'] }}">{{ str_replace(['https://', 'http://'], '', $payload['from']['company']['website']) }}</a>
            </p>
        </div>
    @endisset
</x-support::emails.layout>

<style>
    .breadcrumb {
        font-size: 14px;
        margin-bottom: 20px;
        border-bottom: 1px solid rgb(204, 204, 204);
        padding-bottom: 10px;
        display: flex;
        align-items: center;
    }

    .breadcrumb p {
        font-weight: bold;
        margin: 0;
        margin-left: 10px;
    }

    .view-button {
        display: inline-block;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: bold;
        color: #fff;
        background-color: #007bff;
        border-radius: 3px;
        text-decoration: none;
    }

    .view-button:hover {
        background-color: #0056b3;
    }

    .notification {
        margin: 15px 0;
        color: #555;
        font-size: 13px;
    }

    .company-info {
        font-size: 13px;
        color: #666;
        border-top: 1px solid rgb(204, 204, 204);
        padding-top: 10px;
    }

    .company-name {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .company-details {
        margin: 0;
    }
</style>
