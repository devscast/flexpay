<?php

namespace Devscast\Flexpay\Data;

class TransactionType
{
    private const MOBILE_MONEY = 1;
    private const BANK_CARD = 2;

    private int $type;

    public function __construct(int $type)
    {
        if (!in_array($type, [self::MOBILE_MONEY, self::BANK_CARD])) {
            throw new \InvalidArgumentException('Invalid transaction type');
        }

        $this->type = $type;

    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isMobileMoney(): bool
    {
        return $this->type === self::MOBILE_MONEY;
    }

    public function isBankCard(): bool
    {
        return $this->type === self::BANK_CARD;
    }

}