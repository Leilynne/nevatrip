<?php

namespace App\DTO;

use App\Enums\TicketType;

class TicketDTO
{
    public function __construct(
        public int $id,
        public TicketType $type,
        public int $price,
        public int $orderId,
        public string $barcode,
    ){
    }
}
