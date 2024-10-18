<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Request;

use Devscast\Flexpay\Data\Currency;
use Devscast\Flexpay\Data\Type;
use Webmozart\Assert\Assert;

/**
 * Class PayoutRequest.
 *
 * @author Rooney kalumba
 */
final class PayoutRequest extends Request
{
    public function __construct(
        float $amount,
        string $reference,
        Currency $currency,
        string $callbackUrl,
        public string $phone,
        public Type $type = Type::MOBILE,
    ) {
        Assert::length($this->phone, 12, 'The phone number should be 12 characters long, eg: 243123456789');

        parent::__construct($amount, $reference, $currency, $callbackUrl);
    }

    public function getPayload(): array
    {
        return [
            'merchant' => $this->merchant,
            'type' => $this->type->value,
            'reference' => $this->reference,
            'phone' => $this->phone,
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'callbackUrl' => $this->callbackUrl,
        ];
    }
}
