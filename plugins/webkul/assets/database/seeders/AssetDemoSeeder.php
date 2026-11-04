<?php

namespace Webkul\Asset\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetSiteType;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Employee\Database\Seeders\DepartmentSeeder;
use Webkul\Employee\Database\Seeders\EmployeeSeeder;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Seeders\CompanySeeder;
use Webkul\Support\Models\Company;

class AssetDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $company = Company::query()->first();
        if (! $company) {
            if (class_exists(CompanySeeder::class)) {
                $this->call(CompanySeeder::class);
            }
            $company = Company::query()->first();
        }

        if (! $company) {
            $company = Company::query()->create([
                'sort'                => 1,
                'name'                => 'Aureus Global Enterprises',
                'tax_id'              => 'TAX-001',
                'registration_number' => 'REG-001',
                'company_id'          => 'COMP001',
                'email'               => 'info@aureus.local',
                'is_active'           => true,
            ]);
        }

        $adminUser = User::query()->first();

        if (Employee::query()->where('is_active', true)->count() < 6) {
            if (class_exists(EmployeeSeeder::class)) {
                $this->call(EmployeeSeeder::class);
            }

            if (class_exists(DepartmentSeeder::class)) {
                $this->call(DepartmentSeeder::class);
            }
        }

        $seedEmployees = [
            ['name' => 'Paul Williams', 'work_email' => 'paul@example.com', 'job_title' => 'Experienced Developer'],
            ['name' => 'John Doe', 'work_email' => 'john@example.com', 'job_title' => 'Junior Developer'],
            ['name' => 'Jane Smith', 'work_email' => 'jane@example.com', 'job_title' => 'Project Manager'],
            ['name' => 'Ravi Kumar', 'work_email' => 'ravi@example.com', 'job_title' => 'Team Lead'],
            ['name' => 'Emily Davis', 'work_email' => 'emily@example.com', 'job_title' => 'QA Engineer'],
            ['name' => 'Michael Brown', 'work_email' => 'michael@example.com', 'job_title' => 'UX Designer'],
        ];

        foreach ($seedEmployees as $data) {
            Employee::query()->firstOrCreate(
                ['work_email' => $data['work_email']],
                [
                    'name'          => $data['name'],
                    'job_title'     => $data['job_title'],
                    'company_id'    => $company->id,
                    'creator_id'    => $adminUser?->id,
                    'is_active'     => true,
                    'employee_type' => 'employee',
                    'time_zone'     => 'UTC',
                ]
            );
        }

        Employee::query()
            ->where('is_active', true)
            ->whereIn('work_email', collect($seedEmployees)->pluck('work_email'))
            ->where(fn ($q) => $q->whereNull('company_id')->orWhere('company_id', '!=', $company->id))
            ->update(['company_id' => $company->id]);

        $devDept = Department::query()->where('company_id', $company->id)->where('name', 'Research & Development')->first()
            ?? Department::query()->where('name', 'Research & Development')->first();
        $mgmtDept = Department::query()->where('company_id', $company->id)->where('name', 'Management')->first()
            ?? Department::query()->where('name', 'Management')->first();
        $projDept = Department::query()->where('company_id', $company->id)->where('name', 'Long Term Projects')->first()
            ?? Department::query()->where('name', 'Long Term Projects')->first();

        if ($devDept) {
            Employee::query()->whereIn('work_email', ['paul@example.com', 'john@example.com', 'emily@example.com'])
                ->whereNull('department_id')
                ->update(['department_id' => $devDept->id]);
        }

        if ($mgmtDept) {
            Employee::query()->where('work_email', 'jane@example.com')
                ->whereNull('department_id')
                ->update(['department_id' => $mgmtDept->id]);
        }

        if ($projDept) {
            Employee::query()->where('work_email', 'ravi@example.com')
                ->whereNull('department_id')
                ->update(['department_id' => $projDept->id]);
        }

        $paul = Employee::query()->where('work_email', 'paul@example.com')->first();
        $john = Employee::query()->where('work_email', 'john@example.com')->first();
        $jane = Employee::query()->where('work_email', 'jane@example.com')->first();
        $ravi = Employee::query()->where('work_email', 'ravi@example.com')->first();
        $emily = Employee::query()->where('work_email', 'emily@example.com')->first();
        $michael = Employee::query()->where('work_email', 'michael@example.com')->first();

        $activeEmployees = Employee::query()
            ->where('is_active', true)
            ->where('company_id', $company->id)
            ->get();

        $paul = $paul ?? $activeEmployees->get(0);
        $john = $john ?? $activeEmployees->get(1) ?? $paul;
        $jane = $jane ?? $activeEmployees->get(2) ?? $paul;
        $ravi = $ravi ?? $activeEmployees->get(3) ?? $paul;
        $emily = $emily ?? $activeEmployees->get(4) ?? $paul;
        $michael = $michael ?? $activeEmployees->get(5) ?? $paul;

        AssetMovement::query()->where('company_id', $company->id)->delete();
        Asset::withTrashed()->where('company_id', $company->id)->forceDelete();
        AssetProduct::withTrashed()->where('company_id', $company->id)->forceDelete();
        AssetWarehouse::withTrashed()->where('company_id', $company->id)->forceDelete();
        AssetSite::withTrashed()->where('company_id', $company->id)->update(['parent_id' => null]);
        AssetSite::withTrashed()->where('company_id', $company->id)->forceDelete();

        $settings = app(AssetSettings::class);
        $settings->tag_prefix = 'AST-';
        $settings->auto_generate_tag = true;
        $settings->tag_padding = 5;
        $settings->default_condition = 'good';
        $settings->require_serial_by_default = true;
        $settings->enable_site_assets = true;
        $settings->require_site_on_custody = false;
        $settings->default_custody_duration_days = null;
        $settings->require_expected_return_date = false;
        $settings->prevent_duplicate_product_custody = false;
        $settings->require_attachments_on_custody = false;
        $settings->require_attachments_on_damage = false;
        $settings->require_attachments_on_termination = false;
        $settings->restrict_termination_to_managers = false;
        $settings->termination_approval_cost_threshold = null;

        $centralWarehouse = AssetWarehouse::query()->create([
            'company_id'            => $company->id,
            'name'                  => 'Central IT Warehouse (Riyadh HQ)',
            'code'                  => 'WH-RUH-01',
            'custodian_employee_id' => $paul?->id,
            'is_active'             => true,
            'note'                  => 'Main temperature-controlled technical storage and distribution hub.',
        ]);

        $jeddahWarehouse = AssetWarehouse::query()->create([
            'company_id'            => $company->id,
            'name'                  => 'Jeddah Regional Logistics Depot',
            'code'                  => 'WH-JED-01',
            'custodian_employee_id' => $john?->id,
            'is_active'             => true,
            'note'                  => 'Regional depot servicing Western province projects and offices.',
        ]);

        $scrapWarehouse = AssetWarehouse::query()->create([
            'company_id'            => $company->id,
            'name'                  => 'Transit & Decommission Bay',
            'code'                  => 'WH-SCRAP-01',
            'custodian_employee_id' => $jane?->id,
            'is_active'             => true,
            'note'                  => 'Holding bay for damaged, inspection-pending, or EOL decommissioned hardware.',
        ]);

        $settings->default_warehouse_id = $centralWarehouse->id;
        $settings->save();

        $hqType = AssetSiteType::query()->firstOrCreate(['name' => 'Headquarters']);
        $branchType = AssetSiteType::query()->firstOrCreate(['name' => 'Branch Office']);
        $remoteType = AssetSiteType::query()->firstOrCreate(['name' => 'Remote Project']);

        $riyadhHqSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'site_type_id'                  => $hqType->id,
            'name'                          => 'Riyadh Headquarters Complex',
            'code'                          => 'RUH-HQ',
            'usage_type'                    => AssetSiteUsage::AssetLocation,
            'default_custodian_employee_id' => $paul?->id,
            'is_active'                     => true,
            'note'                          => 'Corporate executive complex, core data center, and technical facilities.',
        ]);

        $dataCenterSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'parent_id'                     => $riyadhHqSite->id,
            'site_type_id'                  => $hqType->id,
            'name'                          => 'Main Data Center & Server Room',
            'code'                          => 'RUH-DC01',
            'default_custodian_employee_id' => $paul?->id,
            'is_active'                     => true,
            'note'                          => 'Tier-3 enterprise server room housing network backbones and core compute racks.',
        ]);

        $infrastructureSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'parent_id'                     => $riyadhHqSite->id,
            'site_type_id'                  => $hqType->id,
            'name'                          => 'Technical Infrastructure & Network Closets',
            'code'                          => 'RUH-NET01',
            'default_custodian_employee_id' => $john?->id,
            'is_active'                     => true,
            'note'                          => 'Core wiring closets, telecommunication racks, and campus network switches.',
        ]);

        $riyadhWorkspaceSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'site_type_id'                  => $hqType->id,
            'name'                          => 'Riyadh HQ Corporate Workspaces',
            'code'                          => 'RUH-WS',
            'usage_type'                    => AssetSiteUsage::CustodyZone,
            'default_custodian_employee_id' => $paul?->id,
            'is_active'                     => true,
            'note'                          => 'Designated employee custody zone for HQ personnel and software engineering floors.',
        ]);

        $engineeringFloorSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'parent_id'                     => $riyadhWorkspaceSite->id,
            'site_type_id'                  => $hqType->id,
            'name'                          => 'Floor 3 - Engineering & IT Wing',
            'code'                          => 'RUH-FL03',
            'default_custodian_employee_id' => $john?->id,
            'is_active'                     => true,
            'note'                          => 'Software engineering, design studios, and technical team custody zone.',
        ]);

        $jeddahBranchSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'site_type_id'                  => $branchType->id,
            'name'                          => 'Jeddah Commercial Center',
            'code'                          => 'JED-BR',
            'usage_type'                    => AssetSiteUsage::AssetLocation,
            'default_custodian_employee_id' => $john?->id,
            'is_active'                     => true,
            'note'                          => 'Western province regional commercial and operations center.',
        ]);

        $jeddahServerSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'parent_id'                     => $jeddahBranchSite->id,
            'site_type_id'                  => $branchType->id,
            'name'                          => 'Server Room & Local Network Hub',
            'code'                          => 'JED-SRV01',
            'default_custodian_employee_id' => $john?->id,
            'is_active'                     => true,
            'note'                          => 'Branch network and local server distribution hub.',
        ]);

        $jeddahWorkspaceSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'site_type_id'                  => $branchType->id,
            'name'                          => 'Jeddah Regional Office Workspaces',
            'code'                          => 'JED-WS',
            'usage_type'                    => AssetSiteUsage::CustodyZone,
            'default_custodian_employee_id' => $jane?->id,
            'is_active'                     => true,
            'note'                          => 'Western province personnel and operations team workspace zone.',
        ]);

        $jeddahOpsSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'parent_id'                     => $jeddahWorkspaceSite->id,
            'site_type_id'                  => $branchType->id,
            'name'                          => 'Floor 2 - Operations & Logistics Hall',
            'code'                          => 'JED-OPS02',
            'default_custodian_employee_id' => $jane?->id,
            'is_active'                     => true,
            'note'                          => 'Regional operations management and field logistics custody zone.',
        ]);

        $neomFieldSite = AssetSite::query()->create([
            'company_id'                    => $company->id,
            'site_type_id'                  => $remoteType->id,
            'name'                          => 'NEOM Sector 4 Field Exploration Zone',
            'code'                          => 'NEOM-S4',
            'usage_type'                    => AssetSiteUsage::CustodyZone,
            'default_custodian_employee_id' => $ravi?->id,
            'is_active'                     => true,
            'note'                          => 'Dedicated custody handover zone for rough-terrain exploration gear and field teams.',
        ]);

        $itCat = AssetCategory::query()->firstOrCreate(['name' => 'IT Equipment']);
        $laptopsCat = AssetCategory::query()->firstOrCreate(['name' => 'Laptops', 'parent_id' => $itCat->id]);
        $desktopsCat = AssetCategory::query()->firstOrCreate(['name' => 'Desktops & Workstations', 'parent_id' => $itCat->id]);
        $monitorsCat = AssetCategory::query()->firstOrCreate(['name' => 'Monitors & Displays', 'parent_id' => $itCat->id]);
        $networkingCat = AssetCategory::query()->firstOrCreate(['name' => 'Networking & Infrastructure', 'parent_id' => $itCat->id]);

        $officeCat = AssetCategory::query()->firstOrCreate(['name' => 'Office Equipment']);
        $printersCat = AssetCategory::query()->firstOrCreate(['name' => 'Printers & Multi-Function', 'parent_id' => $officeCat->id]);

        $furnitureCat = AssetCategory::query()->firstOrCreate(['name' => 'Furniture']);
        $chairsCat = AssetCategory::query()->firstOrCreate(['name' => 'Ergonomic Seating', 'parent_id' => $furnitureCat->id]);

        $vehiclesCat = AssetCategory::query()->firstOrCreate(['name' => 'Vehicles']);
        $fieldVehiclesCat = AssetCategory::query()->firstOrCreate(['name' => 'Field Utility Vehicles', 'parent_id' => $vehiclesCat->id]);

        $mbp16 = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $laptopsCat->id,
            'name'        => 'MacBook Pro 16" M3 Max',
            'model'       => 'Apple M3 Max (16-core CPU, 40-core GPU) / 36GB Unified / 1TB SSD',
            'cost'        => 14500.00,
            'is_active'   => true,
            'note'        => 'Standard high-performance development and architectural design workstation.',
        ]);

        $xps15 = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $laptopsCat->id,
            'name'        => 'Dell XPS 15 9530',
            'model'       => 'Intel Core i7-13700H / 32GB DDR5 / 1TB NVMe / RTX 4060',
            'cost'        => 8200.00,
            'is_active'   => true,
            'note'        => 'Senior management and enterprise mobility laptop.',
        ]);

        $thinkpadT14 = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $laptopsCat->id,
            'name'        => 'Lenovo ThinkPad T14s Gen 4',
            'model'       => 'AMD Ryzen 7 PRO 7840U / 16GB LPDDR5x / 512GB SSD',
            'cost'        => 5400.00,
            'is_active'   => true,
            'note'        => 'Standard engineering fleet and operational laptop.',
        ]);

        $macStudio = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $desktopsCat->id,
            'name'        => 'Mac Studio M2 Ultra',
            'model'       => 'Apple M2 Ultra / 64GB RAM / 2TB SSD',
            'cost'        => 18000.00,
            'is_active'   => true,
            'note'        => 'AI modeling, media production, and simulation lab workstation.',
        ]);

        $dellMonitor32 = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $monitorsCat->id,
            'name'        => 'Dell UltraSharp 32" 4K USB-C Hub Monitor',
            'model'       => 'U3223QE IPS Black / 90W Power Delivery / RJ45',
            'cost'        => 3100.00,
            'is_active'   => true,
            'note'        => 'Color-calibrated dual display standard.',
        ]);

        $studioDisplay = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $monitorsCat->id,
            'name'        => 'Apple Studio Display 27" 5K Retina',
            'model'       => 'MK0U3LL/A Standard Glass / Tilt-Adjustable Stand',
            'cost'        => 6800.00,
            'is_active'   => true,
            'note'        => 'Executive suite 5K Retina display.',
        ]);

        $ciscoSwitch = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $networkingCat->id,
            'name'        => 'Cisco Catalyst 9200L 48-Port PoE+ Switch',
            'model'       => 'C9200L-48P-4G-E Layer 3 Gigabit Managed Switch',
            'cost'        => 11500.00,
            'is_active'   => true,
            'note'        => 'Core distribution and edge access network hardware.',
        ]);

        $hpLaserJet = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $printersCat->id,
            'name'        => 'HP LaserJet Enterprise MFP M528dn',
            'model'       => '1PV64A Monochromatic Multifunction Network Printer',
            'cost'        => 4200.00,
            'is_active'   => true,
            'note'        => 'High-volume departmental network multifunction printer.',
        ]);

        $aeronChair = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $chairsCat->id,
            'name'        => 'Herman Miller Aeron Chair',
            'model'       => 'AERON-B-GRAPHITE / PostureFit SL / Fully Adjustable Arms',
            'cost'        => 4800.00,
            'is_active'   => true,
            'note'        => 'Ergonomic task chair for software engineers and executives.',
        ]);

        $toyotaHilux = AssetProduct::query()->create([
            'company_id'  => $company->id,
            'category_id' => $fieldVehiclesCat->id,
            'name'        => 'Toyota Hilux Double Cab 4x4',
            'model'       => '2024 Diesel 2.8L Turbo / 4WD / Automatic Transmission',
            'cost'        => 118000.00,
            'is_active'   => true,
            'note'        => 'Heavy-duty remote site exploration and engineering field vehicle.',
        ]);

        $service = app(AssetCustodyService::class);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $mbp16->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-MBP16-1001',
                    'asset_tag' => 'AST-00001',
                    'condition' => AssetCondition::New->value,
                    'cost'      => 14500.00,
                    'note'      => 'Factory sealed stock for Q4 technical onboardings.',
                ],
                [
                    'serial_no' => 'SN-MBP16-1002',
                    'asset_tag' => 'AST-00002',
                    'condition' => AssetCondition::New->value,
                    'cost'      => 14500.00,
                    'note'      => 'Reserved for Lead Cloud Architect joining in November.',
                ],
            ],
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $dellMonitor32->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-U32-2001',
                    'asset_tag' => 'AST-00003',
                    'condition' => AssetCondition::New->value,
                    'cost'      => 3100.00,
                    'note'      => 'Central warehouse display buffer inventory.',
                ],
                [
                    'serial_no' => 'SN-U32-2002',
                    'asset_tag' => 'AST-00004',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 3100.00,
                    'note'      => 'Opened box demo unit, excellent condition.',
                ],
            ],
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $aeronChair->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-HM-CHAIR-01',
                    'asset_tag' => 'AST-00005',
                    'condition' => AssetCondition::New->value,
                    'cost'      => 4800.00,
                    'note'      => 'Unassembled ergonomic chair in original packaging.',
                ],
            ],
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $mbp16->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-MBP16-9901',
                    'asset_tag' => 'AST-00006',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 14500.00,
                    'note'      => 'Primary workstation assigned to Lead Architect.',
                ],
            ],
        ]);
        $assetMbpIssued = Asset::query()->where('serial_no', 'SN-MBP16-9901')->firstOrFail();
        $service->issue($assetMbpIssued, [
            'allocation_mode'    => 'employee_custody',
            'employee_id'        => $paul?->id,
            'site_id'            => $engineeringFloorSite->id,
            'condition_out'      => AssetCondition::Good,
            'issued_at'          => now()->subMonths(2),
            'expected_return_at' => now()->addMonths(10),
            'issue_note'         => 'Assigned for enterprise architectural development and production deployments.',
            'issue_attachments'  => ['custody_handover_signed_paul.pdf'],
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $thinkpadT14->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-TPT14-4412',
                    'asset_tag' => 'AST-00007',
                    'condition' => AssetCondition::New->value,
                    'cost'      => 5400.00,
                    'note'      => 'Standard fleet laptop.',
                ],
            ],
        ]);
        $assetThinkpadIssued = Asset::query()->where('serial_no', 'SN-TPT14-4412')->firstOrFail();
        $service->issue($assetThinkpadIssued, [
            'allocation_mode'    => 'employee_custody',
            'employee_id'        => $john?->id,
            'site_id'            => $engineeringFloorSite->id,
            'condition_out'      => AssetCondition::New,
            'issued_at'          => now()->subMonth(),
            'expected_return_at' => now()->addMonths(11),
            'issue_note'         => 'Full-stack software developer equipment allocation.',
            'issue_attachments'  => ['hardware_receipt_johndoe.pdf'],
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $xps15->id,
            'warehouse_id'  => $jeddahWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-XPS15-7721',
                    'asset_tag' => 'AST-00008',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 8200.00,
                    'note'      => 'Regional manager high-performance mobility laptop.',
                ],
            ],
        ]);
        $assetXpsIssued = Asset::query()->where('serial_no', 'SN-XPS15-7721')->firstOrFail();
        $service->issue($assetXpsIssued, [
            'allocation_mode'    => 'employee_custody',
            'employee_id'        => $jane?->id,
            'site_id'            => $jeddahOpsSite->id,
            'condition_out'      => AssetCondition::Good,
            'issued_at'          => now()->subWeeks(3),
            'expected_return_at' => now()->addMonths(5),
            'issue_note'         => 'Assigned to Operations Manager for Western province facility audits.',
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $studioDisplay->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-ASD27-3310',
                    'asset_tag' => 'AST-00009',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 6800.00,
                    'note'      => 'High-definition 5K display unit.',
                ],
            ],
        ]);
        $assetStudioDisplay = Asset::query()->where('serial_no', 'SN-ASD27-3310')->firstOrFail();
        $service->issue($assetStudioDisplay, [
            'allocation_mode' => 'employee_custody',
            'employee_id'     => $john?->id,
            'site_id'         => $engineeringFloorSite->id,
            'issued_at'       => now()->subMonths(3),
            'issue_note'      => 'Initial allocation to Frontend Engineering department.',
        ]);
        $service->transfer($assetStudioDisplay, [
            'allocation_mode' => 'employee_custody',
            'employee_id'     => $paul?->id,
            'site_id'         => $engineeringFloorSite->id,
            'issued_at'       => now()->subWeek(),
            'note'            => 'Departmental reallocation: transferred to Chief Architect office setup.',
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $ciscoSwitch->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-CSC92-00441',
                    'asset_tag' => 'AST-00010',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 11500.00,
                    'note'      => 'Network backbone switch.',
                ],
            ],
        ]);
        $assetCisco = Asset::query()->where('serial_no', 'SN-CSC92-00441')->firstOrFail();
        $service->issue($assetCisco, [
            'allocation_mode' => 'site_asset',
            'site_id'         => $dataCenterSite->id,
            'issued_at'       => now()->subMonths(4),
            'issue_note'      => 'Core rack switch permanently mounted in Main Data Center Server Rack B2.',
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $hpLaserJet->id,
            'warehouse_id'  => $jeddahWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-HPLJ-88120',
                    'asset_tag' => 'AST-00011',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 4200.00,
                    'note'      => 'Shared office laser multifunction printer.',
                ],
            ],
        ]);
        $assetHp = Asset::query()->where('serial_no', 'SN-HPLJ-88120')->firstOrFail();
        $service->issue($assetHp, [
            'allocation_mode' => 'site_asset',
            'site_id'         => $jeddahServerSite->id,
            'issued_at'       => now()->subMonths(2),
            'issue_note'      => 'Shared network laser printer deployed to Jeddah regional facility.',
        ]);

        $service->bulkRegister([
            'company_id'        => $company->id,
            'product_id'        => $ciscoSwitch->id,
            'initial_placement' => 'site_asset',
            'site_id'           => $infrastructureSite->id,
            'tracking_mode'     => 'with_serials',
            'items'             => [
                [
                    'serial_no' => 'SN-CSC92-FL03-01',
                    'asset_tag' => 'AST-00012',
                    'condition' => AssetCondition::New->value,
                    'cost'      => 11500.00,
                    'note'      => 'Edge distribution switch installed directly in network infrastructure closet.',
                ],
            ],
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $ciscoSwitch->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-CSC92-XFER-01',
                    'asset_tag' => 'AST-00013',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 11500.00,
                    'note'      => 'Secondary distribution switch for data center expansion.',
                ],
            ],
        ]);
        $switchXfer = Asset::query()->where('serial_no', 'SN-CSC92-XFER-01')->firstOrFail();
        $service->issue($switchXfer, [
            'allocation_mode' => 'site_asset',
            'site_id'         => $dataCenterSite->id,
            'issued_at'       => now()->subMonths(2),
            'issue_note'      => 'Initial deployment to Main Data Center staging rack.',
        ]);
        $service->transfer($switchXfer, [
            'allocation_mode' => 'site_asset',
            'site_id'         => $infrastructureSite->id,
            'issued_at'       => now()->subWeeks(2),
            'note'            => 'Reallocated from Data Center to Network Closets for high-speed network expansion.',
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $toyotaHilux->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'VIN-TY8492049102',
                    'asset_tag' => 'AST-00014',
                    'condition' => AssetCondition::New->value,
                    'cost'      => 118000.00,
                    'note'      => 'Field utility pickup registered with commercial plate # 4921-KSA.',
                ],
            ],
        ]);
        $assetHilux = Asset::query()->where('serial_no', 'VIN-TY8492049102')->firstOrFail();
        $service->issue($assetHilux, [
            'allocation_mode'    => 'employee_custody',
            'site_id'            => $neomFieldSite->id,
            'employee_id'        => $ravi?->id,
            'condition_out'      => AssetCondition::New,
            'issued_at'          => now()->subMonths(1),
            'expected_return_at' => now()->addMonths(12),
            'issue_note'         => 'Deployed to NEOM Sector 4 Field Base under Lead Site Engineer custody.',
            'issue_attachments'  => ['vehicle_field_dispatch_order.pdf'],
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $macStudio->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-MS2-00812',
                    'asset_tag' => 'AST-00015',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 18000.00,
                    'note'      => 'Heavy computation laboratory workstation.',
                ],
            ],
        ]);
        $assetMacStudio = Asset::query()->where('serial_no', 'SN-MS2-00812')->firstOrFail();
        $service->issue($assetMacStudio, [
            'allocation_mode' => 'employee_custody',
            'employee_id'     => $emily?->id,
            'site_id'         => $engineeringFloorSite->id,
            'issued_at'       => now()->subMonths(5),
            'issue_note'      => 'Assigned for benchmark simulation project.',
        ]);
        $service->return($assetMacStudio, [
            'warehouse_id' => $centralWarehouse->id,
            'condition_in' => AssetCondition::Good,
            'ended_at'     => now()->subDays(5),
            'return_note'  => 'Simulation phase successfully concluded; unit safely returned to central inventory.',
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $thinkpadT14->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-TPT14-0099',
                    'asset_tag' => 'AST-00016',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 5400.00,
                    'note'      => 'Field survey unit.',
                ],
            ],
        ]);
        $assetDamaged = Asset::query()->where('serial_no', 'SN-TPT14-0099')->firstOrFail();
        $service->issue($assetDamaged, [
            'allocation_mode' => 'employee_custody',
            'employee_id'     => $ravi?->id,
            'site_id'         => $neomFieldSite->id,
            'issued_at'       => now()->subMonths(3),
            'issue_note'      => 'Assigned for rough terrain geotechnical inspections.',
        ]);
        $service->return($assetDamaged, [
            'warehouse_id'       => $scrapWarehouse->id,
            'condition_in'       => AssetCondition::Damaged,
            'ended_at'           => now()->subDays(2),
            'return_note'        => 'Device suffered physical impact; fractured LCD panel and damaged right hinge during field transit.',
            'return_attachments' => ['damage_inspection_report_screen_crack.jpg'],
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $xps15->id,
            'warehouse_id'  => $scrapWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-XPS-OLD-SCRAP',
                    'asset_tag' => 'AST-00017',
                    'condition' => AssetCondition::Damaged->value,
                    'cost'      => 7500.00,
                    'note'      => 'Legacy development unit with persistent liquid corrosion.',
                ],
            ],
        ]);
        $assetToScrap = Asset::query()->where('serial_no', 'SN-XPS-OLD-SCRAP')->firstOrFail();
        $service->scrap($assetToScrap, [
            'ended_at' => now()->subDay(),
            'note'     => 'Motherboard failure and corrosion beyond economical repair; approved for electronic waste recycling.',
        ]);

        $service->bulkRegister([
            'company_id'    => $company->id,
            'product_id'    => $dellMonitor32->id,
            'warehouse_id'  => $centralWarehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                [
                    'serial_no' => 'SN-U32-LOST-99',
                    'asset_tag' => 'AST-00018',
                    'condition' => AssetCondition::Good->value,
                    'cost'      => 3100.00,
                    'note'      => 'Portable presentation monitor.',
                ],
            ],
        ]);
        $assetToLose = Asset::query()->where('serial_no', 'SN-U32-LOST-99')->firstOrFail();
        $service->issue($assetToLose, [
            'allocation_mode' => 'employee_custody',
            'employee_id'     => $jane?->id,
            'site_id'         => $jeddahOpsSite->id,
            'issued_at'       => now()->subMonths(2),
            'issue_note'      => 'Checked out for external partner summit demonstration.',
        ]);
        $service->markLost($assetToLose, [
            'ended_at' => now()->subHours(12),
            'note'     => 'Stolen during conference transit in hotel parking area; official police theft incident report #99482 filed.',
        ]);

        $this->seedStandardRoles();
    }

    protected function seedStandardRoles(): void
    {
        $this->call(RolePermissionSeeder::class);
    }
}
