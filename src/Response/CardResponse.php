<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Response;

use Devscast\Flexpay\Data\Status;

/**
 * Class CardResponse.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class CardResponse extends FlexpayResponse
{
    public function __construct(
        public Status $code,
        public string $message = '',
        public ?string $orderNumber = null,
        public ?string $url = null
    ) {
    }
}
