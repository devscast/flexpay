<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Data;

/**
 * Class Currency.
 *
 * La devise de la transaction.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum Currency: string
{
    /**
     * USD : dollars américains
     */
    case USD = 'USD';

    /**
     * Franc Congolais.
     */
    case CDF = 'CDF';
}
