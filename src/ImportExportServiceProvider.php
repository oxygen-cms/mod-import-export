<?php

namespace OxygenModule\ImportExport;

use Oxygen\Core\Blueprint\BlueprintManager;
use Oxygen\Data\BaseServiceProvider;
use OxygenModule\ImportExport\Database\DatabaseManager;
use OxygenModule\ImportExport\Database\DatabaseWorker;
use OxygenModule\ImportExport\Console\BackupCommand;
use OxygenModule\ImportExport\Console\BackupImportCommand;
use OxygenModule\ImportExport\Strategy\PHPZipExportStrategy;
use OxygenModule\ImportExport\Strategy\PHPZipImportStrategy;

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
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'oxygen/mod-import-export');
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'oxygen.mod-import-export');
        $this->loadRoutesFrom(__DIR__ . '/../resources/routes.php');

        $this->publishes([
            __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/oxygen/mod-import-export'),
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/oxygen/mod-import-export'),
            __DIR__ . '/../config/config.php' => config_path('oxygen/mod-import-export.php')
		]);

		$this->commands(BackupCommand::class);
		$this->commands(BackupImportCommand::class);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */

	public function register() {
        $this->app->singleton(ImportExportManager::class, function($app) {
            $manager = new ImportExportManager($app['config'], $app, new PHPZipImportStrategy(), new PHPZipExportStrategy());
            $manager->addWorker($app[DatabaseWorker::class]);
            return $manager;
        });
    }

}
