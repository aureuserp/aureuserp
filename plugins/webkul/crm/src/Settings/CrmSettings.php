<?php

namespace Webkul\Crm\Settings;

use Spatie\LaravelSettings\Settings;

class CrmSettings extends Settings
{
    /**
     * Leads: a qualification step before the creation of an opportunity. When off, the Leads
     * menu is hidden and new records start as opportunities.
     */
    public bool $use_leads;

    /**
     * Recurring revenues on opportunities, with recurring plans (Monthly, Yearly…).
     */
    public bool $use_recurring_revenues;

    /**
     * A salesperson can be a member of several sales teams.
     */
    public bool $multi_teams;

    /**
     * Rule-based assignment of leads to teams and salespeople.
     */
    public bool $use_auto_assignment;

    /**
     * `manual` (from the "Assign now" buttons) or `auto` (repeatedly, on a schedule).
     */
    public string $auto_assignment_action;

    public int $auto_assignment_interval_number;

    /**
     * `minutes`, `hours`, `days` or `weeks`.
     */
    public string $auto_assignment_interval_type;

    /**
     * When the next scheduled assignment runs (ISO date-time), or null.
     */
    public ?string $auto_assignment_run_at;

    public static function group(): string
    {
        return 'crm';
    }
}
