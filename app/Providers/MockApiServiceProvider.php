<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class MockApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Auth::loginUsingId(155662255);

        Http::fake([
            'https://api.site.com/book' => function () {
                $responses = [
                    [
                        'body' => ['message' => 'order successfully booked'],
                        'code' => 201
                    ],
                    [
                        'body' => ['error' => 'barcode already exists'],
                        'code' => 422
                    ],
                ];
                $random = array_rand($responses);

                return Http::response($responses[$random]['body'], $responses[$random]['code']);
            },
            'https://api.site.com/approve' => function () {
                $responses = [
                    [
                        'body' => ['message' => 'order successfully aproved'],
                        'code' => 201
                    ],
                    [
                        'body' => ['error' => 'event cancelled'],
                        'code' => 422
                    ],
                ];
                $random = array_rand($responses);

                return Http::response($responses[$random]['body'], $responses[$random]['code']);
            },
        ]);
    }
}

