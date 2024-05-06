<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Response;

use Devscast\Flexpay\Data\Status;

/**
 * Class FlexpayResponse.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract class FlexpayResponse
{
    public Status $code;

    public string $message;

    public function isSuccessful(): bool
    {
        return $this->code === Status::SUCCESS;
    }
}
