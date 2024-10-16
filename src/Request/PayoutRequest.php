<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Request;

use Devscast\Flexpay\Data\Currency;
use Webmozart\Assert\Assert;

final class PayoutRequest extends Request
{
    public function __construct(
        float $amount,
        string $reference,
        Currency $currency,
        string $callbackUrl,
        public string $phone,
        public int $type = 1,
    ) {

        Assert::length($this->phone, 12, 'The phone number should be 12 characters long, eg: 243123456789');

        parent::__construct($amount, $reference, $currency, $callbackUrl);
    }

    public function getPayload(): array
    {
        return [
            'authorization' => $this->authorization,
            'type' => $this->type,
            'reference' => $this->reference,
            'phone' => $this->phone,
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'callbackUrl' => $this->callbackUrl,
        ];
    }
}
