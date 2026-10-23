<?php

use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\InboundEmailResource\Pages\ManageInboundEmails;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\MailServerResource\Pages\ManageMailServers;
use Webkul\Crm\Models\InboundEmail;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\MailAlias;
use Webkul\Crm\Models\MailServer;
use Webkul\Crm\Services\Mail\ImapMailbox;
use Webkul\Crm\Services\Mail\InboundEmailProcessor;
use Webkul\Crm\Services\Mail\InboundMailFetcher;
use Webkul\Crm\Services\Mail\ParsedEmail;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\Sale\Models\Team;

require_once __DIR__.'/../../Helpers/CrmTestHelper.php';

beforeEach(function () {
    CrmTestHelper::bootstrap();

    Storage::fake('public');

    $settings = app(CrmSettings::class);
    $settings->use_leads = true;
    $settings->save();
});

function inboundAliasTeam(string $contact = MailAlias::CONTACT_EVERYONE, ?string $domain = 'example.com'): Team
{
    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId(), 'is_active' => true]);

    MailAlias::query()->create(['team_id' => $team->id, 'alias_name' => 'sales', 'alias_domain' => $domain, 'alias_contact' => $contact]);

    return $team;
}

/**
 * @param  array<string, string>  $headers
 */
function inboundRawEmail(array $headers, string $body = 'Hello'): string
{
    $headers = [
        'From'       => 'Jane Buyer <jane@customer.test>',
        'To'         => 'sales@example.com',
        'Subject'    => 'Need 50 chairs',
        'Message-ID' => '<'.uniqid('msg', true).'@customer.test>',
        ...$headers,
    ];

    return collect($headers)->map(fn (string $value, string $name): string => "{$name}: {$value}")->implode("\r\n")."\r\n\r\n".$body;
}

describe('parsing', function () {
    it('reads encoded headers, html and text bodies and attachments', function () {
        $raw = implode("\r\n", [
            'From: =?UTF-8?B?SsO8cmdlbiBNw7xsbGVy?= <Juergen@Customer.test>',
            'To: "Sales" <sales@example.com>',
            'Cc: other@example.com',
            'Subject: =?UTF-8?Q?Need_50_chairs_=E2=80=93_quote?=',
            'Message-ID: <first-1@customer.test>',
            'MIME-Version: 1.0',
            'Content-Type: multipart/mixed; boundary="b1"',
            '',
            '--b1',
            'Content-Type: multipart/alternative; boundary="b2"',
            '',
            '--b2',
            'Content-Type: text/plain; charset=utf-8',
            '',
            'Hello, we need 50 chairs.',
            '--b2',
            'Content-Type: text/html; charset=utf-8',
            'Content-Transfer-Encoding: quoted-printable',
            '',
            '<p>Hello, we need <b>50 chairs</b>.</p>',
            '--b2--',
            '--b1',
            'Content-Type: application/pdf; name="spec.pdf"',
            "Content-Disposition: attachment; filename*=UTF-8''Sp%C3%A9c%20sheet.pdf",
            'Content-Transfer-Encoding: base64',
            '',
            base64_encode('%PDF-1.4 fake'),
            '--b1--',
            '',
        ]);

        $email = ParsedEmail::fromRaw($raw);

        expect($email->messageId)->toBe('first-1@customer.test')
            ->and($email->fromEmail)->toBe('juergen@customer.test')
            ->and($email->fromName)->toBe('Jürgen Müller')
            ->and($email->subject)->toBe('Need 50 chairs – quote')
            ->and($email->recipients)->toBe(['sales@example.com', 'other@example.com'])
            ->and(trim($email->text))->toBe('Hello, we need 50 chairs.')
            ->and($email->html)->toContain('<b>50 chairs</b>')
            ->and($email->attachments)->toHaveCount(1)
            ->and($email->attachments[0]['name'])->toBe('Spéc sheet.pdf')
            ->and($email->attachments[0]['content'])->toBe('%PDF-1.4 fake')
            ->and($email->isAutoReply)->toBeFalse();
    });

    it('detects automatic replies and reads reply headers', function () {
        $email = ParsedEmail::fromRaw(inboundRawEmail([
            'Auto-Submitted' => 'auto-replied',
            'In-Reply-To'    => '<a@x.test>',
            'References'     => '<root@x.test> <a@x.test>',
        ]));

        expect($email->isAutoReply)->toBeTrue()
            ->and($email->threadMessageIds())->toBe(['a@x.test', 'root@x.test']);
    });
});

describe('processing', function () {
    it('creates a lead in the alias team from an email', function () {
        $team = inboundAliasTeam();

        $result = app(InboundEmailProcessor::class)->process(inboundRawEmail([
            'Content-Type' => 'text/html; charset=utf-8',
        ], '<html><head><style>p{}</style></head><body><p>Hello <b>there</b></p><script>alert(1)</script></body></html>'));

        $lead = Lead::query()->withoutGlobalScopes()->find($result->lead_id);

        expect($result->status)->toBe(InboundEmail::STATUS_LEAD_CREATED)
            ->and($lead->type)->toBe(Lead::TYPE_LEAD)
            ->and($lead->name)->toBe('Need 50 chairs')
            ->and($lead->email)->toBe('jane@customer.test')
            ->and($lead->contact_name)->toBe('Jane Buyer')
            ->and($lead->team_id)->toBe($team->id)
            ->and($lead->description)->toContain('<b>there</b>')
            ->and($lead->description)->not->toContain('script')
            ->and($lead->messages()->where('type', 'comment')->value('subject'))->toBe('Need 50 chairs');
    });

    it('creates an opportunity when the leads setting is off', function () {
        inboundAliasTeam();

        $settings = app(CrmSettings::class);
        $settings->use_leads = false;
        $settings->save();

        $result = app(InboundEmailProcessor::class)->process(inboundRawEmail([]));

        expect(Lead::query()->withoutGlobalScopes()->find($result->lead_id)->type)->toBe(Lead::TYPE_OPPORTUNITY);
    });

    it('adds a reply to the lead it answers instead of creating a new one', function () {
        inboundAliasTeam();

        $processor = app(InboundEmailProcessor::class);

        $first = $processor->process(inboundRawEmail(['Message-ID' => '<first@customer.test>']));

        $reply = $processor->process(inboundRawEmail([
            'To'          => 'someone@elsewhere.test',
            'Subject'     => 'Re: Need 50 chairs',
            'In-Reply-To' => '<first@customer.test>',
        ], 'Also 10 tables.'));

        expect($reply->status)->toBe(InboundEmail::STATUS_REPLY_ADDED)
            ->and($reply->lead_id)->toBe($first->lead_id)
            ->and(Lead::query()->withoutGlobalScopes()->where('email', 'jane@customer.test')->count())->toBe(1);
    });

    it('accepts a [Lead #id] subject tag only from the lead email address', function () {
        inboundAliasTeam();

        $processor = app(InboundEmailProcessor::class);

        $leadId = $processor->process(inboundRawEmail([]))->lead_id;

        expect($processor->process(inboundRawEmail(['To' => 'x@y.test', 'Subject' => "Update [Lead #{$leadId}]"]))->status)->toBe(InboundEmail::STATUS_REPLY_ADDED)
            ->and($processor->process(inboundRawEmail(['From' => 'stranger@evil.test', 'To' => 'x@y.test', 'Subject' => "Update [Lead #{$leadId}]"]))->status)->toBe(InboundEmail::STATUS_IGNORED);
    });

    it('skips duplicates, automatic replies, unknown addresses and mail loops', function () {
        inboundAliasTeam();

        $processor = app(InboundEmailProcessor::class);

        $raw = inboundRawEmail(['Message-ID' => '<dup@customer.test>']);

        $first = $processor->process($raw);

        expect($processor->process($raw)->id)->toBe($first->id)
            ->and(InboundEmail::query()->where('message_id', 'dup@customer.test')->count())->toBe(1)
            ->and($processor->process(inboundRawEmail(['Auto-Submitted' => 'auto-replied']))->status)->toBe(InboundEmail::STATUS_IGNORED)
            ->and($processor->process(inboundRawEmail(['To' => 'sales@other-domain.test']))->status)->toBe(InboundEmail::STATUS_IGNORED)
            ->and($processor->process(inboundRawEmail(['From' => 'sales@example.com']))->status)->toBe(InboundEmail::STATUS_IGNORED);
    });

    it('matches aliases case-insensitively, with plus-addressing, in cc, and on any domain when none is set', function () {
        $team = inboundAliasTeam(domain: null);

        $result = app(InboundEmailProcessor::class)->process(inboundRawEmail(['To' => 'info@customer.test', 'Cc' => 'SALES+web@anything.test']));

        expect($result->status)->toBe(InboundEmail::STATUS_LEAD_CREATED)
            ->and($result->alias->team_id)->toBe($team->id);
    });

    it('applies "Accept emails from"', function () {
        $team = inboundAliasTeam(MailAlias::CONTACT_PARTNERS);

        $processor = app(InboundEmailProcessor::class);

        $rejected = $processor->process(inboundRawEmail(['From' => 'unknown@nobody.test']));

        $user = CrmTestHelper::actingAs();

        $accepted = $processor->process(inboundRawEmail(['From' => $user->email]));

        MailAlias::query()->where('team_id', $team->id)->update(['alias_contact' => MailAlias::CONTACT_FOLLOWERS]);

        $outsider = $processor->process(inboundRawEmail(['From' => 'someone@customer.test']));

        $team->members()->attach($user->id);

        $member = $processor->process(inboundRawEmail(['From' => $user->email]));

        expect($rejected->status)->toBe(InboundEmail::STATUS_REJECTED)
            ->and($accepted->status)->toBe(InboundEmail::STATUS_LEAD_CREATED)
            ->and($outsider->status)->toBe(InboundEmail::STATUS_REJECTED)
            ->and($member->status)->toBe(InboundEmail::STATUS_LEAD_CREATED);
    });

    it('saves attachments in the lead chatter', function () {
        inboundAliasTeam();

        $raw = implode("\r\n", [
            'From: jane@customer.test',
            'To: sales@example.com',
            'Subject: With file',
            'Message-ID: <file@customer.test>',
            'Content-Type: multipart/mixed; boundary="b"',
            '',
            '--b',
            'Content-Type: text/plain',
            '',
            'See attached.',
            '--b',
            'Content-Type: text/plain; name="notes.txt"',
            'Content-Disposition: attachment; filename="notes.txt"',
            '',
            'file content',
            '--b--',
            '',
        ]);

        $lead = Lead::query()->withoutGlobalScopes()->find(app(InboundEmailProcessor::class)->process($raw)->lead_id);

        $attachment = $lead->attachments()->first();

        expect($attachment->original_file_name)->toBe('notes.txt');

        Storage::disk('public')->assertExists($attachment->file_path);
    });
});

it('renames attachments a browser or server could run', function (string $name, string $stored) {
    expect(InboundEmailProcessor::safeAttachmentName($name))->toBe($stored);
})->with([
    ['quote.pdf', 'quote.pdf'],
    ['photo.JPG', 'photo.JPG'],
    ['page.html', 'page.html.txt'],
    ['logo.SVG', 'logo.SVG.txt'],
    ['shell.php', 'shell.php.txt'],
    ['shell.php.jpg', 'shell.php.jpg.txt'],
    ['README', 'README'],
]);

describe('fetching', function () {
    it('processes every unread email of a server and records the last check', function () {
        inboundAliasTeam();

        $server = MailServer::query()->create(['name' => 'Box', 'host' => 'imap.test', 'username' => 'u', 'password' => 'p']);

        $fetcher = new class(app(InboundEmailProcessor::class)) extends InboundMailFetcher
        {
            protected function mailbox(MailServer $server): ImapMailbox
            {
                return new class($server) extends ImapMailbox
                {
                    public function fetchUnread(callable $handle, int $limit = self::BATCH): int
                    {
                        $handle(inboundRawEmail([]));
                        $handle(inboundRawEmail(['To' => 'nobody@else.test']));

                        return 2;
                    }
                };
            }
        };

        $counts = $fetcher->fetch($server);

        expect($counts[InboundEmail::STATUS_LEAD_CREATED])->toBe(1)
            ->and($counts[InboundEmail::STATUS_IGNORED])->toBe(1)
            ->and($server->fresh()->last_fetched_at)->not->toBeNull()
            ->and($server->fresh()->last_error)->toBeNull();
    });

    it('stores the password encrypted', function () {
        $server = MailServer::query()->create(['name' => 'Box', 'host' => 'imap.test', 'username' => 'u', 'password' => 'secret']);

        expect($server->fresh()->password)->toBe('secret')
            ->and(MailServer::query()->whereKey($server->id)->toBase()->value('password'))->not->toBe('secret');
    });
});

describe('screens', function () {
    it('shows the mail servers and received emails with their permissions', function () {
        CrmTestHelper::actingAs(['view_any_crm_mail::server', 'view_any_crm_inbound::email']);

        Livewire::test(ManageMailServers::class)->assertOk();
        Livewire::test(ManageInboundEmails::class)->assertOk();
    });

    it('forbids the mail servers without permission', function () {
        CrmTestHelper::actingAs([]);

        Livewire::test(ManageMailServers::class)->assertForbidden();
    });
});
