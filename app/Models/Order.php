<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


/**
 * @property-read int $id
 * @property int $event_id
 * @property Carbon $event_date
 * @property int $user_id
 * @property int $total_price
 * @method static create(array $params)
 * @property-read Collection<int, Ticket> $tickets
 */
class Order extends Model
{
    protected $fillable =  [
        'event_id',
        'event_date',
        'user_id',
        'total_price',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);

    }
}
