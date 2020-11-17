<?php

namespace Enum;

use MyCLabs\Enum\Enum;

class OrderStatus extends Enum
{
    const PENDING = 1;
    const CLOSED = 2;

    public static function IsValidStatus($status)
    {
        switch ($status) {
            case OrderStatus::CLOSED:
                return true;
            case OrderStatus::PENDING:
                return true;
            default:
                return false;
        }
    }
}
