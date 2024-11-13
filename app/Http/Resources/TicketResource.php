<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DTO\TicketDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function __construct(TicketDTO $ticketDTO)
    {
        parent::__construct($ticketDTO);
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
            'type' => $this->resource->type->label(),
            'price' => $this->resource->price,
            'barcode' => $this->resource->barcode,
        ];
    }
}
