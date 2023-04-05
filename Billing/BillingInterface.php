<?php

namespace App\Services\Billing;

use App\Models\User;
use Stripe\Customer;
use Illuminate\Support\Collection;
use Stripe\StripeClient;
use Stripe\Token;
use Laravel\Cashier\PaymentMethod;
use Laravel\Cashier\Subscription;

interface BillingInterface {

	public function createCustomer(User $user): Customer;

	public function defaultPaymentMethod(User $user): PaymentMethod | null;

	public function paymentMethods(User $user): Collection | null;

	public function hasDefaultPaymentMethod(User $user): bool;

	public function hasPaymentMethod(User $user): bool;

	public function addPaymentMethod(User $user, array $paymentMethod): PaymentMethod | null;

	public function deletePaymentMethods(User $user): void;

	public function createSubscription(User $user, string $plan): Subscription | null;

	public function changeSubscription(User $user, string $plan): Subscription | null;

}