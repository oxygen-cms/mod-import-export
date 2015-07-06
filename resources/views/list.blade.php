@extends(app('oxygen.layout'))

@section('content')

<?php

use Oxygen\Core\Html\Form\Form;use Oxygen\Core\Html\Header\Header;

    $header = Header::fromBlueprint(
        $blueprint,
        Lang::get('oxygen/mod-import-export::ui.title')
    );

?>

<div class="Block">
    {!! $header->render() !!}
</div>

<?php
    $toolbarItem = $blueprint->getToolbarItem('getExport');
    if($toolbarItem->shouldRender()):
?>
    <div class="Block">
        <div class="Row--visual">
            <h2 class="heading-gamma">Export</h2>
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

            $form->addContent(View::make('oxygen/mod-import-export::importUploadForm', ['blueprint' => $blueprint, 'toolbarItem' => $toolbarItem]));

            echo $form->render();
        ?>
    <?php
        endif;
    ?>
</div>

@stop
