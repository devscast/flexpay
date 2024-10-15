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

    public function getVposAskUrl(): string
    {
        return match ($this) {
            self::LIVE => 'https://cardpayment.flexpay.cd/api/rest/v1/vpos/ask',
            self::SANDBOX => 'https://beta-cardpayment.flexpay.cd/api/rest/v1/vpos/ask',
        };
    }

    public function getMobilePaymentUrl(): string
    {
        return match ($this) {
            self::LIVE, self::SANDBOX => sprintf('%s/paymentService', $this->getBaseUrl()),
        };
    }

    public function getVposPaymentUrl(string $orderNumber): string
    {
        return match ($this) {
            self::LIVE => sprintf('https://cardpayment.flexpay.cd/vpos/pay/%s', $orderNumber),
            self::SANDBOX => sprintf('https://beta-cardpayment.flexpay.cd/vpos/pay/%s', $orderNumber),
        };
    }

    public function getCheckStatusUrl(string $orderNumber): string
    {
        return match ($this) {
            self::LIVE, self::SANDBOX => sprintf('%s/check/%s', $this->getBaseUrl(), $orderNumber),
        };
    }

    public function getPayoutUrl(): string
    {
        return match ($this) {
            self::LIVE => ': http://ip:port/api/rest/v1/merchantPayOutService',
            self::SANDBOX => sprintf('%s/merchant/payout', $this->getBaseUrl()),
        };
    }

    private function getBaseUrl(): string
    {
        return match ($this) {
            self::LIVE => 'http://ip:port/api/rest/v1/merchantPayOutService',
            self::SANDBOX => 'https://beta-backend.flexpay.cd/api/rest/v1',
        };
    }
}
