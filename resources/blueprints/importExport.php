<?php

use Oxygen\Core\Http\Method;
use OxygenModule\ImportExport\Controller\ImportExportController;

Blueprint::make('ImportExport', function(Oxygen\Core\Blueprint\Blueprint $blueprint) {
    $blueprint->setController(ImportExportController::class);
    $blueprint->setDisplayName('Import/Export');
    $blueprint->disablePluralForm();
    $blueprint->setIcon('download');

    $blueprint->setPrimaryToolbarItem('getList');

    $blueprint->makeAction([
        'name'        => 'getList',
        'pattern'     => '/'
    ]);
    $blueprint->makeToolbarItem([
        'action'    => 'getList',
        'label'     => 'Import/Export',
        'icon'      => 'download',
        'color'     => 'white'
    ]);

    $blueprint->makeAction([
        'name'        => 'getExport',
        'pattern'     => 'export',
        'middleware'  => [\OxygenModule\ImportExport\DeleteTemporaryFilesMiddleware::class],
        'useSmoothState' => false
    ]);
    $blueprint->makeToolbarItem([
        'action'    => 'getExport',
        'label'     => 'Export Content',
        'icon'      => 'download',
        'color'     => 'blue'
    ]);

    $blueprint->makeAction([
        'name'        => 'postImport',
        'pattern'     => 'import',
        'method'      => Method::POST
    ]);
    $blueprint->makeToolbarItem([
        'action'    => 'postImport',
        'label'     => 'Import Content',
        'icon'      => 'folder-open-o'
    ]);

});