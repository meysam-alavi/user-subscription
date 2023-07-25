<?php

namespace App\Console\Commands;

use App\Mail\sendEmailAlertSubscriptionExpirationRemind;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;

class alertSubscribeExpirationToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:alert-subscribe-expiration-to-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lastUserId = Redis::command('get', ['lastUserId']);

        if (empty($lastUserId)) {
            $users = User::all();

            foreach ($users as $user) {
                $subscriptionExpire = Redis::command('get', ["user-{$user->id}:subscribe-expired-at"]);

                Redis::command('set', ['lastUserId', $user->id]);
                if (!empty($subscriptionExpire)) {

                    $remindDays = ceil(($subscriptionExpire - time()) / 86400);
                    if ($remindDays == 3) {
                        Mail::to($user->email)->later(now()->addMinutes(5), new sendEmailAlertSubscriptionExpirationRemind($user));
                    }
                }
            }

            Redis::command('set', ['lastUserId', null]);
        }
    }
}
