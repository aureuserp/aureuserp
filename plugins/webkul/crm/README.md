# CRM Plugin

CRM is the AureusERP plugin for customer relationship management. This document tracks what has been built, how it is wired, and the decisions behind it. Add an entry to the [Development Log](#development-log) whenever the plugin changes.

**Current scope (v0.3):**

- **Leads** (qualification step), with Convert to Opportunity,
- the opportunity pipeline (stages, won/lost, team pipelines),
- My Activities,
- contacts,
- configuration screens.

## Lead flow

Leads and opportunities are both rows in `crm_leads`. `type = 'lead'` rows appear under **CRM → Leads**, and `type = 'opportunity'` rows under **CRM → Sales → My Pipeline**.

1. **Create** a lead (Leads → New Lead, or **+** on a kanban column). The form has:
   - header fields: name, probability,
   - left column: company name, address, website, salesperson, sales team,
   - right column: contact name, email, job position, phone, priority, tags,
   - tabs: **Notes**, and **Extra Info** with Marketing (company, campaign, medium, source, referred by) and Analysis (assignment date, closed date).
2. The **assignment date** is set automatically whenever a salesperson is assigned.
3. **Convert to Opportunity** (header of view/edit, or the row menu) asks for:
   - **Customer**: create a new one from the lead (a company if a company name is set, otherwise an individual, copying email, phone, website and address), link an existing customer, or none,
   - the salesperson and sales team.
   It then switches `type` to `opportunity`, sets `converted_at`, and opens the opportunity in My Pipeline. The logic is `Lead::convertToOpportunity()` / `createCustomer()`.
4. **Lost** uses the same action as opportunities (lost reason plus closing note; the lead is archived). **Restore** brings it back.
5. Activities can be scheduled on leads (clock icon, Chatter). My Activities opens the right screen for leads and opportunities via `Filament\Support\LeadResourceResolver`.

The views are **Kanban** (default; stage columns, won stages hidden, a My Leads filter, quick create) and **List** (My Leads / Unassigned / All Leads / Lost). Like My Pipeline, the header shows a single toggle to the other view: **List** on the Kanban, **Kanban** on the List.

## Opportunity flow

1. **Sales → Teams** lists every sales team with its open opportunities and open expected revenue. Teams are the same records as in Configuration → Sales Teams.
2. Opening a team shows **that team's pipeline**: its opportunities, grouped by stage. **New Opportunity** on that page creates the opportunity with the team already set.
3. An opportunity moves through the **stages** from Configuration → Pipeline → Stages (default: New → Qualified → Proposition → Won). On the form and the view page the stage appears as a stepper. **Stages before the current one are shown as active too** (e.g. at Proposition, New and Qualified are highlighted), and in the edit form this follows the selected stage live. This is CSS-only (`.crm-stepper-previous-active` in `resources/css/index.css`, using `:has()`) and enabled per stepper via `extraAttributes(['class' => 'crm-stepper-previous-active'])`. The shared `ProgressStepper` from the Fields plugin is unchanged, so other plugins keep their behaviour.
4. **Won** moves it to the first stage flagged *Is Won Stage?*. That sets probability to 100% and records `closed_at`. Moving it out of a won stage clears `closed_at`.
5. **Lost** asks for a lost reason (Configuration → Pipeline → Lost Reasons) and an optional closing note. It sets probability to 0 and archives the opportunity (soft delete). **Restore** brings it back and clears the lost reason.
6. **Sales → My Pipeline** lists all opportunities, with the views *My Pipeline* (default), *Open*, *Unassigned*, *Won* and *Lost*.
7. Opportunities have Chatter (messages, log notes, activities). Scheduling activities offers the **CRM** activity types and plans (`Lead::ACTIVITY_PLAN_PLUGIN = 'crm'`).

**Kanban (default) and List views.** My Pipeline opens on the Kanban board, and the **List** / **Kanban** buttons in the header switch between them:

- **List** (`/admin/crm/sales/pipeline/list`): a flat, sortable table with My Pipeline / Open / Unassigned / Won / Lost views. "Group by → Stage" is still available.
- **Kanban** (`/admin/crm/sales/pipeline`, the resource's `index` page):
  - columns are the stages, with each column's count and expected-revenue total; Won stages are green,
  - cards show customer · revenue and priority/salesperson/tag badges, and clicking a card opens the opportunity,
  - drag a card to change its stage. Dropping into a Won stage applies the Won rules. Moves require `update_crm_opportunity`,
  - **+** on a column quick-creates an opportunity in that stage,
  - it opens on the current user's opportunities. Use the filter button to switch salesperson, team or priority.
  - A team's pipeline page has a **Kanban** button that opens `/pipeline?team={id}`, filtered to that team.
  - The toolbar (a compact search box, and a filter panel that opens below its button) comes from CRM's own copy of the package's board view, `resources/views/filament/kanban/board.blade.php` (`->boardView('crm::filament.kanban.board')`). Its sizing uses the unlayered `.crm-kanban-*` classes in `resources/css/index.css`. Tailwind utilities would be overridden, because other plugins' Tailwind builds load after CRM's and also contain `.w-full`.

The board uses [`wezlo/filament-kanban`](https://github.com/mustafakhaleddev/filament-kanban) (root `composer.json`, approved 2026-10-19). `CrmPlugin` registers `FilamentKanbanPlugin` on the admin panel.

## Prerequisites

CRM declares these plugin dependencies in `CrmServiceProvider::hasDependencies()`. The installer installs them first and records them in the plugin manager:

| Dependency | Why CRM needs it |
|---|---|
| `contacts` | Sales → Contacts reuses the Contacts plugin's partner screens |
| `sales` | Sales Teams and Activity Plans reuse Sales screens; it pulls in `invoices`, `payments`, `accounts` and `products` |

Core plugins are always installed, so they are not listed as dependencies. CRM uses `security` (Users, Companies), `support` (Activity Types, companies), `partners` (base partner screens) and `chatter`.

## Installation

```bash
composer dump-autoload
php artisan crm:install --no-interaction
```

You can also install it from **Plugins** in the admin panel. The installer:

1. installs dependencies (`contacts`, `sales` and its chain),
2. runs the CRM migrations (`crm_tags`, `crm_lost_reasons`, `crm_stages`, `crm_leads`, `crm_lead_tags`),
3. seeds the defaults:
   - activity types: Email, Call, Meeting, To-Do,
   - lost reasons: Too expensive, Not enough stock, No resources or skills available,
   - stages: New, Qualified, Proposition, Won,
4. regenerates Shield permissions for the admin panel (`shield:generate --all --option=permissions --panel=admin`),
5. syncs every permission to the first role (admin),
6. clears caches so the CRM navigation appears.

Seeders only run on a fresh install. When re-running `crm:install` on an installed plugin, the installer skips them by default; run `php artisan db:seed --class='Webkul\Crm\Database\Seeders\DatabaseSeeder' --force` instead. The seeders upsert by name (activity types by `plugin` + `name`), so they never create duplicates.

Uninstall with `php artisan crm:uninstall`, which also purges the Chatter messages of opportunities. CRM owns the `crm_tags`, `crm_lost_reasons`, `crm_stages`, `crm_leads` and `crm_lead_tags` tables. CRM activity types and plans are rows in the shared `activity_types` / `activity_plans` tables with `plugin = 'crm'`.

> Re-run `crm:install` after changing `config/filament-shield.php`, so new permissions are generated and assigned.

## Navigation

```
CRM                                   (top navigation group)
├── Sales                             /admin/crm/sales
│   ├── My Pipeline                   /admin/crm/sales/pipeline            (Kanban, default; List at /pipeline/list)
│   ├── My Activities                 /admin/crm/sales/activities          (List, default; /kanban, /graph)
│   ├── Teams                         /admin/crm/sales/teams
│   │                                 /admin/crm/sales/teams/{team}/opportunities  (team pipeline)
│   └── Contacts                      /admin/crm/sales/contacts
│                                     (record tabs: View, Edit, Contacts, Addresses, Bank Accounts*)
├── Leads                             /admin/crm/leads
│   └── Leads                         /admin/crm/leads/leads               (Kanban, default; List at /leads/list)
└── Configuration                     /admin/crm/configurations
    ├── Sales Teams                   /admin/crm/configurations/teams
    ├── Pipeline
    │   ├── Stages                    /admin/crm/configurations/stages
    │   ├── Tags                      /admin/crm/configurations/tags
    │   └── Lost Reasons              /admin/crm/configurations/lost-reasons
    ├── Activities
    │   ├── Activity Types            /admin/crm/configurations/activity-types
    │   └── Activity Plans            /admin/crm/configurations/activity-plans
    └── General Settings
        ├── Users                     /admin/crm/configurations/users
        ├── Languages                 /admin/crm/configurations/languages
        └── Companies                 /admin/crm/configurations/companies
```

\* Bank Accounts appears only when the `accounts` plugin is installed, the same as in the Contacts app.

| Screen | Class | Built on | Data source |
|---|---|---|---|
| Leads | `Filament\Clusters\Leads\Resources\LeadResource` | CRM resource (`HasCrmAuthorization` + `LeadRecordPolicy`): `KanbanLeads` (index), `ListLeads`, create/view/edit, `ConvertToOpportunityAction` | `crm_leads` where `type = 'lead'` |
| My Activities | `Filament\Clusters\Sales\Resources\ActivityResource` | CRM resource over Chatter activities: `ListActivities` (table), `KanbanActivities`, `GraphActivities` (stats + 3 charts) | `chatter_messages` where `type = 'activity'` on `crm_leads` (`Models\Activity`) |
| My Pipeline | `Filament\Clusters\Sales\Resources\OpportunityResource` | CRM-owned resource (list with table views, create, view, edit). Actions: `MarkAsWonAction`, `MarkAsLostAction` | `crm_leads` where `type = 'opportunity'` |
| Teams | `Filament\Clusters\Sales\Resources\TeamResource` | read-only overview of `Sale\Models\Team` with open counts and revenue. `ManageTeamOpportunities` (ManageRelatedRecords) shows the team pipeline | `sales_teams` + `crm_leads.team_id` |
| Stages | `Filament\Clusters\Configurations\Resources\StageResource` | CRM-owned, reorderable manage page | `crm_stages` |
| Contacts | `Filament\Clusters\Sales\Resources\ContactResource` | extends `Webkul\Contact\Filament\Resources\PartnerResource` (all record pages subclassed) | `partners_partners` (shared with Contacts) |
| Users | `Filament\Clusters\Configurations\Resources\UserResource` | extends `Webkul\Security\Filament\Resources\UserResource` | `users` table (shared) |
| Languages | `Filament\Clusters\Configurations\Pages\ManageLanguages` | custom page with an embedded table | `config('app.supported_locales')` (read-only) |
| Companies | `Filament\Clusters\Configurations\Resources\CompanyResource` | extends `Webkul\Security\Filament\Resources\CompanyResource` | `companies` table (shared) |
| Sales Teams | `Filament\Clusters\Configurations\Resources\TeamResource` | extends `Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource` | `sales_teams` table (shared with Sales) |
| Tags | `Filament\Clusters\Configurations\Resources\TagResource` | CRM-owned resource, single manage page (create/edit/view in modals) | `crm_tags` (name unique, color) |
| Lost Reasons | `Filament\Clusters\Configurations\Resources\LostReasonResource` | CRM-owned resource, manage page with Active / Archived tabs | `crm_lost_reasons` (name, soft-delete = archived) |
| Activity Types | `Filament\Clusters\Configurations\Resources\ActivityTypeResource` | extends `Webkul\Support\Filament\Resources\ActivityTypeResource` with `$pluginName = 'crm'` | `activity_types` where `plugin = 'crm'` |
| Activity Plans | `Filament\Clusters\Configurations\Resources\ActivityPlanResource` | extends `Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource` (form, infolist, plan templates); own `Tables\ActivityPlansTable` | `activity_plans` where `plugin = 'crm'` |

Activity Types and Plans are **CRM-only records**. Unlike Users, Companies and Sales Teams, they aren't shared with other apps. The list pages show only `plugin = 'crm'` rows, and creating a record sets `plugin = 'crm'` automatically. Activity Plans needs its own table class because Filament stacks `modifyQueryUsing()` scopes: reusing the Sales table would combine its `sales` scope with `crm` and return nothing.

### Left-side navigation

Every CRM page in a section with **more than one menu item** (Sales, Configuration) shows that menu in the left sidebar, including create, view and edit pages. A section with **a single item** (currently Leads) shows **no left sidebar**, so its pages use the full width. Once the section gets a second item, the sidebar appears automatically. How it works:

- CRM's own resources get the sidebar by default (list pages) or through `Webkul\Support\Traits\HasRecordNavigationTabs` (record pages with tabs, such as opportunities). That trait shows the cluster menu on the left and the record tabs as a strip above the content.
- `Filament\Concerns\HasCrmClusterNavigation` returns the cluster menu only when it has more than one item. Pages reused from other plugins that would otherwise show no menu use it: the Users, Companies and Sales Teams create/view/edit pages, and the team pipeline page.
- `ContactResource` sets `$subNavigationPosition = Start`, because the Contacts plugin puts its menu at the top.

- All Lead pages use `HasCrmClusterNavigation`. View/Edit combine it with `HasRecordNavigationTabs` (`insteadof` for `getSubNavigation` / `getSubNavigationPosition`), so the record tabs stay.

### Floating record bar

Lead and opportunity View/Edit pages show one floating bar with the record tabs **and** the workflow buttons:

- Opportunity: **View | Edit | Won | Lost**
- Lead: **View | Edit | Convert to Opportunity | Lost**

Buttons hide when they don't apply (e.g. no Won once won; only View | Edit once lost). The page header keeps Chatter, Edit/View, Restore and Force delete.

This comes from `Filament\Concerns\HasFloatingRecordActionBar` (view `crm::filament.record-action-bar`). It replaces the Support `RecordNavigationTabs` header widget with the same tab markup, rendered by the page itself. That way Won/Lost/Convert stay normal page actions (`markAsWonAction()`, `markAsLostAction()`, `convertToOpportunityAction()`), with working modals and record refresh. Pages list their buttons in `getFloatingRecordActions()`.

**When adding a CRM page**, check that it shows the menu (or none, for a single-item section). Add `HasCrmClusterNavigation` to create/view/edit pages that have no record tabs.

### Same UI as AureusERP

The CRM resources do not copy forms, tables or infolists. Each one extends the original resource and its pages, and overrides only:

- the cluster and the navigation group, label, sort and icon,
- `getPages()`, so routes resolve inside the CRM cluster,
- `$isGloballySearchable = false` on Users and Companies, so global search doesn't show duplicate results,
- authorization (see below).

Any fix or feature added to the Security or Sales screens therefore shows up in CRM automatically.

## Permissions (Roles)

CRM screens have **their own permissions**, listed under CRM in **Settings → Roles**. They are independent of the Security and Sales permissions for the same records:

| Resource / Page | Permissions |
|---|---|
| Leads | the full set with `_crm_lead`. `update` allows Convert, `delete` allows Lost, `restore` allows Restore. Separate from the `_crm_opportunity` permissions |
| My Activities | `view_any`, `view`, `update` (Done / Reschedule / move on kanban), `delete`, `delete_any` (Cancel) with `_crm_activity` |
| Pipeline (opportunities) | the full set with `_crm_opportunity`. `delete` is what allows **Lost**, `update` allows **Won**, and `restore` allows **Restore** |
| Teams (Sales → Teams) | reuses the `_crm_team` permissions of Configuration → Sales Teams. It is excluded from Shield so the role editor doesn't show it twice |
| Stages | `view_any`, `view`, `create`, `update`, `delete`, `delete_any`, `reorder` with `_crm_stage` |
| Contacts | `view_any`, `view`, `create`, `update`, `delete`, `delete_any`, `restore`, `restore_any`, `force_delete`, `force_delete_any` with `_crm_contact` |
| Users | `view_any_crm_user`, `view_crm_user`, `create_crm_user`, `update_crm_user`, `delete_crm_user`, `delete_any_crm_user`, `restore_crm_user`, `restore_any_crm_user`, `force_delete_crm_user`, `force_delete_any_crm_user` |
| Companies | the same set with `_crm_company`, plus `reorder_crm_company` |
| Sales Teams | the same set with `_crm_team`, plus `reorder_crm_team` |
| Tags | `view_any`, `view`, `create`, `update`, `delete`, `delete_any` with `_crm_tag` |
| Lost Reasons | the full set (including `restore*` and `force_delete*`) with `_crm_lost::reason` |
| Activity Types | the same set with `_crm_activity::type`, plus `reorder_crm_activity::type` |
| Activity Plans | the same set with `_crm_activity::plan` |
| Languages | `page_crm_manage_languages` |

Examples:

- A role with `view_any_sale_team` but no `view_any_crm_team` sees Sales Teams in Sales but not in CRM.
- A role with only CRM permissions sees the CRM screens but not the Sales or Security ones.

### How it works

The resources reuse models from other plugins (`Security\Models\User`, `Security\Models\Company`, `Sale\Models\Team`, `Contact\Models\Partner`). Laravel resolves policies per model, so by default the Security and Sales policies would apply. To keep CRM independent:

1. `config/filament-shield.php` lists these resources under `resources.manage`, so Shield generates `*_crm_*` keys. The key format comes from `Webkul\PluginManager\PermissionManager`: `<affix>_crm_<resource>`.
2. `src/Policies/{User,Company,Team,Contact}Policy.php` check those keys. `UserPolicy` keeps the Security plugin's ownership rules (`hasAccess(..., 'creator')`, and no force-deleting your own account).
3. `src/Filament/Concerns/HasCrmAuthorization.php` overrides `Resource::getAuthorizationResponse()` to call the CRM policy named by `getCrmPolicy()`. Filament routes every resource check (page access, header, table and bulk actions) through this method.

The models are deliberately **not** subclassed. A `Crm\Models\User` subclass would change the morph class used by Spatie roles and chatter, and would skip observers registered on the parent models.

**When adding a new CRM resource that reuses another plugin's model**, use `HasCrmAuthorization`, add a CRM policy, and register the resource in `config/filament-shield.php`. A resource with its own CRM model only needs a normal policy in `src/Policies`, which Laravel discovers automatically.

Activity Types and Plans use that second approach: `src/Models/ActivityType` and `src/Models/ActivityPlan` are thin subclasses of the Support models, so `ActivityTypePolicy` and `ActivityPlanPolicy` are discovered automatically. Subclassing is safe here because these models carry no chatter, roles or observers.

## My Activities

**On each lead and opportunity page.** The View and Edit pages show an **Activities** section below the details: `Filament\RelationManagers\ActivitiesRelationManager`, registered on both `LeadResource` and `OpportunityResource`.
- It lists every planned activity on that record, however it was scheduled (Chatter panel, clock icon, My Activities), with a count badge.
- It has the same columns and row actions as My Activities: ✓ Done (with feedback), Reschedule and Cancel.
- Its **Schedule Activity** button is locked to that record (`ScheduleActivityAction::forLead()`), so there's no record picker.
- It uses `Lead::crmActivities()`, a `hasMany` returning `Models\Activity`.
- It lazy-loads when scrolled into view (Filament's default).

**Scheduling an activity** works from three places:

1. **Schedule Activity** in the header of every My Activities view (List / Kanban / Graph). You pick the opportunity, activity type, due date, summary, assignee and a note. Choosing a type pre-fills the due date from the type's delay, plus its summary and default user (`ScheduleActivityAction::defaultsForType()`). Saving requires edit rights on that opportunity.
2. **+ on a My Activities kanban column**: the same form, pre-filled for that column's activity type.
3. **The clock icon on opportunity rows** (My Pipeline → List and the team pipeline): Chatter's own `ActivityTableAction`, which also offers the **CRM activity plans**. The Chatter panel on an opportunity works as before.

Activities are stored by the Chatter plugin. `Models\Activity` is a thin subclass of `Chatter\Models\Message`, scoped to `type = 'activity'` on `Lead`. It adds due-status scopes (`overdue()`, `dueToday()`, `planned()`, `dueStatus()`) and `markAsDone()`. That method does exactly what the Chatter panel does: it posts a "<type> done …" note on the opportunity and removes the activity. Activities on lost opportunities are hidden.

| View | URL | What it shows |
|---|---|---|
| **List** (default) | `/admin/crm/sales/activities` | Due date badge (Overdue red / Today orange / Planned green), opportunity, customer, type, summary, assignee. Views: My Activities, Overdue, Today, Planned, All Users. Row actions: ✓ Mark as Done (with feedback), Open Opportunity, Reschedule (date, summary, assignee), Cancel |
| **Kanban** | `/activities/kanban` | One column per activity type: CRM types plus any type already used on CRM activities. Each column shows its overdue count. Cards show opportunity, summary · date, status/assignee/customer badges, and ↗ open and ✓ done icons. Dragging changes the activity type. Filter: Assigned To (defaults to you) |
| **Graph** | `/activities/graph` | Overdue / Today / Planned / Total counts, a doughnut by due status, stacked bars by type, and bars by user. Defaults to your activities; **Show all users** switches the scope (`?scope=all`) |

Kanban footer actions without a URL didn't work in the package: the card echoes the action, which isn't registered on the page, so nothing happens. CRM's card view `resources/views/filament/kanban/card.blade.php` routes them through the board's own `kanbanCardFooter` action instead. Both CRM boards use it.

## Frontend assets

CRM ships its own compiled Tailwind CSS, like other AureusERP plugins: `resources/css/index.css` → `resources/dist/crm.css`, registered with `FilamentAsset`. The build scans CRM's views **and the kanban package views**, because the package relies on Tailwind utility classes that Filament's CSS doesn't include. It reuses `support/resources/css/colors.css` for the Filament color palettes.

After changing CRM views or upgrading the kanban package:

```bash
cd plugins/webkul/crm && npm install && npm run build
php artisan filament:assets      # copies dist files to public/css/crm, public/css|js/wezlo/filament-kanban
```

Commit `resources/dist/crm.css` and the published `public/` files (this repo tracks published assets).

## Files

```
plugins/webkul/crm
├── composer.json                          PSR-4 Webkul\Crm, provider registration
├── config/filament-shield.php             Shield resources and excluded cluster pages
├── database/migrations/                   crm_tags, crm_lost_reasons
├── database/factories/                    TagFactory, LostReasonFactory (archived state)
├── database/seeders/                      DatabaseSeeder, ActivityTypeSeeder, LostReasonSeeder
├── resources/lang/en/filament/clusters/   translations (mirrors the Filament folder layout)
├── tests/Feature/Filament/                Pest tests (see the warning in the Development Log)
└── src
    ├── CrmServiceProvider.php             package config, dependencies, seeder, install/uninstall
    ├── CrmPlugin.php                      admin panel discovery (only when installed)
    ├── Models/                            Tag, LostReason (CRM tables); ActivityType, ActivityPlan (CRM-scoped subclasses)
    ├── Policies/                          CRM permission checks
    └── Filament
        ├── Concerns/HasCrmAuthorization.php
        └── Clusters
            ├── Sales.php                  slug crm/sales
            ├── Sales/Resources/ContactResource (+ Pages)
            ├── Configurations.php         slug crm/configurations
            ├── Configurations/Pages/ManageLanguages.php
            └── Configurations/Resources/{Team,Tag,LostReason,ActivityType,ActivityPlan,User,Company}Resource (+ Pages, Schemas, Tables)
```