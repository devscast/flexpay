<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Request;

use Devscast\Flexpay\Data\Currency;
use Webmozart\Assert\Assert;

/**
 * Class VposRequest.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class VposRequest extends Request
{
    public function __construct(
        float $amount,
        string $reference,
        Currency $currency,
        string $callbackUrl,
        public readonly ?string $homeUrl,
        ?string $description = null,
        ?string $approveUrl = null,
        ?string $cancelUrl = null,
        ?string $declineUrl = null
    ) {
        Assert::notEmpty($approveUrl, 'The approve url must be provided');

        parent::__construct($amount, $reference, $currency, $callbackUrl, $approveUrl, $description, $cancelUrl, $declineUrl);
    }

    #[\Override]
    public function getPayload(): array
    {
        return [
            'amount' => $this->amount,
            'merchant' => $this->merchant,
            'authorization' => $this->authorization,
            'reference' => $this->reference,
            'currency' => $this->currency->value,
            'callback_url' => $this->callbackUrl,
            'description' => $this->description,
            'approve_url' => $this->approveUrl,
            'cancel_url' => $this->cancelUrl,
            'decline_url' => $this->declineUrl,
            'home_url' => $this->homeUrl,
        ];
    }
}
