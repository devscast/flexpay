<?php

namespace Devscast\Flexpay\Data;

enum TransactionType: int
{
    /**
     * 0 : pour les transactions mobile money
     */
    case MOBILE = 0;

    /**
     * 1 : pour les transactions bancaires
     */
    case CARD = 1;

}
