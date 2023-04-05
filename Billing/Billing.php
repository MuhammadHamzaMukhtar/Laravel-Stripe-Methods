<?php

namespace App\Services\Billing;

use Illuminate\Support\Collection;
use App\Models\User;
use Stripe\Customer;
use Stripe\StripeClient;
use Stripe\Token;
use Laravel\Cashier\PaymentMethod;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\Checkout;

class Billing implements BillingInterface {

    protected $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function createCustomer(User $user): Customer
    {
        return $user->createOrGetStripeCustomer();
    }

    public function defaultPaymentMethod(User $user): PaymentMethod | null
    {
        return $user->defaultPaymentMethod();
    }

    public function paymentMethods(User $user): Collection | null
    {
        return $user->paymentMethods();
    }

    public function hasDefaultPaymentMethod(User $user): bool
    {
        return $user->hasDefaultPaymentMethod();
    }

    public function hasPaymentMethod(User $user): bool
    {
        return $user->hasPaymentMethod();
    }

    private function addCard(array $card): Token | null
    {
        return $this->stripe->tokens->create($card);
    }

    private function updateDefaultPaymentMethodFromStripe($user){
        $user->updateDefaultPaymentMethodFromStripe();
    }

    public function addPaymentMethod(User $user, array $card): PaymentMethod | null
    {
        $paymentMethod = $this->stripe->paymentMethods->create($card);
        $payment = $user->updateDefaultPaymentMethod($paymentMethod);
        $this->updateDefaultPaymentMethodFromStripe($user);
        return $payment;
    }

    public function deletePaymentMethods(User $user): void
    {
        $user->deletePaymentMethods();
    }

    public function createSubscription(User $user, string $plan): Subscription | null
    {
        //return $user->newSubscription('default', $plan)->create($this->defaultPaymentMethod($user)->id);
        return $user->newSubscription('default', $plan)->create();
    }

    public function changeSubscription(User $user, string $plan): Subscription | null
    {
        return $user->subscription('default')->swap($plan);
    }

    public function changeSubscriptionInvoice(User $user, string $plan): Subscription | null
    {
        return $user->subscription('default')->swapAndInvoice($plan);
    }

    public function subscribed(User $user): bool
    {
        return $user->subscribed('default');
    }

    public function subscription(User $user): Subscription | null
    {
        return $user->subscription('default');
    }

    public function hasIncompletePayment(User $user): bool
    {
        return $user->hasIncompletePayment('default');
    }

    public function subscriptionCheckout(User $user, string $plan): Checkout
    {
        return $user->newSubscription('default', $plan)->allowPromotionCodes()->checkout([
            'success_url' => 'http://localhost:3000/error',
            'cancel_url' => 'http://localhost:3000/error',
        ]);
    }

    public function newSubscriptionWithCoupon(User $user, string $plan): Subscription
    {
        $user->newSubscription('default', 'price_monthly')->withPromotionCode('promo_code_id')->create($paymentMethod);
    }

    public function invoices(User $user){

        return $user->invoices();
    }

    public function pendingInvoices(User $user){

        return $user->invoicesIncludingPending();
    }

    public function upcomingInvoice(User $user){

        return  $user->upcomingInvoice();
    }

    public function activeSubscription(){
        return Subscription::query()->active();
    }

    public function canceledSubscription(){

        return Subscription::query()->canceled();
    }

    public function endedSubscription(){
        return Subscription::query()->ended();
    }

    public function incompleteSubscription(){

        return Subscription::query()->incomplete();
    }

    public function notCanceledSubscription(){

       return Subscription::query()->notCanceled();
    }

    public function notOnGracePeriodSubscription(){

        return Subscription::query()->notOnGracePeriod();
    }

    public function notOnTrialSubscription(){

        return Subscription::query()->onGracePeriod();
    }

    public function onTrialSubscription(){
        return Subscription::query()->onTrial();
    }

    
}