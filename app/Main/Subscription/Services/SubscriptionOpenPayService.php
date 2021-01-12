<?php

namespace App\Main\Subscription\Services;

use App\User;

class SubscriptionOpenPayService
{
    public function __invoke(User $user, string $tokenId, string $deviceSessionId, string $planId)
    {
        $openpay = \Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_KEY_PRIVATE'));

        try {
            $customerData = [
                'name' => $user->name,
                'email' => $user->email,
            ];

            $customer = $openpay->customers->add($customerData);

            $cardData = [
                'token_id' => $tokenId,
                'device_session_id' => $deviceSessionId,
               ];

            $card = $customer->cards->add($cardData);

            $dt = (new \DateTime())->modify('-1 day');

            $subscription = [
                'trial_end_date' => $dt->format('Y-m-d'),
                'plan_id' => $planId,
                'card_id' => $card->id,
            ];

            $subscription = $customer->subscriptions->add($subscription);

            return [
                'customerId' => $customer->id,
                'cardId' => $card->id,
                'subscriptionId' => $subscription->id, ];
        } catch (\OpenpayApiTransactionError $e) {
            $err = ('ERROR on the transaction: '.$e->getMessage().
                      ' [error code: '.$e->getErrorCode().
                      ', error category: '.$e->getCategory().
                      ', HTTP code: '.$e->getHttpCode().
                      ', request ID: '.$e->getRequestId().']');
            \Log::error(__FILE__.PHP_EOL.$err);

            throw new \Exception($e->getMessage(), (int) $e->getCode());
        } catch (\OpenpayApiRequestError $e) {
            $err = ('ERROR on the request: '.$e->getMessage());
            \Log::error(__FILE__.PHP_EOL.($e->getCode()).' - '.$err);
            throw new \Exception($e->getMessage(), 400);
        } catch (\OpenpayApiConnectionError $e) {
            $err = ('ERROR while connecting to the API: '.$e->getMessage());
            \Log::error(__FILE__.PHP_EOL.$err);
            throw new \Exception($e->getMessage(), (int) $e->getCode());
        } catch (\OpenpayApiAuthError $e) {
            $err = ('ERROR on the authentication: '.$e->getMessage());
            \Log::error(__FILE__.PHP_EOL.$err);
            throw new \Exception($e->getMessage(), (int) $e->getCode());
        } catch (\OpenpayApiError $e) {
            $err = ('ERROR on the API: '.$e->getMessage());
            \Log::error(__FILE__.PHP_EOL.$err);
            throw new \Exception($e->getMessage(), (int) $e->getCode());
        } catch (\Exception $e) {
            \Log::error(__FILE__);
            \Log::error($e->getMessage());

            throw new \Exception($e->getMessage(), (int) $e->getCode());
        }
    }
}
