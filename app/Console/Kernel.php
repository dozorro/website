<?php namespace App\Console;

use app\Classes\Cron;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
        'App\Console\Commands\ReviewsUpdate',
        'App\Console\Commands\ProcuringEntities',
        'App\Console\Commands\FormsUpdate',
        'App\Console\Commands\FormsLocalUpdate',
        //'App\Console\Commands\CommentsLocalUpdate',
        'App\Console\Commands\SendForms',
        'App\Console\Commands\FormsSync',
        'App\Console\Commands\MonitoringClear',
        'App\Console\Commands\ResaveComments',
        'App\Console\Commands\ResaveForms',
        'App\Console\Commands\FixForms',
        'App\Console\Commands\ResaveAuthors',
        'App\Console\Commands\ResaveOwners',
        'App\Console\Commands\AssignBadges',
		'App\Console\Commands\FormsSyncFix',
        'App\Console\Commands\AddCoins',
        'App\Console\Commands\NgoForms',
		'App\Console\Commands\FixCommentsThread',
		'App\Console\Commands\SetIsCustomerAnswer',
		'App\Console\Commands\ResetRisks',
		'App\Console\Commands\CustomerCount',
		'App\Console\Commands\ScrapeCpv',
		'App\Console\Commands\FixNgoForms',
		'App\Console\Commands\CheckNgoForms',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
	}

}
