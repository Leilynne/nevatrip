<?php

namespace App\Commands;

use Illuminate\Support\Carbon;

readonly class OrderAddListCommand
{
    public function __construct(
        public int $eventId,
        public Carbon $eventDate,
        public int $ticketAdultPrice,
        public int $ticketAdultQuantity,
        public int $ticketKidPrice,
        public int $ticketKidQuantity,
        public int $ticketGroupPrice,
        public int $ticketGroupQuantity,
        public int $ticketBenefitPrice,
        public int $ticketBenefitQuantity,
    ){
    }
}
