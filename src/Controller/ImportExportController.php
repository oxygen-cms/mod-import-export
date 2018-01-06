<?php

namespace OxygenModule\ImportExport\Controller;

use App;
use Artisan;
use Config;
use Exception;
use Illuminate\Http\Request;
use OxygenModule\ImportExport\ImportExportManager;
use View;
use Lang;
use Response;
use Validator;

use Oxygen\Core\Blueprint\BlueprintManager;
use Oxygen\Core\Http\Notification;
use Oxygen\Core\Controller\BlueprintController;
use OxygenModule\ImportExport\Strategy\PHPZipExportStrategy;

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
     * @param \OxygenModule\ImportExport\ImportExportManager $manager
     * @return \Illuminate\Http\Response
     */
    public function getExport(ImportExportManager $manager) {
        try {
            /// this process could take longer than usual, so we'll give it more time to run
            set_time_limit(1000);

            $strategy = new PHPZipExportStrategy();
            $manager->export($strategy);
            return Response::download($strategy->getDownloadableFile());
        } catch(Exception $e) {
            return Response::notification(new Notification(Lang::get('oxygen/mod-import-export::messages.backupFailed'), Notification::FAILED));
        }
    }

    /**
     * Uploads a backup of the content and restores it.
     *
     * @param \Illuminate\Http\Request                       $input
     * @param \OxygenModule\ImportExport\ImportExportManager $manager
     * @return \Illuminate\Http\Response
     */
    public function postImport(Request $input, ImportExportManager $manager) {
        // if no file has been uploaded
        if(!$input->hasFile('file')) {
            // guess if post_max_size has been reached
            if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
                $message = Lang::get('oxygen/crud::messages.upload.tooLarge');
            } else {
                $message = Lang::get('oxygen/crud::messages.upload.noFiles');
            }

            return Response::notification(
                new Notification($message, Notification::FAILED)
            );
        }

        $file = $input->file('file');

        if(is_array($file)) {
            $file = $file[0];
        }

        if(!$file->isValid()) {
            $messages = new MessageBag();
            return Response::notification(new Notification(Lang::get('oxygen/crud::messages.upload.failed', [
                'name' => $file->getClientOriginalName(),
                'error' => $file->getError()
            ]), Notification::FAILED));
        }

        $validator = Validator::make(
            ['file' => $file],
            ['file' => 'mimes:zip']
        );

        if($validator->fails()) {
            return Response::notification(new Notification($validator->messages()->first(), Notification::FAILED));
        }

        $manager->import($file->getRealPath());

        return Response::notification(new Notification(Lang::get('oxygen/mod-import-export::messages.contentImported')), ['refresh' => true, 'hardRedirect' => true]);
    }


}