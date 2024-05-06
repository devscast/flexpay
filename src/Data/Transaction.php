<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Data;

/**
 * Class Transaction.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Transaction
{
    /**
     * @param ?string $orderNumber Le code de la transaction généré par FlexPay lors de la requête de paiement
     * @param string $reference La référence de la transaction envoyée
     * @param float $amount Le montant de la transaction envoyé
     * @param float $amountCustomer Le montant total que le client va payer
     * @param string $createdAt La devise de la transaction
     * @param Status $status Ce code donne le statut de la transaction
     * @param Currency $currency Date de création de la transaction
     */
    public function __construct(
        public string $reference,
        public float $amount,
        public float $amountCustomer,
        public string $createdAt,
        public Status $status,
        public Currency $currency = Currency::CDF,
        public ?string $orderNumber = null,
    ) {
    }
}
