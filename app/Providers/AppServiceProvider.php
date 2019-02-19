<?php namespace App\Providers;

use App\Area;
use Illuminate\Support\ServiceProvider;
use Blade;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Area::created(function ($area) {
		    $area->insertTranslations();
        });

        Blade::directive('time', function($expression) {
            return "<?php echo with{$expression}->format('H:i'); ?>";
        });

        Blade::directive('datetime', function($expression) {
            return "<?php echo with{$expression}->format('d.m.Y H:i'); ?>";
        });

        Blade::directive('date', function($expression) {
            return "<?php echo with{$expression}->format('d.m.Y'); ?>";
        });

        Blade::directive('tenderdate', function($expression) {
            return "<?php echo with{$expression}->format('j.m.y'); ?>";
        });

        Blade::directive('generateword', function($expression) {
            return "<?php echo \\App\\Helpers::getTenderWord($expression); ?>";
        });
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'App\Services\Registrar'
		);

        if(env('APP_DEBUG'))
        {
            $this->app->register('\Barryvdh\Debugbar\ServiceProvider');
        }
	}

}
