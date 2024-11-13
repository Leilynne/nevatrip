<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DTO\OrderDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function __construct(OrderDTO $orderDTO)
    {
        parent::__construct($orderDTO);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'event_id' => $this->resource->eventId,
            'event_date' => $this->resource->eventDate,
            'price' => $this->resource->price,
            'tickets' => TicketResource::collection($this->resource->tickets),
        ];
    }
}
