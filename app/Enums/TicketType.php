<?php

namespace App\Enums;

enum TicketType: string
{
    case Adult = 'adult';
    case Kid = 'kid';
    case Group = 'group';
    case Benefit = 'benefit';

    public function label(): string
    {
        return match ($this) {
            self::Adult => 'Взрослый',
            self::Kid => 'Детский',
            self::Group => 'Групповой',
            self::Benefit => 'Льготный',
        };
    }
}
