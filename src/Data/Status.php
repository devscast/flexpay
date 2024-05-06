<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Data;

/**
 * Class Status.
 *
 * Ce code donne le statut de la requête envoyée
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum Status: int
{
    /**
     * 0 : pour la requête bien envoyée
     */
    case SUCCESS = 0;

    /**
     * 1 : en cas de problème
     */
    case FAILURE = 1;
}
