<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['code' => 'PROD001', 'name' => 'Agent & Category Management - Monthly Subscription', 'description' => 'This product includes features for managing agents and categories, allowing you to efficiently organize and oversee your sales channels and product classifications.  With this product, you can easily add, edit, and remove agents, as well as assign them to specific categories. This helps streamline your operations and ensures that your sales team is effectively managed.', 'price_per_location' => 49900, 'price_per_user' => 14970, 'tax_percentage' => 11, 'billing_cycle' => 'monthly', 'product_type' => 'single'],
            ['code' => 'PROD002', 'name' => 'Event', 'description' => 'Event module for planning and managing customer events', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD003', 'name' => 'Referral', 'description' => 'Referral tracking module for referrals and rewards', 'price_per_location' => 39900, 'price_per_user' => 11970],
            ['code' => 'PROD004', 'name' => 'Sales Leads', 'description' => 'Lead capturing and qualifying module for sales team', 'price_per_location' => 99000, 'price_per_user' => 29700],
            ['code' => 'PROD005', 'name' => 'Booking Reminder', 'description' => 'Automated booking reminder and notification module', 'price_per_location' => 99000, 'price_per_user' => 29700],
            ['code' => 'PROD006', 'name' => 'Complimentary', 'description' => 'Complimentary item management module', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD007', 'name' => 'Promotion', 'description' => 'Promotion campaign management and discounts', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD008', 'name' => 'Point Of Sales', 'description' => 'POS module for quick sales and checkout', 'price_per_location' => 129000, 'price_per_user' => 38700],
            ['code' => 'PROD009', 'name' => 'Sales Order', 'description' => 'Sales order processing module', 'price_per_location' => 199000, 'price_per_user' => 59700],
            ['code' => 'PROD010', 'name' => 'Sales Reward', 'description' => 'Sales rewards and incentive tracking module', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD011', 'name' => 'Sales Target', 'description' => 'Sales target planning and monitoring module', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD012', 'name' => 'Ticketing & Problem', 'description' => 'Ticketing and issue resolution system', 'price_per_location' => 129000, 'price_per_user' => 38700],
            ['code' => 'PROD013', 'name' => 'Task', 'description' => 'Task management and workflow module', 'price_per_location' => 79000, 'price_per_user' => 23700],
            ['code' => 'PROD014', 'name' => 'Service Usage', 'description' => 'Service usage monitoring and billing module', 'price_per_location' => 79000, 'price_per_user' => 23700],
            ['code' => 'PROD015', 'name' => 'Checklist Activity', 'description' => 'Activity checklist and compliance module', 'price_per_location' => 299000, 'price_per_user' => 89700],
            ['code' => 'PROD016', 'name' => 'Service Level Agreement', 'description' => 'SLA management and violation tracking', 'price_per_location' => 39900, 'price_per_user' => 11970],
            ['code' => 'PROD017', 'name' => 'Request Order', 'description' => 'Request order processing and approval', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD018', 'name' => 'Working Order', 'description' => 'Working order management module', 'price_per_location' => 129000, 'price_per_user' => 38700],
            ['code' => 'PROD019', 'name' => 'Bank Account', 'description' => 'Bank account management and reconciliation', 'price_per_location' => 39900, 'price_per_user' => 11970],
            ['code' => 'PROD020', 'name' => 'Cashier', 'description' => 'Cashier module for handling transactions', 'price_per_location' => 79000, 'price_per_user' => 49900],
            ['code' => 'PROD021', 'name' => 'Account Receivable - Billing Reminder', 'description' => 'AR billing reminders and follow-ups', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD022', 'name' => 'Account Receivable - Collection Reminder', 'description' => 'AR collection reminder functionality', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD023', 'name' => 'Account Receivable - Deposit', 'description' => 'Deposit handling in accounts receivable', 'price_per_location' => 79000, 'price_per_user' => 23700],
            ['code' => 'PROD024', 'name' => 'Account Receivable - Invoice', 'description' => 'Invoice generation and AR tracking', 'price_per_location' => 99000, 'price_per_user' => 38700],
            ['code' => 'PROD025', 'name' => 'Account Receivable - Payment', 'description' => 'Payment receiving and reconciliation', 'price_per_location' => 99000, 'price_per_user' => 38700],
            ['code' => 'PROD026', 'name' => 'Account Payable - Deposit', 'description' => 'AP deposit handling and workflows', 'price_per_location' => 79000, 'price_per_user' => 23700],
            ['code' => 'PROD027', 'name' => 'Account Payable - Invoice', 'description' => 'AP invoice tracking and approval', 'price_per_location' => 99000, 'price_per_user' => 38700],
            ['code' => 'PROD028', 'name' => 'Account Payable - Payment', 'description' => 'AP payment processing and capture', 'price_per_location' => 99000, 'price_per_user' => 38700],
            ['code' => 'PROD029', 'name' => 'Chart of Account', 'description' => 'General chart of account module', 'price_per_location' => 129000, 'price_per_user' => 28700],
            ['code' => 'PROD030', 'name' => 'Fiscal Period', 'description' => 'Fiscal period setup and reports', 'price_per_location' => 99000, 'price_per_user' => 38700],
            ['code' => 'PROD031', 'name' => 'General Ledger', 'description' => 'General ledger recording and reporting', 'price_per_location' => 79000, 'price_per_user' => 23700],
            ['code' => 'PROD032', 'name' => 'Cash Flow', 'description' => 'Cash flow management and forecasting', 'price_per_location' => 79000, 'price_per_user' => 23700],
            ['code' => 'PROD033', 'name' => 'Financial Report', 'description' => 'Financial reporting and analysis module', 'price_per_location' => 129000, 'price_per_user' => 38700],
            ['code' => 'PROD034', 'name' => 'Financial Dashboard', 'description' => 'Financial performance dashboard and KPIs', 'price_per_location' => 99000, 'price_per_user' => 38700],
            ['code' => 'PROD035', 'name' => 'Inventory Management', 'description' => 'Inventory tracking and stock level management', 'price_per_location' => 129000, 'price_per_user' => 38700],
            ['code' => 'PROD036', 'name' => 'Material Management', 'description' => 'Material and component inventory tracking', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD037', 'name' => 'Shipping Cost Management', 'description' => 'Shipping costs and carrier optimization', 'price_per_location' => 49900, 'price_per_user' => 14970],
            ['code' => 'PROD038', 'name' => 'Stock Opname', 'description' => 'Stock opname and physical count management', 'price_per_location' => 79000, 'price_per_user' => 23700],
            ['code' => 'PROD039', 'name' => 'Purchase Order', 'description' => 'Purchase order creation and tracking', 'price_per_location' => 129000, 'price_per_user' => 38700],
            ['code' => 'PROD040', 'name' => 'Delivery Order', 'description' => 'Delivery order management', 'price_per_location' => 79000, 'price_per_user' => 23700],
            ['code' => 'PROD041', 'name' => 'Received Order', 'description' => 'Received order and warehouse receiving process', 'price_per_location' => 79000, 'price_per_user' => 23700],
            ['code' => 'PROD042', 'name' => 'Stock Moving Order', 'description' => 'Stock movement ordering and transfer', 'price_per_location' => 79000, 'price_per_user' => 23700],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']],
                array_merge($product, [
                    'tax_percentage' => $product['tax_percentage'] ?? 11,
                    'billing_cycle' => $product['billing_cycle'] ?? 'monthly',
                    'product_type' => $product['product_type'] ?? 'single',
                    'description' => $product['description'] ?? 'No description yet',
                ])
            );
        }

        $arrayOfProductCodes = array('PROD008', 'PROD019', 'PROD020', 'PROD024', 'PROD025');

        $pointOfSalesProduct = Product::updateOrCreate(
            ['code' => 'PROD_BUNDLE_001'],
            [
                'name' => 'RAKOMSIS POS',
                'description' => 'A comprehensive point of sale solution for managing sales, inventory, and customer relationships.',
                'price_per_location' => 499000,
                'price_per_user' => 149700,
                'tax_percentage' => 11,
                'billing_cycle' => 'monthly',
                'product_type' => 'bundle',
            ]
        );

        $pointOfSalesProduct->includedProducts()->sync(Product::whereIn('code', $arrayOfProductCodes)->pluck('id')->toArray());
    }
}
