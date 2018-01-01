<?php

namespace OxygenModule\ImportExport;

use Oxygen\Core\Blueprint\BlueprintManager;
use Oxygen\Data\BaseServiceProvider;
use OxygenModule\ImportExport\Database\DatabaseManager;
use OxygenModule\ImportExport\Database\DatabaseWorker;
use OxygenModule\ImportExport\Console\BackupCommand;
use OxygenModule\ImportExport\Console\BackupImportCommand;

class ImportExportServiceProvider extends BaseServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'oxygen/mod-import-export');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'oxygen/mod-import-export');
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'oxygen.mod-import-export');

        $this->publishes([
            __DIR__ . '/../resources/lang' => base_path('resources/lang/vendor/oxygen/mod-import-export'),
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/oxygen/mod-import-export'),
            __DIR__ . '/../config/config.php' => config_path('oxygen/mod-import-export.php')
		]);

		$this->commands(BackupCommand::class);
		$this->commands(BackupImportCommand::class);

        $this->app['router']->middleware('oxygen.deleteTemporaryFiles', DeleteTemporaryFilesMiddleware::class);

        $this->app[BlueprintManager::class]->loadDirectory(__DIR__ . '/../resources/blueprints');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */

	public function register() {
        $this->app->singleton(ImportExportManager::class, function($app) {
            $manager = new ImportExportManager($app['config'], $app);
            $manager->addWorker($app[DatabaseWorker::class]);
            return $manager;
        });
    }

}
