<?php namespace Ravloony\LaravelTransduct;

use Illuminate\Support\ServiceProvider;

class LaravelTransductServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['transduct'] = $this->app->share(function($app)
		{
			return new LaravelTransduct;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('transduct');
	}

}
