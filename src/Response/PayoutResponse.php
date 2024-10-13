<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Response;

use Devscast\Flexpay\Data\Status;


final class PayoutResponse extends FlexpayResponse
{
    public function __construct(
        public Status $code,
        public string $message = '',
        public ?string $orderNumber = null,
    ) {
    }


}
