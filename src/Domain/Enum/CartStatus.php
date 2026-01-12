<?php declare(strict_types=1);

namespace MyShoppingCart\Domain\Enum;

enum CartStatus: string {
    case OPENED = 'opened';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}