<?php

declare(strict_types=1);

namespace Devscast\Flexpay;

use Devscast\Flexpay\Data\Currency;
use Webmozart\Assert\Assert;

/**
 * Class PaymentEntry.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class PaymentEntry
{
    /**
     * Le montant de la transaction
     */
    public readonly float $amount;

    /**
     * La référence de la transaction
     */
    public readonly string $reference;

    /**
     * La devise de la transaction.
     */
    public readonly Currency $currency;

    /**
     * L’url de retour ou le résultat de la transaction sera envoyé en tache
     * de fond avec la référence et le statut de la transaction
     */
    public readonly string $callbackUrl;

    /**
     * Le numéro de téléphone du client
     * @example 243999999999
     */
    public readonly ?string $phone;

    private ?string $merchant = null;

    public function __construct(
        float $amount,
        string $reference,
        Currency $currency,
        string $callbackUrl,
        string $phone = null,
        public readonly int $type = 1,
        public readonly ?string $description = null,
        public readonly ?string $approveUrl = null,
        public readonly ?string $cancelUrl = null,
        public readonly ?string $declineUrl = null,
    ) {
        Assert::greaterThan($amount, 0, 'The transaction amount should be greater than 0');
        Assert::notEmpty($reference, 'The transaction reference is mandatory');
        Assert::oneOf($currency, [Currency::CDF, Currency::USD], 'Unsupported currency');
        Assert::notEmpty($callbackUrl, 'The callback (webhook) url must be provided');
        Assert::nullOrLength($phone, 12, 'Invalid phone number, format: 243123456789');

        $this->amount = $amount;
        $this->reference = $reference;
        $this->callbackUrl = $callbackUrl;
        $this->currency = $currency;
        $this->phone = $phone;
    }

    /**
     * @internal Cette méthode est utilisée pour définir le marchand
     */
    public function setMerchant(string $merchant): void
    {
        $this->merchant = $merchant;
    }

    public function getMerchant(): string
    {
        return (string) $this->merchant;
    }
}
