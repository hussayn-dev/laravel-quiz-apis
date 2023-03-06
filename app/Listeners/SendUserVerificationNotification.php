<?php

namespace App\Listeners;

use App\Notifications\sendUserEmailVerificationNotification;
use App\Events\SendUserVerificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


class SendUserVerificationNotification
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
     * @param  \App\Events\SendUserVerificationEvent  $event
     * @return void
     */
    public function handle(SendUserVerificationEvent $event)
    {
        //
        $user = $event->user;
        $user->notify(new sendUserEmailVerificationNotification($user));

    }
}
