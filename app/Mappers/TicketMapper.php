<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTO\TicketDTO;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

readonly class TicketMapper
{
    public function mapModelToDTO(Ticket $ticket): TicketDTO
    {
        return new TicketDTO(
            $ticket->id,
            $ticket->type,
            $ticket->price,
            $ticket->order_id,
            $ticket->barcode,
        );
    }

    /**
     * @return TicketDTO[]
     */
    public function mapModelsToDTOArray(Collection $tickets): array
    {
        $result = [];
        foreach ($tickets as $ticket) {
            $result[] = $this->mapModelToDTO($ticket);
        }

        return $result;
    }
}
