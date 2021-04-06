<?php

namespace App\Main\Subscription\Services;

class CreateSubscriptionByCustomerOpenPayService
{
    public function __invoke(int $customerId, int $cardId, int $planId)
    {
        $openpay = \Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_KEY_PRIVATE'));

        try {
            $dt = (new \DateTime())->modify('+1 day');

            $subscription = [
                'trial_end_date' => $dt->format('Y-m-d'),
                'plan_id' => $planId,
                'card_id' => $cardId,
            ];

            \Log::error('Array subscription: '.print_r($subscription, 1));
            $customerObj = $openpay->customers->get($customerId);
            $subscription = $customerObj->subscriptions->add($subscription);

            return $subscription;
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
