<?php

namespace App\DTO;

use Illuminate\Support\Carbon;

class OrderDTO
{
    /**
     * @param TicketDTO[] $tickets
     */
    public function __construct(
        public int $id,
        public int $eventId,
        public Carbon $eventDate,
        public int $price,
        public array $tickets,
    ){
    }
}
