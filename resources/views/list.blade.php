@extends(app('oxygen.layout'))

@section('content')

<?php

    use Oxygen\Core\Html\Header\Header;

    $header = Header::fromBlueprint(
        $blueprint,
        Lang::get('oxygen/mod-import-export::title')
    );

?>

<div class="Block">
    {!! $header->render() !!}
</div>

<div class="Block">
    <?php
        $toolbarItem = $blueprint->getToolbarItem('getExport');
        if($toolbarItem->shouldRender()):
    ?>
        <div class="Row--visual">
            <h2 class="heading-gamma">Backup</h2>
            {{ $toolbarItem->render(['margin' => 'vertical']) }}
            <p>
                Creates a backup of all the content on the website, including the entire database and media files.<br>
                It is recommended to make regular backups to ensure the safety of your content.
            </p>
        </div>
    <?php
        endif;
    ?>
</div>

@stop
