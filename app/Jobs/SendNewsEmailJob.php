<?php

namespace App\Jobs;

use App\Campaign;
use App\ErrorLog;
use App\Mail\NewsEmail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $campaign;
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users_temp = $this->campaign->users();

        foreach ($users_temp as $user) {
            try{
                Mail::to($user->email)->send(new NewsEmail());
                $result = $user->campaigns()->where('campaign_id',$this->campaign->id)->first();
                $result->pivot->status = 'Completed';
                $result->pivot->save();

                $this->campaign->status = 'Completed';
                $this->campaign->save();
            }
            catch (\Exception $e){

            }
        }
    }
}
