<?php

namespace App\Listeners;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Paddle\Customer;
use Laravel\Paddle\Events\SubscriptionCreated;
use Laravel\Paddle\Events\WebhookReceived;
use Laravel\Paddle\Exceptions\InvalidPassthroughPayload;
use function Safe\json_decode;

class WebhookReceivedListener
{
    /**
     * Handle Paddle webhook event.
     *
     * @param  WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        Log::info("{$event->payload['alert_name']} paddle webhook received", [
            'payload' => $event->payload,
        ]);

        $method = 'handle'.Str::studly($event->payload['alert_name']);

        if (method_exists($this, $method)) {
            try {
                $this->{$method}($event->payload);
            } catch (InvalidPassthroughPayload) {
                // Ignored
            }
        }
    }

    /**
     * Handle subscription created.
     *
     * @param  array  $payload
     * @return void
     *
     * @throws \Laravel\Paddle\Exceptions\InvalidPassthroughPayload
     */
    protected function handleSubscriptionCreated(array $payload)
    {
        $customer = $this->findOrCreateCustomer($payload, $payload['passthrough']);

        $trialEndsAt = $payload['status'] === Subscription::STATUS_TRIALING
            ? Carbon::createFromFormat('Y-m-d', $payload['next_bill_date'], 'UTC')->startOfDay()
            : null;

        $plan = Plan::where('plan_id_on_paddle', $payload['subscription_plan_id'])->firstOrFail();

        /** @var Subscription */
        $subscription = $customer->subscriptions()->create([
            'name' => $plan->plan_name,
            'paddle_id' => $payload['subscription_id'],
            'paddle_plan' => $payload['subscription_plan_id'],
            'paddle_status' => $payload['status'],
            'quantity' => $payload['quantity'],
            'trial_ends_at' => $trialEndsAt,
        ]);

        SubscriptionCreated::dispatch($customer, $subscription, $payload);
    }

    /**
     * Find or create a customer based on the passthrough values and return the billable model.
     *
     * @param  array  $payload
     * @param  string  $passthrough
     * @return User
     *
     * @throws \Laravel\Paddle\Exceptions\InvalidPassthroughPayload
     */
    protected function findOrCreateCustomer(array $payload, string $passthrough): User
    {
        $passthrough = json_decode($passthrough, true);

        if (is_array($passthrough) && isset($passthrough['billable_id'], $passthrough['billable_type'])) {
            // It will be handled by cashier's WebhookController
            throw new InvalidPassthroughPayload();
        }

        return tap(User::firstOrCreate(
            ['email' => $payload['email']],
            ['name' => $payload['email']]
        ), function ($user) {
            Customer::firstOrCreate([
                'billable_id' => $user->id,
                'billable_type' => User::class,
            ]);
        });
    }
}
