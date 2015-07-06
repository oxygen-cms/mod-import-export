<?php

use Oxygen\Core\Http\Method;
use OxygenModule\ImportExport\Controller\ImportExportController;

Blueprint::make('ImportExport', function($blueprint) {
    $blueprint->setController(ImportExportController::class);
    $blueprint->setDisplayName('Import/Export');
    $blueprint->disablePluralForm();
    $blueprint->setIcon('save');

    $blueprint->setPrimaryToolbarItem('getList');

    $blueprint->makeAction([
        'name'        => 'getList',
        'pattern'     => '/'
    ]);
    $blueprint->makeToolbarItem([
        'action'    => 'getList',
        'label'     => 'Import/Export',
        'icon'      => 'save',
        'color'     => 'white'
    ]);

    $blueprint->makeAction([
        'name'        => 'getExport',
        'pattern'     => 'export'
    ]);
    $blueprint->makeToolbarItem([
        'action'    => 'getExport',
        'label'     => 'Export Content',
        'icon'      => 'save',
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