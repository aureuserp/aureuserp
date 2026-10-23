<?php

namespace Webkul\Crm\Services\Mail;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Webkul\Crm\Models\InboundEmail;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\MailAlias;
use Webkul\Crm\Models\MailServer;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class InboundEmailProcessor
{
    public const MAX_ATTACHMENT_SIZE = 25 * 1024 * 1024;

    public const UNSAFE_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'pht', 'phps', 'phar',
        'html', 'htm', 'xhtml', 'shtml', 'xht', 'svg', 'svgz', 'xml', 'xsl', 'xslt',
        'js', 'mjs', 'swf', 'htaccess', 'htpasswd', 'cgi', 'pl', 'py', 'sh', 'asp', 'aspx', 'jsp',
    ];

    public function process(string|ParsedEmail $email, ?MailServer $server = null): InboundEmail
    {
        $email = is_string($email) ? ParsedEmail::fromRaw($email) : $email;

        if ($existing = InboundEmail::query()->where('message_id', $email->messageId)->first()) {
            return $existing;
        }

        $log = fn (string $status, ?string $reason = null, ?Lead $lead = null, ?MailAlias $alias = null): InboundEmail => InboundEmail::query()->create([
            'mail_server_id' => $server?->getKey(),
            'alias_id'       => $alias?->getKey(),
            'lead_id'        => $lead?->getKey(),
            'message_id'     => $email->messageId,
            'in_reply_to'    => $email->inReplyTo,
            'from_email'     => $email->fromEmail,
            'from_name'      => $email->fromName ? mb_substr($email->fromName, 0, 255) : null,
            'recipients'     => implode(', ', $email->recipients),
            'subject'        => mb_substr($email->subject, 0, 255),
            'status'         => $status,
            'reason'         => $reason,
            'received_at'    => $email->date ?? now(),
        ]);

        try {
            if (blank($email->fromEmail)) {
                return $log(InboundEmail::STATUS_IGNORED, __('crm::filament/mail.reasons.no-sender'));
            }

            if ($email->isAutoReply) {
                return $log(InboundEmail::STATUS_IGNORED, __('crm::filament/mail.reasons.auto-reply'));
            }

            $aliases = $this->activeAliases();

            if ($aliases->contains(fn (MailAlias $alias): bool => $this->isAliasAddress($alias, $email->fromEmail))) {
                return $log(InboundEmail::STATUS_IGNORED, __('crm::filament/mail.reasons.from-alias'));
            }

            if ($lead = $this->findThreadLead($email)) {
                DB::transaction(fn () => $this->postEmail($lead, $email));

                return $log(InboundEmail::STATUS_REPLY_ADDED, null, $lead);
            }

            $alias = $aliases->first(fn (MailAlias $alias): bool => collect($email->recipients)->contains(fn (string $recipient): bool => $this->isAliasAddress($alias, $recipient)));

            if (! $alias) {
                return $log(InboundEmail::STATUS_IGNORED, __('crm::filament/mail.reasons.no-alias'));
            }

            if (! $this->isSenderAllowed($alias, $email->fromEmail)) {
                return $log(InboundEmail::STATUS_REJECTED, __('crm::filament/mail.reasons.not-allowed', [
                    'contact' => __('crm::filament/mail.alias-contacts.'.$alias->alias_contact),
                ]), null, $alias);
            }

            $lead = DB::transaction(function () use ($alias, $email): Lead {
                $lead = $this->createLead($alias, $email);

                $this->postEmail($lead, $email);

                return $lead;
            });

            return $log(InboundEmail::STATUS_LEAD_CREATED, null, $lead, $alias);
        } catch (Throwable $exception) {
            report($exception);

            return $log(InboundEmail::STATUS_FAILED, mb_substr($exception->getMessage(), 0, 1000));
        }
    }

    public function activeAliases(): EloquentCollection
    {
        return MailAlias::query()
            ->whereNotNull('alias_name')
            ->whereHas('team', fn ($query) => $query->withoutGlobalScopes()->whereNull('deleted_at')->where('is_active', true))
            ->with(['team' => fn ($query) => $query->withoutGlobalScopes()])
            ->get();
    }

    public function isAliasAddress(MailAlias $alias, string $address): bool
    {
        $address = mb_strtolower(trim($address));

        if (! str_contains($address, '@') || blank($alias->alias_name)) {
            return false;
        }

        [$local, $domain] = explode('@', $address, 2);

        $local = explode('+', $local, 2)[0];

        return $local === $alias->alias_name && (blank($alias->alias_domain) || $domain === $alias->alias_domain);
    }

    protected function findThreadLead(ParsedEmail $email): ?Lead
    {
        $threadIds = $email->threadMessageIds();

        if ($threadIds) {
            $leadId = InboundEmail::query()
                ->whereIn('message_id', $threadIds)
                ->whereNotNull('lead_id')
                ->latest('id')
                ->value('lead_id');

            if ($leadId && ($lead = $this->leadQuery()->find($leadId))) {
                return $lead;
            }
        }

        if (preg_match('/\[(?:lead|opportunity)?\s*#(\d+)\]/i', $email->subject, $matches)) {
            $lead = $this->leadQuery()->find((int) $matches[1]);

            if ($lead && $lead->email_normalized === Lead::normalizeEmail($email->fromEmail)) {
                return $lead;
            }
        }

        return null;
    }

    public function isSenderAllowed(MailAlias $alias, string $fromEmail): bool
    {
        $fromEmail = mb_strtolower($fromEmail);

        return match ($alias->alias_contact) {
            MailAlias::CONTACT_PARTNERS  => $this->findPartner($fromEmail) !== null || $this->isUserEmail($fromEmail),
            MailAlias::CONTACT_FOLLOWERS => $this->isTeamMemberEmail($alias, $fromEmail),
            MailAlias::CONTACT_EMPLOYEES => $this->isUserEmail($fromEmail) || $this->isEmployeeEmail($fromEmail),
            default                      => true,
        };
    }

    protected function createLead(MailAlias $alias, ParsedEmail $email): Lead
    {
        $team = $alias->team;

        $partner = $this->findPartner($email->fromEmail);

        $usesLeads = app(CrmSettings::class)->use_leads && ($team->crmTeam?->use_leads ?? true);

        $lead = new Lead;

        $lead->forceFill([
            'type'         => $usesLeads ? Lead::TYPE_LEAD : Lead::TYPE_OPPORTUNITY,
            'name'         => mb_substr(filled($email->subject) ? $email->subject : __('crm::filament/mail.no-subject', ['email' => $email->fromEmail]), 0, 255),
            'email'        => $email->fromEmail,
            'contact_name' => $email->fromName ?? $partner?->name,
            'partner_id'   => $partner?->getKey(),
            'description'  => $this->bodyHtml($email),
            'team_id'      => $team->getKey(),
            'user_id'      => null,
            'company_id'   => $team->company_id ?? $partner?->company_id ?? Company::query()->value('id'),
        ]);

        $lead->save();

        return $lead;
    }

    protected function postEmail(Lead $lead, ParsedEmail $email): void
    {
        $partner = $this->findPartner($email->fromEmail);

        $sender = e(filled($email->fromName) ? $email->fromName.' <'.$email->fromEmail.'>' : $email->fromEmail);

        $message = $lead->addMessage([
            'type'        => 'comment',
            'subject'     => e($email->subject),
            'body'        => '<p><strong>'.e(__('crm::filament/mail.chatter.from')).'</strong> '.$sender.'</p>'.$this->bodyHtml($email),
            'company_id'  => $lead->company_id,
            'is_internal' => false,
            'causer_type' => $partner?->getMorphClass(),
            'causer_id'   => $partner?->getKey(),
        ]);

        $paths = [];

        foreach ($email->attachments as $attachment) {
            if (strlen($attachment['content']) > self::MAX_ATTACHMENT_SIZE) {
                continue;
            }

            $path = 'messages-attachments/inbound/'.Str::uuid().'/'.static::safeAttachmentName($attachment['name']);

            Storage::disk('public')->put($path, $attachment['content']);

            $paths[] = $path;
        }

        if ($paths) {
            $lead->addAttachments($paths, ['message_id' => $message->getKey(), 'company_id' => $lead->company_id]);
        }
    }

    public static function safeAttachmentName(string $name): string
    {
        $segments = array_slice(explode('.', mb_strtolower($name)), 1);

        return array_intersect($segments, self::UNSAFE_EXTENSIONS) ? $name.'.txt' : $name;
    }

    protected function bodyHtml(ParsedEmail $email): string
    {
        if (filled($email->html)) {
            $html = preg_replace('#<(style|script|head)\b[^>]*>.*?</\1>#is', '', $email->html);

            return Str::sanitizeHtml((string) $html);
        }

        if (filled($email->text)) {
            return '<p>'.nl2br(e(trim($email->text))).'</p>';
        }

        return '';
    }

    protected function leadQuery()
    {
        return Lead::query()->withoutGlobalScopes();
    }

    protected function findPartner(?string $email): ?Partner
    {
        if (blank($email)) {
            return null;
        }

        return Partner::query()
            ->withoutGlobalScopes()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->orderBy('id')
            ->first();
    }

    protected function isUserEmail(string $email): bool
    {
        return User::query()->withoutGlobalScopes()->whereRaw('LOWER(email) = ?', [$email])->exists();
    }

    protected function isEmployeeEmail(string $email): bool
    {
        if (! Schema::hasTable('employees_employees') || ! Schema::hasColumn('employees_employees', 'work_email')) {
            return false;
        }

        return DB::table('employees_employees')->whereNull('deleted_at')->whereRaw('LOWER(work_email) = ?', [$email])->exists();
    }

    protected function isTeamMemberEmail(MailAlias $alias, string $email): bool
    {
        $team = $alias->team;

        $userIds = DB::table('sales_team_members')->where('team_id', $team->getKey())->pluck('user_id')->push($team->user_id)->filter();

        return User::query()->withoutGlobalScopes()->whereKey($userIds)->whereRaw('LOWER(email) = ?', [$email])->exists();
    }
}
