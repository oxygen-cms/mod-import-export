<?php

namespace OxygenModule\ImportExport\Controller;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Factory;
use Oxygen\Core\Blueprint\BlueprintNotFoundException;
use OxygenModule\ImportExport\ImportExportManager;
use ReflectionException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Oxygen\Core\Blueprint\BlueprintManager;
use Oxygen\Core\Http\Notification;
use Oxygen\Core\Controller\BlueprintController;
use OxygenModule\ImportExport\Strategy\PHPZipExportStrategy;

class ImportExportController extends BlueprintController {

    /**
     * Constructs the AuthController.
     *
     * @param BlueprintManager $manager
     * @throws BlueprintNotFoundException
     * @throws ReflectionException
     */
    public function __construct(BlueprintManager $manager) {
        parent::__construct($manager->get('ImportExport'));
    }

    /**
     * Shows the update form.
     *
     * @return \Illuminate\View\View
     */

    public function getList() {
        return view('oxygen/mod-import-export::list', [
            'title' => __('oxygen/mod-import-export::ui.title')
        ]);
    }

    /**
     * Create a backup of the database/other content and save it as a file.
     *
     * @param ImportExportManager $manager
     * @return BinaryFileResponse
     */
    public function getExport(ImportExportManager $manager) {
        try {
            /// this process could take longer than usual, so we'll give it more time to run
            set_time_limit(1000);

            $strategy = new PHPZipExportStrategy();
            $manager->export($strategy);
            return response()->download($strategy->getDownloadableFile());
        } catch(Exception $e) {
            logger()->error($e->getMessage(), ['exception' => $e]);
            return notify(new Notification(__('oxygen/mod-import-export::messages.backupFailed'), Notification::FAILED));
        }
    }

    /**
     * Uploads a backup of the content and restores it.
     *
     * @param Request $input
     * @param ImportExportManager $manager
     * @param Factory $validationFactory
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public function postImport(Request $input, ImportExportManager $manager, Factory $validationFactory) {
        // if no file has been uploaded
        if(!$input->hasFile('file')) {
            // guess if post_max_size has been reached
            if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
                $message = __('oxygen/crud::messages.upload.tooLarge');
            } else {
                $message = __('oxygen/crud::messages.upload.noFiles');
            }

            return notify(
                new Notification($message, Notification::FAILED)
            );
        }

        $file = $input->file('file');

        if(is_array($file)) {
            $file = $file[0];
        }

        if(!$file->isValid()) {
            $messages = new MessageBag();
            return notify(new Notification(__('oxygen/crud::messages.upload.failed', [
                'name' => $file->getClientOriginalName(),
                'error' => $file->getError()
            ]), Notification::FAILED));
        }

        $validator = $validationFactory->make(
            ['file' => $file],
            ['file' => 'mimes:zip']
        );

        if($validator->fails()) {
            return notify(new Notification($validator->messages()->first(), Notification::FAILED));
        }

        $manager->import($file->getRealPath());

        return notify(new Notification(__('oxygen/mod-import-export::messages.contentImported')), ['refresh' => true, 'hardRedirect' => true]);
    }


}