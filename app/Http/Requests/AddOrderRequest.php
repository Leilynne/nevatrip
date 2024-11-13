<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer'], //при наличии таблици events можно было бы проверить exists:events,id
            'event_date' => ['required', 'date_format:Y-m-d H:i:s'],

            'ticket_adult_price' => ['required_with:ticket_adult_quantity', 'integer', 'min:0'],
            'ticket_adult_quantity' => ['integer', 'min:0'],

            'ticket_kid_price' => ['required_with:ticket_kid_quantity', 'integer', 'min:0'],
            'ticket_kid_quantity' => ['integer', 'min:0'],

            'ticket_group_price' => ['required_with:ticket_group_quantity', 'integer', 'min:0'],
            'ticket_group_quantity' => ['integer', 'min:0'],

            'ticket_benefit_price' => ['required_with:ticket_benefit_quantity', 'integer', 'min:0'],
            'ticket_benefit_quantity' => ['integer', 'min:0'],
        ];
    }
}
