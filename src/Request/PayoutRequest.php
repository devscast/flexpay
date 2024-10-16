<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Request;

use Devscast\Flexpay\Credential;
use Devscast\Flexpay\Data\Currency;
use Devscast\Flexpay\Data\TransactionType;
use Webmozart\Assert\Assert;

final class PayoutRequest extends Request
{
    public function __construct(
        float $amount,
        string $reference,
        Currency $currency,
        string $callbackUrl,
        public Credential $credentials,
        public string $telephone,
        public TransactionType $type,
    ) {

        Assert::length($this->telephone, 12, 'The phone number should be 12 characters long, eg: 243123456789');

        parent::__construct($amount, $reference, $currency, $callbackUrl);
    }

    public function getPayload(): array
    {
        return [
            'authorization' => sprintf('Bearer %s', $this->credentials->token),
            'merchant' => $this->merchant,
            'type' => $this->type->value,
            'reference' => $this->reference,
            'phone' => $this->telephone,
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'callbackUrl' => $this->callbackUrl,
        ];
    }
}
