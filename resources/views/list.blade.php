@extends(app('oxygen.layout'))

@section('content')

<?php

use Oxygen\Core\Html\Form\Form;
use Oxygen\Core\Html\Form\Row;
use Oxygen\Core\Html\Toolbar\ButtonToolbarItem;
use Oxygen\Core\Html\Toolbar\SubmitToolbarItem;

?>

<?php
    $toolbarItem = $blueprint->getToolbarItem('getExport');
    if($toolbarItem->shouldRender()):
?>
    <div class="Block">
        <div class="Header Header--main">
            <div class="Header-title heading-beta">
                Export
            </div>
        </div>
        <div class="Row--visual">
            {!! $toolbarItem->render(['margin' => 'vertical']) !!}
            <p>
                Creates a backup of all the content on the website, including the entire database and media files.<br>
                It is recommended to make regular backups to ensure the safety of your content.
            </p>
        </div>
    </div>
<?php
    endif;
?>

<?php
    $toolbarItem = $blueprint->getToolbarItem('postImport');
    if($toolbarItem->shouldRender()):
?>
    <div class="Block">
        <?php
            $form = new Form($blueprint->getAction('postImport'));
            $form->setUseMultipartFormData(true);
            $form->setAsynchronous(true);

            $form->addContent(view('oxygen/mod-import-export::importUploadForm', ['blueprint' => $blueprint, 'toolbarItem' => $toolbarItem]));

            $footer = new Row([
                new ButtonToolbarItem(__('oxygen/crud::ui.close'), $blueprint->getAction('getList')),
                new SubmitToolbarItem(__('oxygen/mod-import-export::ui.import'))
            ]);
            $footer->isFooter = true;
            $form->addContent($footer);

            echo $form->render();
        ?>
    </div>
<?php
    endif;
?>

@stop
