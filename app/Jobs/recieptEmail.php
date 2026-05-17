<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class recieptEmail implements ShouldQueue
{
    use Queueable;
    const retries = 3;
    /**
     * Create a new job instance.
     */
    public function __construct(protected Order $order) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->order->user->email)->send(new \App\Mail\RecieptEmail($this->order));
    }
}
