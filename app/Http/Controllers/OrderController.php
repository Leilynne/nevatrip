<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\OrderAddListCommand;
use App\Handlers\OrderAddListHandler;
use App\Http\Requests\AddOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Carbon;

readonly class OrderController
{
    public function __construct(
        private OrderAddListHandler $orderAddListHandler,
    ) {
    }

    public function store(AddOrderRequest $request): OrderResource
    {
        $data = $request->validated();

        return new OrderResource(
            $this->orderAddListHandler->handle(
                new OrderAddListCommand(
                    (int) $data['event_id'],
                    Carbon::createFromFormat('Y-m-d H:i:s', $data['event_date']),
                    (int) ($data['ticket_adult_price'] ?? 0),
                    (int) ($data['ticket_adult_quantity'] ?? 0),
                    (int) ($data['ticket_kid_price'] ?? 0),
                    (int) ($data['ticket_kid_quantity'] ?? 0),
                    (int) ($data['ticket_group_price'] ?? 0),
                    (int) ($data['ticket_group_quantity'] ?? 0),
                    (int) ($data['ticket_benefit_price'] ?? 0),
                    (int) ($data['ticket_benefit_quantity'] ?? 0),
                )
            )
        );
    }
}
