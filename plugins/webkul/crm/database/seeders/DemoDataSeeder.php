<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\Account\Models\Product;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\ActivityType;
use Webkul\Crm\Models\CrmTeam;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadConversion;
use Webkul\Crm\Models\LostReason;
use Webkul\Crm\Models\MailAlias;
use Webkul\Crm\Models\PartnerLevel;
use Webkul\Crm\Models\RecurringPlan;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Services\LeadConverter;
use Webkul\Crm\Services\LeadScoring;
use Webkul\Crm\Services\Mail\InboundEmailProcessor;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;

class DemoDataSeeder extends Seeder
{
    protected int $companyId;

    protected array $users = [];

    protected array $teams = [];

    protected array $stages = [];

    protected array $customers = [];

    protected array $tags = [];

    public function run(): void
    {
        if (Lead::withTrashed()->withoutGlobalScopes()->where('name', 'ERP Implementation')->exists()) {
            $this->command?->warn('CRM demo data already exists, skipping.');

            return;
        }

        $admin = User::query()->where('email', 'admin@example.com')->first()
            ?? User::query()->orderBy('id')->firstOrFail();

        Auth::setUser($admin);

        $this->companyId = (int) ($admin->default_company_id ?: 1);

        $this->seedUsers($admin);
        $this->seedTeams();
        $this->seedTags();
        $this->seedCustomers();

        $this->stages = Stage::query()->pluck('id', 'name')->all();

        $leads = $this->seedLeads();
        $opportunities = $this->seedOpportunities();

        $this->seedConversions($leads);
        $this->seedScheduledActivities([...$leads, ...$opportunities]);
        $this->seedDoneActivities([...$leads, ...$opportunities]);
        $this->seedQuotations($opportunities);
        $this->seedInboundEmails();

        app(LeadScoring::class)->rebuildAndRescore();

        $this->command?->info('CRM demo data created: '.count($leads).' leads, '.count($opportunities).' opportunities.');
    }

    protected function seedUsers(User $admin): void
    {
        $this->users['admin'] = $admin->id;

        foreach (['john' => 'John Carter', 'priya' => 'Priya Nair', 'emma' => 'Emma Wilson'] as $key => $name) {
            $this->users[$key] = User::query()->firstOrCreate(
                ['email' => str_replace(' ', '.', strtolower($name)).'@demo-crm.test'],
                ['name' => $name, 'password' => Str::random(32), 'default_company_id' => $this->companyId, 'is_active' => true, 'resource_permission' => 'global'],
            )->id;
        }
    }

    protected function seedTeams(): void
    {
        $teams = [
            'Direct Sales' => ['john', ['US', 'CA', 'GB', 'AU'], 'sales'],
            'Website'      => ['priya', ['IN'], 'website'],
            'Pre-Sales'    => ['emma', ['FR', 'DE', 'AE'], 'presales'],
        ];

        foreach ($teams as $name => [$leader, $countries, $alias]) {
            $team = Team::query()->withoutGlobalScopes()->firstOrCreate(
                ['name' => $name, 'company_id' => $this->companyId],
                ['user_id' => $this->users[$leader], 'creator_id' => $this->users['admin'], 'is_active' => true],
            );

            $team->members()->syncWithoutDetaching([$this->users[$leader], $this->users['admin']]);

            CrmTeam::query()->updateOrCreate(['team_id' => $team->id], [
                'use_leads'         => true,
                'use_opportunities' => true,
                'assignment_optout' => false,
                'assignment_domain' => [
                    'match'      => 'all',
                    'conditions' => [['field' => 'country_id', 'operator' => 'in', 'value' => array_values(array_filter(array_map(fn (string $code): ?int => $this->countryId($code), $countries)))]],
                ],
            ]);

            foreach ([$this->users[$leader] => 30, $this->users['admin'] => 10] as $userId => $capacity) {
                TeamMember::query()->updateOrCreate(
                    ['team_id' => $team->id, 'user_id' => $userId],
                    ['is_active' => true, 'assignment_optout' => false, 'assignment_max' => $capacity],
                );
            }

            MailAlias::query()->updateOrCreate(['team_id' => $team->id], [
                'alias_name'    => $alias,
                'alias_domain'  => 'demo-crm.test',
                'alias_contact' => MailAlias::CONTACT_EVERYONE,
            ]);

            $this->teams[$name] = $team->id;
        }
    }

    protected function seedTags(): void
    {
        foreach (['Product' => '#6366f1', 'Software' => '#0ea5e9', 'Services' => '#10b981', 'Training' => '#f59e0b', 'Consulting' => '#ef4444', 'Upsell' => '#8b5cf6'] as $name => $color) {
            $this->tags[$name] = Tag::query()->firstOrCreate(['name' => $name], ['color' => $color])->id;
        }
    }

    protected function seedCustomers(): void
    {
        $customers = [
            ['Azure Interior', 'US', 'Fremont', 'info@azure-interior.example.com', '+1 510 555 0101'],
            ['Deco Addict', 'FR', 'Paris', 'contact@decoaddict.example.com', '+33 1 55 50 0102'],
            ['Gemini Furniture', 'GB', 'London', 'sales@gemini-furniture.example.com', '+44 20 5550 0103'],
            ['Ready Mat', 'DE', 'Berlin', 'hello@readymat.example.com', '+49 30 5550 0104'],
            ['Lumber Inc', 'CA', 'Toronto', 'orders@lumber.example.com', '+1 416 555 0105'],
            ['Wood Corner', 'AU', 'Sydney', 'team@woodcorner.example.com', '+61 2 5550 0106'],
            ['Acme Corporation', 'US', 'New York', 'purchasing@acme.example.com', '+1 212 555 0107'],
            ['Globex Systems', 'IN', 'Bengaluru', 'it@globex.example.com', '+91 80 5550 0108'],
            ['Initech Solutions', 'IN', 'Pune', 'admin@initech.example.com', '+91 20 5550 0109'],
            ['Stark Industries', 'AE', 'Dubai', 'procurement@stark.example.com', '+971 4 555 0110'],
        ];

        foreach ($customers as [$name, $countryCode, $city, $email, $phone]) {
            $partner = Partner::query()->withoutGlobalScopes()->firstOrCreate(
                ['name' => $name, 'company_id' => $this->companyId],
                [
                    'account_type' => 'company',
                    'sub_type'     => 'partner',
                    'email'        => $email,
                    'phone'        => $phone,
                    'city'         => $city,
                    'country_id'   => $this->countryId($countryCode),
                    'creator_id'   => $this->users['admin'],
                ],
            );

            $this->customers[$name] = $partner->id;
        }

        foreach (['Acme Corporation' => 'Gold', 'Globex Systems' => 'Silver', 'Deco Addict' => 'Bronze'] as $customer => $levelName) {
            $level = PartnerLevel::query()->where('name', $levelName)->first();

            if (! $level) {
                continue;
            }

            DB::table('crm_partners')->updateOrInsert(
                ['partner_id' => $this->customers[$customer]],
                ['level_id' => $level->id, 'level_weight' => $level->partner_weight, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }

    protected function seedLeads(): array
    {
        $leads = [
            ['Website enquiry: 50 ergonomic chairs', 'Laura Bennett', 'Bennett Design Studio', 'Office Manager', 'US', 'Chicago', 'Website', 'john', 'Search engine', 'Google', null, 0],
            ['Bulk order for co-working space', 'Ravi Menon', 'WorkHub Co-Spaces', 'Founder', 'IN', 'Mumbai', 'Website', 'priya', 'LinkedIn', 'LinkedIn', 'Sale', 1],
            ['Interested in training packages', 'Sophie Martin', 'Martin & Fils', 'HR Director', 'FR', 'Lyon', 'Pre-Sales', 'emma', 'Newsletter', 'Email', 'Email Campaign - Services', 1],
            ['Request for product catalogue', 'Lukas Becker', 'Becker Logistik', 'Purchasing Lead', 'DE', 'Hamburg', 'Direct Sales', 'john', 'Facebook', 'Facebook', 'Christmas Special', 2],
            ['Demo request from trade fair', 'Olivia Brown', 'Brown & Partners', 'Operations Head', 'GB', 'Manchester', 'Direct Sales', null, 'Lead Recall', 'Direct', null, 2],
            ['Partnership opportunity', 'Ahmed Khan', 'Desert Rose Trading', 'Managing Director', 'AE', 'Abu Dhabi', 'Pre-Sales', 'emma', 'LinkedIn', 'LinkedIn', null, 3],
            ['Quote for school furniture', 'Noah Thompson', 'Maple Leaf Academy', 'Bursar', 'CA', 'Vancouver', 'Website', 'priya', 'Search engine', 'Google', 'Sale', 4],
            ['Callback: storage solutions', 'Chloe Wright', 'Wright Warehousing', 'Facilities Manager', 'AU', 'Melbourne', 'Direct Sales', 'john', 'Newsletter', 'Phone', null, 5],
            ['Newsletter signup: consulting', 'Ananya Iyer', 'Iyer Analytics', 'CTO', 'IN', 'Chennai', 'Pre-Sales', null, 'Newsletter', 'Email', 'Email Campaign - Products', 6],
            ['Hotel lobby redesign', 'Marco Rossi', 'Grand Rossi Hotels', 'General Manager', 'FR', 'Nice', 'Direct Sales', 'admin', 'Search engine', 'Banner', null, 7],
            ['Cold lead: outdated contact', 'Peter Grant', 'Grant Holdings', 'Assistant', 'GB', 'Leeds', 'Website', 'priya', 'Craigslist', 'Direct', null, 8, 'lost'],
            ['Student project enquiry', 'Mia Fischer', 'Uni Berlin', 'Student', 'DE', 'Berlin', 'Website', 'emma', 'X', 'X', null, 9, 'lost'],
        ];

        return collect($leads)->map(function (array $data): Lead {
            [$name, $contact, $company, $job, $country, $city, $team, $user, $source, $medium, $campaign, $monthsAgo] = $data;

            $lead = Lead::query()->forceCreate([
                'type'            => Lead::TYPE_LEAD,
                'name'            => $name,
                'contact_name'    => $contact,
                'company_name'    => $company,
                'job_title'       => $job,
                'email'           => strtolower(str_replace(' ', '.', $contact)).'@example.com',
                'phone'           => '+1 555 '.random_int(100, 999).' '.random_int(1000, 9999),
                'city'            => $city,
                'country_id'      => $this->countryId($country),
                'team_id'         => $this->teams[$team] ?? null,
                'user_id'         => $this->users[$user ?? 'admin'],
                'utm_source_id'   => UTMSource::query()->where('name', $source)->value('id'),
                'utm_medium_id'   => UTMMedium::query()->where('name', $medium)->value('id'),
                'utm_campaign_id' => $campaign ? UtmCampaign::query()->where('name', $campaign)->value('id') : null,
                'priority'        => OpportunityPriority::Low,
                'probability'     => 10,
                'company_id'      => $this->companyId,
                'created_at'      => $this->monthsAgo($monthsAgo),
                'updated_at'      => $this->monthsAgo($monthsAgo),
            ]);

            if (($data[12] ?? null) === 'lost') {
                $lead->markAsLost(LostReason::query()->orderBy('id')->value('id'), 'No budget this year.');
                $this->backdate($lead, closedAt: $this->monthsAgo($monthsAgo)->addDays(10));
            }

            return $lead;
        })->all();
    }

    protected function seedOpportunities(): array
    {
        $opportunities = [
            ['Office Furniture Refresh', 'Azure Interior', 'New', 'john', 'Direct Sales', 12000, 20, OpportunityPriority::Medium, 1, ['Product'], 1],
            ['Ergonomic Chairs x120', 'Deco Addict', 'New', 'priya', 'Website', 18500, 15, OpportunityPriority::Low, 2, ['Product', 'Upsell'], 0],
            ['ERP Implementation', 'Globex Systems', 'Qualified', 'emma', 'Pre-Sales', 45000, 40, OpportunityPriority::VeryHigh, 2, ['Software', 'Consulting'], 3],
            ['Annual Maintenance Contract', 'Acme Corporation', 'Qualified', 'john', 'Direct Sales', 9800, 50, OpportunityPriority::High, 0, ['Services'], 2],
            ['Staff Training Programme', 'Initech Solutions', 'Qualified', 'priya', 'Pre-Sales', 6400, 35, OpportunityPriority::Medium, 3, ['Training'], 4],
            ['Warehouse Shelving', 'Lumber Inc', 'Proposition', 'john', 'Direct Sales', 23000, 70, OpportunityPriority::High, 1, ['Product'], 5],
            ['Showroom Fit-out', 'Gemini Furniture', 'Proposition', 'emma', 'Direct Sales', 31000, 65, OpportunityPriority::VeryHigh, 0, ['Product', 'Services'], 2],
            ['Cloud Migration Consulting', 'Stark Industries', 'Proposition', 'admin', 'Pre-Sales', 52000, 60, OpportunityPriority::High, 4, ['Consulting', 'Software'], 1],
            ['Desk Accessories Bundle', 'Wood Corner', 'New', 'priya', 'Website', 3200, 10, OpportunityPriority::Low, null, ['Product'], 0],
            ['Conference Room AV Setup', 'Ready Mat', 'Qualified', 'emma', 'Website', 14750, 45, OpportunityPriority::Medium, 1, ['Services'], 6],
            ['Head Office Furniture', 'Acme Corporation', 'Won', 'john', 'Direct Sales', 27500, 100, OpportunityPriority::High, -1, ['Product'], 7, 'won'],
            ['CRM Licences (50 seats)', 'Globex Systems', 'Won', 'emma', 'Pre-Sales', 15000, 100, OpportunityPriority::Medium, -2, ['Software', 'Upsell'], 8, 'won'],
            ['Onboarding Workshop', 'Deco Addict', 'Won', 'priya', 'Website', 4200, 100, OpportunityPriority::Low, -3, ['Training'], 9, 'won'],
            ['Retail Display Units', 'Wood Corner', 'Qualified', 'john', 'Direct Sales', 8800, 30, OpportunityPriority::Medium, -1, ['Product'], 5, 'lost'],
            ['Data Centre Racks', 'Stark Industries', 'Proposition', 'admin', 'Pre-Sales', 39000, 55, OpportunityPriority::High, -2, ['Product'], 6, 'lost'],
            ['Security Audit', 'Initech Solutions', 'New', 'priya', 'Pre-Sales', 7600, 20, OpportunityPriority::Low, -1, ['Consulting'], 10, 'lost'],
        ];

        $lostReasons = LostReason::query()->orderBy('id')->pluck('id')->all();

        return collect($opportunities)->values()->map(function (array $data, int $index) use ($lostReasons): Lead {
            [$name, $customer, $stage, $user, $team, $revenue, $probability, $priority, $closingInMonths, $tags, $monthsAgo] = $data;

            $partner = Partner::query()->withoutGlobalScopes()->find($this->customers[$customer]);

            $createdAt = $this->monthsAgo($monthsAgo);

            $opportunity = Lead::query()->forceCreate([
                'type'                  => Lead::TYPE_OPPORTUNITY,
                'name'                  => $name,
                'partner_id'            => $partner->id,
                'company_name'          => $partner->name,
                'email'                 => $partner->email,
                'phone'                 => $partner->phone,
                'city'                  => $partner->city,
                'country_id'            => $partner->country_id,
                'stage_id'              => $this->stages[$stage === 'Won' ? 'Proposition' : $stage],
                'user_id'               => $this->users[$user],
                'team_id'               => $this->teams[$team],
                'expected_revenue'      => $revenue,
                'probability'           => $stage === 'Won' ? 90 : $probability,
                'priority'              => $priority,
                'expected_closing_date' => $closingInMonths === null ? null : today()->addMonths($closingInMonths)->endOfMonth()->subDays($index % 20),
                'utm_source_id'         => UTMSource::query()->inRandomOrder()->value('id'),
                'utm_medium_id'         => UTMMedium::query()->inRandomOrder()->value('id'),
                'utm_campaign_id'       => $index % 3 === 0 ? UtmCampaign::query()->inRandomOrder()->value('id') : null,
                'converted_at'          => $index % 2 === 0 ? $createdAt->copy()->addDays(3) : null,
                'company_id'            => $this->companyId,
                'created_at'            => $createdAt,
                'updated_at'            => $createdAt,
            ]);

            $opportunity->tags()->sync(array_map(fn (string $tag): int => $this->tags[$tag], $tags));

            $recurring = [
                'Annual Maintenance Contract' => [800, 'Yearly'],
                'CRM Licences (50 seats)'     => [1500, 'Monthly'],
                'Cloud Migration Consulting'  => [2500, 'Monthly'],
            ][$name] ?? null;

            Lead::withTrashed()->whereKey($opportunity->id)->update([
                'color'             => $index % 4 === 0 ? ($index % 11) + 1 : 0,
                'recurring_revenue' => $recurring[0] ?? 0,
                'recurring_plan_id' => $recurring ? RecurringPlan::query()->where('name', $recurring[1])->value('id') : null,
            ]);

            $closedAt = $closingInMonths !== null && $closingInMonths < 0
                ? today()->addMonths($closingInMonths)->startOfMonth()->addDays(12)
                : null;

            if (($data[11] ?? null) === 'won') {
                $opportunity->markAsWon();
                $this->backdate($opportunity, closedAt: $closedAt);
            }

            if (($data[11] ?? null) === 'lost') {
                $opportunity->markAsLost($lostReasons[$index % count($lostReasons)] ?? null, 'Customer went with a cheaper offer.');
                $this->backdate($opportunity, closedAt: $closedAt);
            }

            return $opportunity;
        })->all();
    }

    protected function seedConversions(array $leads): void
    {
        foreach (['Bulk order for co-working space', 'Partnership opportunity'] as $name) {
            $lead = collect($leads)->first(fn (Lead $lead): bool => $lead->name === $name);

            if ($lead && ! $lead->trashed()) {
                app(LeadConverter::class)->convert($lead->fresh(), [
                    'action'   => LeadConversion::ACTION_CONVERT,
                    'customer' => LeadConversion::CUSTOMER_CREATE,
                ]);
            }
        }
    }

    protected function seedQuotations(array $opportunities): void
    {
        $product = Product::query()->first();

        if (! $product) {
            return;
        }

        foreach (['Warehouse Shelving' => 12, 'Showroom Fit-out' => 25, 'ERP Implementation' => 5] as $name => $quantity) {
            $opportunity = collect($opportunities)->first(fn (Lead $lead): bool => $lead->name === $name);

            if (! $opportunity?->partner_id) {
                continue;
            }

            $order = Order::query()->create([
                'partner_id'     => $opportunity->partner_id,
                'company_id'     => $this->companyId,
                'user_id'        => $opportunity->user_id,
                'team_id'        => $opportunity->team_id,
                'origin'         => $opportunity->name,
                'date_order'     => now(),
                'validity_date'  => now()->addDays(30),
                'currency_rate'  => 1,
            ]);

            $order->forceFill(['opportunity_id' => $opportunity->id])->saveQuietly();

            $line = $order->lines()->create([
                'company_id'           => $this->companyId,
                'currency_id'          => $order->currency_id,
                'order_partner_id'     => $order->partner_id,
                'salesman_id'          => $order->user_id,
                'product_id'           => $product->id,
                'product_uom_id'       => $product->uom_id,
                'name'                 => $product->name,
                'product_uom_qty'      => $quantity,
                'product_qty'          => $quantity,
                'qty_delivered_method' => 'manual',
                'price_unit'           => $product->price,
                'creator_id'           => $this->users['admin'],
            ]);

            $line->taxes()->sync($product->productTaxes->modelKeys());

            SaleOrder::computeSaleOrderLine($line->fresh());
            SaleOrder::computeSaleOrder($order->fresh());
        }
    }

    protected function seedInboundEmails(): void
    {
        $emails = [
            ['website@demo-crm.test', 'Rahul Verma', 'rahul.verma@example.com', 'Need 40 workstations for our Pune office', 'demo-inbound-1@example.com', null],
            ['presales@demo-crm.test', 'Clara Dubois', 'clara.dubois@example.com', 'Consulting for warehouse automation', 'demo-inbound-2@example.com', null],
            ['website@demo-crm.test', 'Rahul Verma', 'rahul.verma@example.com', 'Re: Need 40 workstations for our Pune office', 'demo-inbound-3@example.com', 'demo-inbound-1@example.com'],
        ];

        foreach ($emails as [$to, $fromName, $fromEmail, $subject, $messageId, $inReplyTo]) {
            $headers = [
                "From: {$fromName} <{$fromEmail}>",
                "To: {$to}",
                "Subject: {$subject}",
                "Message-ID: <{$messageId}>",
                'Date: '.now()->toRfc2822String(),
                'Content-Type: text/plain; charset=UTF-8',
            ];

            if ($inReplyTo) {
                $headers[] = "In-Reply-To: <{$inReplyTo}>";
                $headers[] = "References: <{$inReplyTo}>";
            }

            app(InboundEmailProcessor::class)->process(implode("\r\n", $headers)."\r\n\r\nHello,\r\n\r\n{$subject}. Please send a quote.\r\n\r\nThanks,\r\n{$fromName}\r\n");
        }
    }

    protected function seedScheduledActivities(array $records): void
    {
        $types = ActivityType::query()->where('plugin', 'crm')->pluck('id', 'name');

        $plans = [
            ['Call', 'Follow-up call on proposal', -3],
            ['Email', 'Send product catalogue', -1],
            ['Meeting', 'On-site requirements meeting', 0],
            ['Call', 'Discuss pricing', 0],
            ['To-Do', 'Prepare quotation', 2],
            ['Meeting', 'Product demo', 4],
            ['Email', 'Share case studies', 6],
            ['To-Do', 'Check stock availability', 9],
            ['Call', 'Intro call', 1],
            ['Email', 'Send contract draft', 12],
        ];

        $openRecords = collect($records)->reject(fn (Lead $record): bool => $record->trashed() || $record->isWon())->values();

        foreach ($plans as $index => [$type, $summary, $dueInDays]) {
            /** @var Lead $record */
            $record = $openRecords[$index % $openRecords->count()];

            $record->addMessage([
                'type'             => 'activity',
                'activity_type_id' => $types[$type] ?? null,
                'summary'          => $summary,
                'assigned_to'      => $record->user_id ?? $this->users['admin'],
                'date_deadline'    => today()->addDays($dueInDays),
                'company_id'       => $this->companyId,
            ]);
        }
    }

    protected function seedDoneActivities(array $records): void
    {
        $types = ActivityType::query()->where('plugin', 'crm')->pluck('id', 'name')->values()->all();

        $summaries = ['Qualification call', 'Sent brochure', 'Needs analysis meeting', 'Follow-up email', 'Pricing discussion', 'Demo session', 'Negotiation call', 'Sent revised quote'];

        $openRecords = collect($records)->reject(fn (Lead $record): bool => $record->trashed())->values();

        for ($index = 0; $index < 24; $index++) {
            /** @var Lead $record */
            $record = $openRecords[$index % $openRecords->count()];

            $message = $record->addMessage([
                'type'             => 'activity',
                'activity_type_id' => $types[$index % count($types)],
                'summary'          => $summaries[$index % count($summaries)],
                'assigned_to'      => $record->user_id ?? $this->users['admin'],
                'date_deadline'    => today(),
                'company_id'       => $this->companyId,
            ]);

            $activity = Activity::query()->find($message->id);

            $activity?->markAsDone($index % 3 === 0 ? 'Customer was very interested.' : null);

            $doneAt = now()->subMonths($index % 12)->subDays($index % 7);

            ActivityLog::query()->latest('id')->first()?->forceFill([
                'done_at'             => $doneAt,
                'activity_created_at' => $doneAt->copy()->subDays(3),
                'date_deadline'       => $doneAt->toDateString(),
                'created_at'          => $doneAt,
                'updated_at'          => $doneAt,
            ])->save();
        }
    }

    protected function backdate(Lead $lead, ?Carbon $closedAt): void
    {
        if (! $closedAt) {
            return;
        }

        Lead::withTrashed()->whereKey($lead->id)->update([
            'closed_at'  => $closedAt,
            'deleted_at' => $lead->trashed() ? $closedAt : null,
        ]);
    }

    protected function monthsAgo(int $months): Carbon
    {
        return now()->subMonths($months)->startOfMonth()->addDays(($months * 7) % 25)->setTime(10, 0);
    }

    protected function countryId(string $code): ?int
    {
        return Country::query()->where('code', $code)->value('id');
    }
}
