<?php

namespace OxygenModule\ImportExport\Controller;

use App;
use Artisan;
use Config;
use Exception;
use OxygenModule\ImportExport\ImportExportManager;
use View;
use Lang;
use Response;

use Oxygen\Core\Blueprint\BlueprintManager;
use Oxygen\Core\Http\Notification;
use Oxygen\Core\Controller\BlueprintController;
use ZipArchive;

class ImportExportController extends BlueprintController {

    /**
     * Constructs the AuthController.
     *
     * @param BlueprintManager        $manager
     */
    public function __construct(BlueprintManager $manager) {
        parent::__construct($manager->get('ImportExport'));
    }

    /**
     * Shows the update form.
     *
     * @return \Illuminate\Http\Response
     */

    public function getList() {
        return View::make('oxygen/mod-import-export::list', [
            'title' => Lang::get('oxygen/mod-import-export::ui.title')
        ]);
    }

    /**
     * Create a backup of the database/other content and save it as a file.
     *
     * @return \Illuminate\Http\Response
     */
    public function getExport(ImportExportManager $manager) {
        try {
            return Response::download($manager->export());
        } catch(Exception $e) {
            return Response::notification(new Notification(Lang::get('messages.utilities.backupFailed')));
        }
    }

    /**
     * Uploads a backup of the content and restores it.
     *
     * @return \Illuminate\Http\Response
     */
    public function postImport() {
        return Response::notification(new Notification('Imported'));
        /*$manager = App::make('oxygen.backup');

        try {
            return Response::download($manager->make());
        } catch(Exception $e) {
            return Response::notification(new Notification(Lang::get('messages.utilities.backupFailed')));
        }*/
    }


}