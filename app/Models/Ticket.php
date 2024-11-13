<?php

namespace App\Models;

use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property TicketType $type
 * @property integer $price
 * @property int $order_id
 * @property string $barcode
 */

class Ticket extends Model
{
    protected $fillable = [
        'type',
        'price',
        'order_id',
        'barcode',
];

    public function casts(): array
    {
        return [
            'type' => TicketType::class,
        ];

    }

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
