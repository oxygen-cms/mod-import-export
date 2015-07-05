@extends(app('oxygen.layout'))

@section('content')

<?php

    use Carbon\Carbon;
    use Oxygen\Core\Html\Header\Header;

    $header = Header::fromBlueprint(
        $blueprint,
        Lang::get('oxygen/mod-security::loginLog.title')
    );

    $header->setBackLink(URL::route($blueprint->getRouteName('getList')));

?>

<div class="Block">
    {!! $header->render() !!}
</div>

<div class="Block">
    <table>
        <tr>
            <th>Username</th>
            <th>IP Addresss</th>
            <th>Timestamp</th>
            <th>Type</th>
        </tr>
        @foreach(array_reverse($log) as $item)
        <tr>
            <td>{{{ $item->getUsername() }}}</td>
            <td>
                {{{ $item->getIpAddress() }}}
                <?php
                    $button = $blueprint->getToolbarItem('postLoginLocation');
                    echo $button->render(['model' => $item, 'margin' => 'horizontal', 'inline' => true]);
                ?>
            </td>
            <td>{{{ Carbon::instance($item->getTimestamp())->toDayDateTimeString() }}}</td>
            <td>{{{ $item->getType() }}}</td>
        </tr>
        @endforeach
    </table>
</div>

@stop
