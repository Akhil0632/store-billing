<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! $this->order->exists) {
            Log::warning("Order {$this->order->id} no longer exists — skipping confirmation email.");
            return;
        }

        Log::info('📧 Sending order confirmation email', [
            'order_id'       => $this->order->id,
            'customer_email' => $this->order->customer->email,
            'grand_total'    => $this->order->grand_total,
        ]);

        Mail::to($this->order->customer->email)
            ->send(new OrderConfirmationMail($this->order));
    }

    public function failed(Throwable $exception): void
    {
        Log::error('❌ Order confirmation email failed permanently', [
            'order_id' => $this->order->id,
            'error'    => $exception->getMessage(),
        ]);
    }

}
