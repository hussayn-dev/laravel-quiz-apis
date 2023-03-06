<?php

namespace App\Providers;
use App\Notifications\SendPasswordResetNotification;
use App\Providers\SendPasswordMailEvent;
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
     * @param  \App\Providers\SendPasswordMailEvent  $event
     * @return void
     */
    public function handle(SendPasswordMailEvent $event)
    {
        //

        $user = $event->user;
        $user->notify(new SendPasswordResetNotification($user, $event->token));
    }
}
