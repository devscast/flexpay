<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Response;

use Devscast\Flexpay\Data\Status;
use Devscast\Flexpay\Data\Transaction;

/**
 * Class CheckResponse.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class CheckResponse extends FlexpayResponse
{
    public function __construct(
        public Status $code,
        public string $message,
        public ?Transaction $transaction = null
    ) {
    }
}
