<?php

namespace Webkul\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Product\Enums\AttributeType;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\AttributeOption;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class SampleDataSeeder extends Seeder
{
    protected ?int $companyId = null;

    protected ?int $userId = null;

    protected ?int $uomId = null;

    /**
     * Cache of category name => id, seeded on demand for the IT catalog.
     */
    protected array $categories = [];

    /**
     * A curated catalog of realistic IT / electronic products (laptops,
     * tablets, RAM, SSDs, peripherals, networking and IT services) so the
     * demo data looks production-like.
     *
     * Optional demo/sample data intended for development environments only.
     */
    public function run(): void
    {
        if (Product::query()->exists()) {
            $this->command?->warn('Products already exist — skipping product demo data.');

            return;
        }

        $this->companyId = Company::query()->value('id');
        $this->userId = User::query()->value('id');
        $this->uomId = UOM::query()->where('name', 'Units')->value('id')
            ?? UOM::query()->value('id');

        $this->seedCategories();

        $this->seedSimpleProducts();

        $this->seedConfigurableProducts();

        $count = Product::query()->whereNull('parent_id')->count();
        $variants = Product::query()->whereNotNull('parent_id')->count();

        $this->command?->info("Created {$count} demo products ({$variants} variants).");
    }

    /**
     * Create the IT-themed category tree under the existing "All" root.
     */
    protected function seedCategories(): void
    {
        $root = Category::query()->whereNull('parent_id')->orderBy('id')->first();

        foreach (['Computers', 'Components', 'Peripherals', 'Networking', 'IT Services'] as $name) {
            $category = Category::create([
                'name'       => $name,
                'parent_id'  => $root?->id,
                'creator_id' => $this->userId,
            ]);

            $this->categories[$name] = $category->id;
        }

        $this->categories['All'] = $root?->id;
    }

    /**
     * Straightforward, single-variant products.
     */
    protected function seedSimpleProducts(): void
    {
        // [name, reference, category, type, price, cost]
        $catalog = [
            // Computers
            ['Dell XPS 15 Laptop', 'IT-LAP-0001', 'Computers', ProductType::GOODS, 1499.00, 1100.00],
            ['MacBook Pro 14"', 'IT-LAP-0002', 'Computers', ProductType::GOODS, 1999.00, 1500.00],
            ['Lenovo ThinkPad X1', 'IT-LAP-0003', 'Computers', ProductType::GOODS, 1299.00, 950.00],
            ['iPad Air', 'IT-TAB-0001', 'Computers', ProductType::GOODS, 599.00, 450.00],
            ['iPad Pro 12.9"', 'IT-TAB-0002', 'Computers', ProductType::GOODS, 1099.00, 850.00],
            ['Mac Mini M2', 'IT-DSK-0001', 'Computers', ProductType::GOODS, 699.00, 520.00],

            // Components
            ['16GB DDR5 RAM Module', 'IT-RAM-0001', 'Components', ProductType::GOODS, 89.00, 55.00],
            ['32GB DDR5 RAM Module', 'IT-RAM-0002', 'Components', ProductType::GOODS, 159.00, 100.00],
            ['1TB NVMe SSD', 'IT-SSD-0001', 'Components', ProductType::GOODS, 129.00, 80.00],
            ['2TB NVMe SSD', 'IT-SSD-0002', 'Components', ProductType::GOODS, 219.00, 150.00],
            ['RTX 4070 Graphics Card', 'IT-GPU-0001', 'Components', ProductType::GOODS, 649.00, 500.00],
            ['Intel Core i7 Processor', 'IT-CPU-0001', 'Components', ProductType::GOODS, 399.00, 300.00],

            // Peripherals
            ['27" 4K Monitor', 'IT-MON-0001', 'Peripherals', ProductType::GOODS, 449.00, 320.00],
            ['Mechanical Keyboard', 'IT-KEY-0001', 'Peripherals', ProductType::GOODS, 119.00, 70.00],
            ['Wireless Mouse', 'IT-MOU-0001', 'Peripherals', ProductType::GOODS, 49.00, 25.00],
            ['USB-C Docking Station', 'IT-DOC-0001', 'Peripherals', ProductType::GOODS, 199.00, 130.00],
            ['1080p Webcam', 'IT-CAM-0001', 'Peripherals', ProductType::GOODS, 79.00, 45.00],

            // Networking
            ['Wi-Fi 6 Router', 'IT-NET-0001', 'Networking', ProductType::GOODS, 179.00, 110.00],
            ['8-Port Gigabit Switch', 'IT-NET-0002', 'Networking', ProductType::GOODS, 89.00, 55.00],

            // IT Services
            ['IT Support (Hourly)', 'IT-SRV-0001', 'IT Services', ProductType::SERVICE, 75.00, 0.00],
            ['Device Setup & Configuration', 'IT-SRV-0002', 'IT Services', ProductType::SERVICE, 120.00, 0.00],
            ['Network Installation', 'IT-SRV-0003', 'IT Services', ProductType::SERVICE, 350.00, 0.00],
            ['Cloud Migration Consulting', 'IT-SRV-0004', 'IT Services', ProductType::SERVICE, 200.00, 0.00],
        ];

        foreach ($catalog as [$name, $reference, $category, $type, $price, $cost]) {
            $this->makeProduct($name, $reference, $category, $type, $price, $cost);
        }
    }

    /**
     * Configurable products with attributes and auto-generated variants,
     * reusing the application's own Product::generateVariants() logic.
     */
    protected function seedConfigurableProducts(): void
    {
        $ram = $this->makeAttribute('RAM', AttributeType::RADIO, [
            ['8 GB', null, 0.00],
            ['16 GB', null, 150.00],
            ['32 GB', null, 350.00],
        ]);

        $storage = $this->makeAttribute('Storage', AttributeType::SELECT, [
            ['512 GB', null, 0.00],
            ['1 TB', null, 120.00],
            ['2 TB', null, 300.00],
        ]);

        $color = $this->makeAttribute('Color', AttributeType::COLOR, [
            ['Silver', '#C0C0C0', 0.00],
            ['Space Grey', '#4A4A4A', 0.00],
            ['Midnight', '#191970', 0.00],
        ]);

        // Business Laptop — RAM x Storage => 9 variants.
        $laptop = $this->makeProduct('Business Laptop', 'IT-LAP-0100', 'Computers', ProductType::GOODS, 999.00, 700.00);
        $this->attachAttribute($laptop, $ram);
        $this->attachAttribute($laptop, $storage);
        $laptop->generateVariants();

        // Tablet Pro — Storage x Color => 9 variants.
        $tablet = $this->makeProduct('Tablet Pro', 'IT-TAB-0100', 'Computers', ProductType::GOODS, 649.00, 480.00);
        $this->attachAttribute($tablet, $storage);
        $this->attachAttribute($tablet, $color);
        $tablet->generateVariants();
    }

    protected function makeProduct(string $name, string $reference, string $category, ProductType $type, float $price, float $cost): Product
    {
        return Product::create([
            'type'            => $type,
            'name'            => $name,
            'reference'       => $reference,
            'price'           => $price,
            'cost'            => $cost,
            'enable_sales'    => true,
            'enable_purchase' => $type === ProductType::GOODS,
            'category_id'     => $this->resolveCategory($category),
            'uom_id'          => $this->uomId,
            'uom_po_id'       => $this->uomId,
            'company_id'      => $this->companyId,
            'creator_id'      => $this->userId,
        ]);
    }

    /**
     * Create an attribute together with its options.
     *
     * @param  array<int, array{0:string,1:?string,2:float}>  $options  [name, color, extraPrice]
     */
    protected function makeAttribute(string $name, AttributeType $type, array $options): Attribute
    {
        $attribute = Attribute::create([
            'name'       => $name,
            'type'       => $type,
            'creator_id' => $this->userId,
        ]);

        foreach ($options as $sort => [$optionName, $color, $extraPrice]) {
            AttributeOption::create([
                'attribute_id' => $attribute->id,
                'name'         => $optionName,
                'color'        => $color,
                'extra_price'  => $extraPrice,
                'sort'         => $sort + 1,
                'creator_id'   => $this->userId,
            ]);
        }

        return $attribute->load('options');
    }

    /**
     * Link an attribute (and all of its options) to a configurable product so
     * generateVariants() can build the combinations.
     */
    protected function attachAttribute(Product $product, Attribute $attribute): void
    {
        $productAttribute = ProductAttribute::create([
            'product_id'   => $product->id,
            'attribute_id' => $attribute->id,
            'creator_id'   => $this->userId,
        ]);

        foreach ($attribute->options as $option) {
            ProductAttributeValue::create([
                'product_id'           => $product->id,
                'attribute_id'         => $attribute->id,
                'product_attribute_id' => $productAttribute->id,
                'attribute_option_id'  => $option->id,
                'extra_price'          => $option->extra_price,
            ]);
        }
    }

    /**
     * Resolve a category id by name, falling back to the "All" root so a
     * product is never left without a category.
     */
    protected function resolveCategory(string $name): ?int
    {
        return $this->categories[$name]
            ?? $this->categories['All']
            ?? Category::query()->value('id');
    }
}
