<?php

namespace App\Providers;

use App\Events\LibrarianCreated;
use App\Listeners\SendPasswordResetEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        LibrarianCreated::class => [
            SendPasswordResetEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
