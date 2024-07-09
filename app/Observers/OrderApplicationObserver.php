<?php

namespace App\Observers;

use App\OrderApplication;
use App\Notifications\NewApplicationNotification;
use App\Notifications\SentForAnalysisNotification;
use App\Notifications\StatusChangeNotification;
use App\Notifications\SubmittedAnalysisNotification;
use App\Role;
use App\Status;
use Illuminate\Support\Facades\Notification;

class OrderApplicationObserver
{
    /**
     * Handle the order application "creating" event.
     *
     * @param  \App\OrderApplication  $orderApplication
     * @return void
     */
    public function creating(OrderApplication $orderApplication)
    {
        $processingStatus = Status::whereName('Processing')->first();

        $orderApplication->status()->associate($processingStatus);
    }

    /**
     * Handle the order application "created" event.
     *
     * @param  \App\OrderApplication  $orderApplication
     * @return void
     */
    public function created(OrderApplication $orderApplication)
    {
        $admins = Role::find(1)->users;

        // Notification::send($admins, new NewApplicationNotification($orderApplication));
    }

    /**
     * Handle the order application "updated" event.
     *
     * @param  \App\OrderApplication  $orderApplication
     * @return void
     */
    public function updated(OrderApplication $orderApplication)
    {
        if ($orderApplication->isDirty('status_id')) {
            if (in_array($orderApplication->status_id, [2, 5])) {
                if ($orderApplication->status_id == 2) {
                    $user = $orderApplication->analyst;
                } else {
                    $user = $orderApplication->cfo;
                }

                // Notification::send($user, new SentForAnalysisNotification($orderApplication));
            } elseif (in_array($orderApplication->status_id, [3, 4, 6, 7])) {
                $users = Role::find(1)->users;

                // Notification::send($users, new SubmittedAnalysisNotification($orderApplication));
            } elseif (in_array($orderApplication->status_id, [8, 9])) {
                // $orderApplication->created_by->notify(new StatusChangeNotification($orderApplication));
            }
        }
    }
}
