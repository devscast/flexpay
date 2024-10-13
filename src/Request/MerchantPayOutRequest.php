<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Request;

use Devscast\Flexpay\Credential;
use Devscast\Flexpay\Data\Currency;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

final class MerchantPayOutRequest extends Request
{
    private HttpClientInterface $client;

    private string $apiUrl = 'https://api.flexpay.cd/api/merchantPayOutService';

    public function __construct(
        float $amount,
        string $reference,
        Currency $currency,
        string $callbackUrl,
        public string $phone,
        public Credential $credentials,
        public string $telephone,
        public int $type = 1,
    ) {
        $this->client = HttpClient::create();

        Assert::length($this->telephone, 12, 'The phone number should be 12 characters long, eg: 243123456789');

        parent::__construct($amount, $reference, $currency, $callbackUrl);
    }

    public function getPayload(): array
    {
        return [
            'merchant' => $this->merchant,
            'type' => $this->type,
            'reference' => $this->reference,
            'phone' => $this->telephone,
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'callbackUrl' => $this->callbackUrl,
        ];
    }
}
