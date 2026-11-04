<?php

namespace Webkul\Crm\Services\Mail;

use Illuminate\Support\Carbon;
use Throwable;

class ParsedEmail
{
    public function __construct(
        public string $messageId,
        public ?string $inReplyTo,
        public array $references,
        public ?string $fromEmail,
        public ?string $fromName,
        public array $recipients,
        public string $subject,
        public ?string $text,
        public ?string $html,
        public array $attachments,
        public ?Carbon $date,
        public bool $isAutoReply,
    ) {}

    public static function fromRaw(string $raw): self
    {
        [$headerBlock, $body] = static::splitHeadersAndBody($raw);

        $headers = static::parseHeaders($headerBlock);

        $parts = ['text' => null, 'html' => null, 'attachments' => []];

        static::collectParts($headers, $body, $parts);

        [$fromEmail, $fromName] = static::parseAddresses($headers['from'][0] ?? '')[0] ?? [null, null];

        $recipients = collect(['to', 'cc', 'delivered-to', 'x-original-to', 'envelope-to'])
            ->flatMap(fn (string $name): array => $headers[$name] ?? [])
            ->flatMap(fn (string $value): array => array_column(static::parseAddresses($value), 0))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $messageId = static::firstMessageId($headers['message-id'][0] ?? '')
            ?? 'generated-'.sha1($raw).'@crm.local';

        $references = static::allMessageIds(implode(' ', $headers['references'] ?? []));

        try {
            $date = filled($headers['date'][0] ?? null) ? Carbon::parse($headers['date'][0]) : null;
        } catch (Throwable) {
            $date = null;
        }

        $autoSubmitted = mb_strtolower($headers['auto-submitted'][0] ?? 'no');
        $precedence = mb_strtolower($headers['precedence'][0] ?? '');

        return new self(
            messageId: $messageId,
            inReplyTo: static::firstMessageId($headers['in-reply-to'][0] ?? ''),
            references: $references,
            fromEmail: $fromEmail,
            fromName: $fromName,
            recipients: $recipients,
            subject: trim(static::decodeHeader($headers['subject'][0] ?? '')),
            text: $parts['text'],
            html: $parts['html'],
            attachments: $parts['attachments'],
            date: $date,
            isAutoReply: ($autoSubmitted !== '' && $autoSubmitted !== 'no')
                || in_array($precedence, ['bulk', 'junk', 'auto_reply'], true)
                || isset($headers['x-autoreply']) || isset($headers['x-autorespond']),
        );
    }

    public function threadMessageIds(): array
    {
        return array_values(array_unique(array_filter([$this->inReplyTo, ...array_reverse($this->references)])));
    }

    protected static function splitHeadersAndBody(string $raw): array
    {
        $raw = str_replace("\r\n", "\n", $raw);

        $position = strpos($raw, "\n\n");

        return $position === false ? [$raw, ''] : [substr($raw, 0, $position), substr($raw, $position + 2)];
    }

    protected static function parseHeaders(string $block): array
    {
        $headers = [];

        $block = preg_replace("/\n[ \t]+/", ' ', $block);

        foreach (explode("\n", $block) as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$name, $value] = explode(':', $line, 2);

            $headers[mb_strtolower(trim($name))][] = trim($value);
        }

        return $headers;
    }

    protected static function collectParts(array $headers, string $body, array &$parts, int $depth = 0): void
    {
        [$type, $params] = static::parseHeaderParams($headers['content-type'][0] ?? 'text/plain');
        [$disposition, $dispositionParams] = static::parseHeaderParams($headers['content-disposition'][0] ?? '');

        if (str_starts_with($type, 'multipart/')
            && filled($params['boundary'] ?? null)
            && $depth < 10) {
            foreach (static::splitMultipart($body, $params['boundary']) as $part) {
                [$partHeaderBlock, $partBody] = static::splitHeadersAndBody($part);

                static::collectParts(static::parseHeaders($partHeaderBlock), $partBody, $parts, $depth + 1);
            }

            return;
        }

        $content = static::decodeBody($body, mb_strtolower($headers['content-transfer-encoding'][0] ?? '7bit'));

        $filename = $dispositionParams['filename'] ?? $params['name'] ?? null;

        $isAttachment = $disposition === 'attachment'
            || filled($filename)
            || ! in_array($type, ['text/plain', 'text/html'], true);

        if ($type === 'message/rfc822') {
            $isAttachment = true;
            $filename ??= 'message.eml';
        }

        if ($isAttachment) {
            if ($content !== '') {
                $parts['attachments'][] = [
                    'name'    => static::safeFileName(static::decodeHeader((string) $filename) ?: 'attachment'),
                    'mime'    => $type,
                    'content' => $content,
                ];
            }

            return;
        }

        $content = static::toUtf8($content, $params['charset'] ?? null);

        if ($type === 'text/html') {
            $parts['html'] ??= $content;
        } else {
            $parts['text'] ??= $content;
        }
    }

    protected static function splitMultipart(string $body, string $boundary): array
    {
        $sections = preg_split('/^--'.preg_quote($boundary, '/').'(?:--)?[ \t]*$/m', $body);

        if (! is_array($sections)
            || count($sections) < 3) {
            return [];
        }

        return array_map(fn (string $section): string => ltrim($section, "\n"), array_slice($sections, 1, -1));
    }

    protected static function parseHeaderParams(string $value): array
    {
        $segments = preg_split('/;(?=(?:[^"]*"[^"]*")*[^"]*$)/', $value) ?: [];

        $main = mb_strtolower(trim((string) array_shift($segments)));

        $params = [];

        foreach ($segments as $segment) {
            if (! str_contains($segment, '=')) {
                continue;
            }

            [$name, $paramValue] = explode('=', $segment, 2);

            $name = mb_strtolower(trim($name));
            $paramValue = trim(trim($paramValue), '"');

            if (! str_contains($name, '*')) {
                $params[$name] = $paramValue;

                continue;
            }

            $isEncoded = str_ends_with($name, '*');
            $name = (string) preg_replace('/\*\d*\*?$/', '', $name);

            if ($isEncoded
                && preg_match("/^([^']*)'[^']*'(.*)$/s", $paramValue, $encoded)) {
                $paramValue = static::toUtf8(rawurldecode($encoded[2]), $encoded[1] ?: null);
            } elseif ($isEncoded) {
                $paramValue = rawurldecode($paramValue);
            }

            $params[$name] = ($params[$name] ?? '').$paramValue;
        }

        return [$main, $params];
    }

    protected static function decodeBody(string $body, string $encoding): string
    {
        return match (trim($encoding)) {
            'base64'           => (string) base64_decode(preg_replace('/\s+/', '', $body), false),
            'quoted-printable' => quoted_printable_decode($body),
            default            => $body,
        };
    }

    protected static function toUtf8(string $content, ?string $charset): string
    {
        $charset = mb_strtoupper(trim((string) $charset, " \t\"'"));

        if ($charset === ''
            || $charset === 'UTF-8'
            || $charset === 'US-ASCII') {
            return mb_check_encoding($content, 'UTF-8') ? $content : mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
        }

        $converted = @iconv($charset, 'UTF-8//IGNORE', $content);

        return $converted === false ? mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1') : $converted;
    }

    protected static function decodeHeader(string $value): string
    {
        if (! str_contains($value, '=?')) {
            return $value;
        }

        $decoded = @iconv_mime_decode($value, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');

        return $decoded === false ? mb_decode_mimeheader($value) : $decoded;
    }

    protected static function parseAddresses(string $value): array
    {
        $value = static::decodeHeader($value);

        $addresses = [];

        foreach (preg_split('/,(?=(?:[^"]*"[^"]*")*[^"]*$)/', $value) ?: [] as $entry) {
            $entry = trim($entry);

            if (preg_match('/^(.*)<([^>]+)>$/s', $entry, $matches)) {
                $email = trim($matches[2]);
                $name = trim(trim($matches[1]), '"\' ');
            } else {
                $email = $entry;
                $name = '';
            }

            $email = mb_strtolower($email);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $addresses[] = [$email, $name !== '' ? $name : null];
            }
        }

        return $addresses;
    }

    protected static function firstMessageId(string $value): ?string
    {
        return static::allMessageIds($value)[0] ?? null;
    }

    protected static function allMessageIds(string $value): array
    {
        preg_match_all('/<([^<>\s]+)>/', $value, $matches);

        $ids = $matches[1];

        if (! $ids
            && filled(trim($value))
            && ! str_contains(trim($value), ' ')) {
            $ids = [trim($value)];
        }

        return array_values(array_unique(array_map(fn (string $id): string => mb_substr($id, 0, 255), $ids)));
    }

    protected static function safeFileName(string $name): string
    {
        $name = preg_replace('/[\x00-\x1F\x7F\/\\\\:*?"<>|]+/u', '_', basename(str_replace('\\', '/', $name)));

        $name = trim((string) $name, ' .');

        return mb_substr($name !== '' ? $name : 'attachment', 0, 150);
    }
}
