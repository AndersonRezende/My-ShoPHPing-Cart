<?php declare(strict_types=1);

enum CartStatus: string {
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}