<?php

namespace OxygenModule\ImportExport\Controller;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OxygenModule\ImportExport\ImportExportManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportExportController extends Controller {

    /**
     * Create a backup of the database/other content and save it as a file.
     *
     * @param ImportExportManager $manager
     * @return BinaryFileResponse|JsonResponse
     * @throws Exception
     */
    public function getExport(ImportExportManager $manager) {
        /// this process could take longer than usual, so we'll give it more time to run
        set_time_limit(1000);

        $manager->export();
        return response()->download($manager->getExportStrategy()->getDownloadableFile());
    }

}
