<?php

namespace App\Listeners;

use App\Notifications\SendPasswordResetNotification;
use App\Events\SendPasswordResetEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPasswordResetListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SendPasswordResetEvent  $event
     * @return void
     */
    public function handle(SendPasswordResetEvent $event)
    {
        //
    
        $user = $event->user;
        $user->notify(new SendPasswordResetNotification($user, $event->token));

    }
}
