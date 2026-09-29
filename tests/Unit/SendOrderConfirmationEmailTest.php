<?php

namespace Tests\Unit;

use App\Jobs\SendOrderConfirmationEmail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendOrderConfirmationEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_sends_an_order_confirmation_email_to_the_customer(): void
    {
        Mail::fake();

        $order = Order::factory()->create();

        (new SendOrderConfirmationEmail($order))->handle();

        Mail::assertSent(OrderConfirmationMail::class, function (OrderConfirmationMail $mail) use ($order) {
            return $mail->hasTo($order->customer->email)
                && $mail->order->id === $order->id;
        });
    }

    /** @test */
    public function test_it_logs_a_warning_and_skips_sending_when_the_order_no_longer_exists(): void
    {
        Mail::fake();
        Log::spy();

        $order = Order::factory()->create();
        $orderId = $order->id;
        $order->delete();

        // Simulate a stale job payload — the model no longer exists in the DB
        $staleOrder = new Order();
        $staleOrder->id = $orderId;
        $staleOrder->exists = false;

        (new SendOrderConfirmationEmail($staleOrder))->handle();

        Mail::assertNothingSent();
        Log::shouldHaveReceived('warning')->once();
    }
}
