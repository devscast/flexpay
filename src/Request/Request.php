<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Request;

use Devscast\Flexpay\Credential;
use Devscast\Flexpay\Data\Currency;
use Webmozart\Assert\Assert;

/**
 * Class Request.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract class Request
{
    public ?string $merchant = null;

    public ?string $authorization = null;

    public function __construct(
        public readonly float $amount,
        public readonly string $reference,
        public readonly Currency $currency,
        public readonly string $callbackUrl,
        public readonly ?string $approveUrl = null,
        public readonly ?string $description = null,
        public readonly ?string $cancelUrl = null,
        public readonly ?string $declineUrl = null,
    ) {
        Assert::greaterThan($this->amount, 0, 'The transaction amount should be greater than 0');
        Assert::notEmpty($this->reference, 'The transaction reference is mandatory');
        Assert::oneOf($this->currency, Currency::cases(), 'Unsupported currency');
        Assert::notEmpty($this->callbackUrl, 'The callback (webhook) url must be provided');
    }

    /**
     * @internal
     *
     * Cette méthode est utilisée pour définir les informations d'authentification.
     * Elle est définie ici pour éviter de passer par le constructeur
     * et rajouter de la complexité pour le développeur final
     */
    public function setCredential(Credential $credential): void
    {
        $this->merchant = $credential->merchant;
        $this->authorization = $credential->token;
    }

    abstract public function getPayload(): array;
}
