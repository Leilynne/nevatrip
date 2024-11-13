<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTO\OrderDTO;
use App\Models\Order;

readonly class OrderMapper
{
    public function __construct(
        private TicketMapper $ticketMapper,
    ) {
    }

    public function mapModelToDTO(Order $order): OrderDTO
    {
        return new OrderDTO(
            $order->id,
            $order->event_id,
            $order->event_date,
            $order->total_price,
            $this->ticketMapper->mapModelsToDTOArray($order->tickets),
        );
    }
}
