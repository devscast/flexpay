<?php

namespace Devscast\Flexpay\Data;

enum TransactionType : int
{
    /**
     * 0 : pour les transactions mobile money
     */
    case MOBILE_MONEY = 1;


    /**
     * 1 : pour les transactions bancaires
     */
    case BANK_CARD = 2;

}
