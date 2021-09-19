<?php

namespace OxygenModule\ImportExport\Controller;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OxygenModule\ImportExport\ImportExportManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Oxygen\Core\Http\Notification;

class ImportExportController extends Controller {

    /**
     * Constructs the ImportExportController.
     */
    public function __construct() {
    }

    /**
     * Create a backup of the database/other content and save it as a file.
     *
     * @param ImportExportManager $manager
     * @return BinaryFileResponse|JsonResponse
     * @throws Exception
     */
    public function getExport(ImportExportManager $manager) {
        try {
            /// this process could take longer than usual, so we'll give it more time to run
            set_time_limit(1000);

            $manager->export();
            return response()->download($manager->getExportStrategy()->getDownloadableFile());
        } catch(Exception $e) {
            if(config('app.debug')) {
                throw $e;
            }
            logger()->error($e->getMessage(), ['exception' => $e]);
            return notify(new Notification(__('oxygen/mod-import-export::messages.backupFailed'), Notification::FAILED));
        }
    }

}
