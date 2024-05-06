<?php

declare(strict_types=1);

namespace Devscast\Flexpay;

use Webmozart\Assert\Assert;

/**
 * Class Credential.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Credential
{
    /**
     * Le token d’autorisation
     */
    public string $token;

    /**
     * Le code Marchand FlexPay, ex: ZANDO
     */
    public string $merchant;

    public function __construct(
        #[\SensitiveParameter] string $token,
        #[\SensitiveParameter] string $merchant,
    ) {
        Assert::notEmpty($token, 'The authorization token cannot be empty');
        Assert::notEmpty($merchant, 'Merchant cannot be empty');

        $this->token = $token;
        $this->merchant = $merchant;
    }
}
