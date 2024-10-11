<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Request;

use Devscast\Flexpay\Data\Currency;
use Webmozart\Assert\Assert;

/**
 * Class MobileRequest.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class MobileRequest extends Request
{
    public function __construct(
        float $amount,
        string $reference,
        Currency $currency,
        string $callbackUrl,
        public readonly string $phone,
        public readonly int $type = 1,
        ?string $description = null,
        ?string $approveUrl = null,
        ?string $cancelUrl = null,
        ?string $declineUrl = null
    ) {
        Assert::length($this->phone, 12, 'The phone number should be 12 characters long, eg: 243123456789');

        parent::__construct($amount, $reference, $currency, $callbackUrl, $approveUrl, $description, $cancelUrl, $declineUrl);
    }

    #[\Override]
    public function getPayload(): array
    {
        return [
            'phone' => $this->phone,
            'type' => $this->type,
            'amount' => $this->amount,
            'merchant' => $this->merchant,
            'reference' => $this->reference,
            'currency' => $this->currency->value,
            'callbackUrl' => $this->callbackUrl,
            'description' => $this->description,
            'approveUrl' => $this->approveUrl,
            'cancelUrl' => $this->cancelUrl,
            'declineUrl' => $this->declineUrl,
        ];
    }
}
