<?php

declare(strict_types=1);

namespace Devscast\Flexpay;

/**
 * Class Environment.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum Environment: string
{
    case LIVE = 'prod';
    case SANDBOX = 'dev';

    public function getCardPaymentUrl(): string
    {
        return match ($this) {
            self::LIVE => 'https://cardpayment.flexpay.cd/v1.1/pay',
            self::SANDBOX => 'https://beta-cardpayment.flexpay.cd/v1.1/pay',
        };
    }

    public function getMobilePaymentUrl(): string
    {
        return match ($this) {
            self::LIVE, self::SANDBOX => sprintf('%s/paymentService', $this->getBaseUrl()),
        };
    }

    public function getCheckStatusUrl(string $orderNumber): string
    {
        return match ($this) {
            self::LIVE, self::SANDBOX => sprintf('%s/check/%s', $this->getBaseUrl(), $orderNumber),
        };
    }

    private function getBaseUrl(): string
    {
        return match ($this) {
            self::LIVE => 'https://backend.flexpay.cd/api/rest/v1',
            self::SANDBOX => 'https://beta-backend.flexpay.cd/api/rest/v1',
        };
    }
}
