<?php

use Illuminate\Routing\Router;
use OxygenModule\ImportExport\Controller\ImportExportController;

Route::prefix('/oxygen/api/import-export')->middleware('api_auth')->group(function(Router $router) {
    $router->post('/export', [ImportExportController::class, 'getExport'])
        ->name('importExport.getExport')
        ->middleware('oxygen.permissions:importExport.getExport');
});