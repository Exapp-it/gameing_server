<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Affiliate\AffiliateService;
use App\Services\Referral\ReferralStatisticService;
use Illuminate\Console\Command;

class RewardUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reward:users';
    protected $description = 'Reward users based on referral losses';


    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        parent::__construct();
        $this->affiliateService = $affiliateService;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $this->affiliateService->rewardReferrer($user);
        }

        $this->info('Users have been rewarded!');
    }
}
