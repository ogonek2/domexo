<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAdsPurchaseConversionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'name' => 'TestCategory',
            'is_active' => true,
        ]);
    }

    public function test_thank_you_page_sends_google_ads_conversion_on_purchase(): void
    {
        $purchase = [
            'event' => 'purchase',
            'transaction_id' => '42',
            'value' => 1299.5,
            'currency' => 'UAH',
            'ecommerce' => [
                'transaction_id' => '42',
                'value' => 1299.5,
                'currency' => 'UAH',
                'items' => [
                    [
                        'item_name' => 'Test product',
                        'item_id' => 'SKU-1',
                        'price' => 1299.5,
                        'quantity' => 1,
                        'currency' => 'UAH',
                    ],
                ],
            ],
            'items' => [
                [
                    'id' => 'SKU-1',
                    'google_business_vertical' => 'retail',
                ],
            ],
        ];

        $response = $this->withSession(['purchase_js' => $purchase])
            ->get('/thank-you');

        $response->assertOk();
        $response->assertSee('"event":"purchase"', false);
        $response->assertSee('"currency":"UAH"', false);
        $response->assertSee('"transaction_id":"42"', false);
        $response->assertSee('gtag(\'event\', \'conversion\'', false);
        $response->assertSee('AW-17008582925/JMThCJzL47gaEI3Cqa4_', false);
        $response->assertSee('transaction_id: "42"', false);
        $response->assertSee('currency: "UAH"', false);
        $response->assertSee('value: 1299.5', false);
    }

    public function test_thank_you_page_without_purchase_does_not_send_conversion(): void
    {
        $response = $this->get('/thank-you');

        $response->assertOk();
        $response->assertDontSee('gtag(\'event\', \'conversion\'', false);
        $response->assertDontSee('AW-17008582925/JMThCJzL47gaEI3Cqa4_', false);
    }
}
