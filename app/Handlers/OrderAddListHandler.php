<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Commands\OrderAddListCommand;
use App\DTO\OrderDTO;
use App\Enums\TicketType;
use App\Mappers\OrderMapper;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Uid\Ulid;


readonly class OrderAddListHandler
{
    public function __construct(
        private OrderMapper $orderMapper,
    ) {
    }

    /**
     * @throws HttpException
     */
    public function handle(OrderAddListCommand $command): OrderDTO
    {
        $eventId = $command->eventId;
        $eventDate = $command->eventDate;
        $ticketAdultQuantity = $command->ticketAdultQuantity;
        $ticketAdultPrice = $command->ticketAdultPrice;
        $ticketKidQuantity = $command->ticketKidQuantity;
        $ticketKidPrice = $command->ticketKidPrice;
        $ticketBenefitQuantity = $command->ticketBenefitQuantity;
        $ticketBenefitPrice = $command->ticketBenefitPrice;
        $ticketGroupPrice = $command->ticketGroupPrice;
        $ticketGroupQuantity = $command->ticketGroupQuantity;

        $totalQuantity = $ticketGroupQuantity + $ticketBenefitQuantity + $ticketAdultQuantity + $ticketKidQuantity;

        if (0 === $totalQuantity) {
            throw new HttpException(422, 'В заказе должен быть хотя бы 1 билет');
        }

        $price = $ticketKidPrice * $ticketKidQuantity + $ticketBenefitPrice * $ticketBenefitQuantity
            + $ticketGroupPrice * $ticketGroupQuantity +$ticketAdultQuantity * $ticketAdultPrice;

        $barcodes = $this->postBook(
            $eventId,
            $eventDate,
            $ticketAdultQuantity,
            $ticketAdultPrice,
            $ticketGroupQuantity,
            $ticketBenefitPrice,
            $ticketBenefitQuantity,
            $ticketGroupPrice,
            $ticketKidQuantity,
            $ticketKidPrice,
            $totalQuantity
        );
        $approveResponse = Http::post('https://api.site.com/approve', [
            'barcodes' => $barcodes,
        ]);

        if (false === $approveResponse->successful()) {
            throw new HttpException(422, $approveResponse->json('error'));
        }

        /* @var Order $order */
        $order = Order::create([
            'event_id' => $eventId,
            'event_date' => $eventDate,
            'total_price' => $price,
        ]);

        if ($ticketAdultQuantity > 0) {
            $this->createTickets(
                TicketType::Adult,
                $ticketAdultPrice,
                $order->id,
                $barcodes,
                $ticketAdultQuantity,
            );
        }
        if ($ticketKidQuantity > 0) {
            $this->createTickets(
                TicketType::Kid,
                $ticketKidPrice,
                $order->id,
                $barcodes,
                $ticketKidQuantity,
            );
        }
        if ($ticketGroupQuantity > 0) {
            $this->createTickets(
                TicketType::Group,
                $ticketGroupPrice,
                $order->id,
                $barcodes,
                $ticketGroupQuantity,
            );
        }
        if ($ticketBenefitQuantity > 0) {
            $this->createTickets(
                TicketType::Benefit,
                $ticketBenefitPrice,
                $order->id,
                $barcodes,
                $ticketBenefitQuantity,
            );
        }

        return $this->orderMapper->mapModelToDTO($order);
    }

    /**
     * @return string[]
     */
    public function postBook(
        $eventId,
        $eventDate,
        $ticketAdultPrice,
        $ticketAdultQuantity,
        $ticketKidPrice,
        $ticketKidQuantity,
        $ticketGroupPrice,
        $ticketGroupQuantity,
        $ticketBenefitPrice,
        $ticketBenefitQuantity,
        $totalQuantity
    ): array {
        $barcodes = $this->createBarcodes($totalQuantity);
        $response = Http::post('https://api.site.com/book', [
            'event_id' => $eventId,
            'event_date' => $eventDate->format('Y-m-d H:i:s'),
            'ticket_adult_price' => $ticketAdultPrice,
            'ticket_adult_quantity' => $ticketAdultQuantity,
            'ticket_kid_price' => $ticketKidPrice,
            'ticket_kid_quantity' => $ticketKidQuantity,
            'ticket_group_price' => $ticketGroupPrice,
            'ticket_group_quantity' => $ticketGroupQuantity,
            'ticket_benefit_price' => $ticketBenefitPrice,
            'ticket_benefit_quantity' => $ticketBenefitQuantity,
            'barcodes' => $barcodes,
        ]);

        if (false === $response->successful()) {
            $barcodes = $this->createBarcodes($totalQuantity);
            $response = Http::post('https://api.site.com/book', [
                'event_id' => $eventId,
                'event_date' => $eventDate->format('Y-m-d H:i:s'),
                'ticket_adult_price' => $ticketAdultPrice,
                'ticket_adult_quantity' => $ticketAdultQuantity,
                'ticket_kid_price' => $ticketKidPrice,
                'ticket_kid_quantity' => $ticketKidQuantity,
                'ticket_group_price' => $ticketGroupPrice,
                'ticket_group_quantity' => $ticketGroupQuantity,
                'ticket_benefit_price' => $ticketBenefitPrice,
                'ticket_benefit_quantity' => $ticketBenefitQuantity,
                'barcodes' => $barcodes,
            ]);
        }

        if (false === $response->successful()) {
            throw new HttpException(422, $response->json('error'));
        }

        return $barcodes;
    }

    /**
     * @return string[]
     */
    public function createBarcodes(int $totalQuantity): array
    {
        $barcodes = [];
        for ($i = $totalQuantity; $i > 0; $i--) {
            $barcodes[] = $this->ulidToNumericString(new Ulid());
        }

        return $barcodes;
    }

    private function createTickets(TicketType $type, int $price, int $orderId, array &$barcodes, int $quantity): void
    {
        for ($i = 0; $i < $quantity; $i++) {
            Ticket::create([
                'type' => $type,
                'price' => $price,
                'order_id' => $orderId,
                'barcode' => array_shift($barcodes),
            ]);
        }
    }
    private function ulidToNumericString(Ulid $ulid): string
    {
        $base32 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numericString = '';

        foreach (str_split($ulid->toString()) as $char) {
            $numericValue = strpos($base32, $char);
            $numericString .= str_pad((string)$numericValue, 2, '0', STR_PAD_LEFT);
        }

        return $numericString;
    }
}
