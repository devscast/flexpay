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
     * @param string $token Le token d’autorisation
     * @param string $merchant Le code Marchand FlexPay, ex: ZANDO
     */
    public function __construct(
        #[\SensitiveParameter] public string $token,
        #[\SensitiveParameter] public string $merchant,
    ) {
        Assert::notEmpty($this->token, 'The authorization token cannot be empty');
        Assert::notEmpty($this->merchant, 'Merchant cannot be empty');
    }
}
