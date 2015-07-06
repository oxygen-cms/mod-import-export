<div class="Row--noLayout">
    <h2 class="heading-gamma">Import</h2>
    {!! $toolbarItem->render(['margin' => 'vertical']) !!}
    <p>
        Creates a backup of all the content on the website, including the entire database and media files.<br>
        It is recommended to make regular backups to ensure the safety of your content.
    </p>
</div>

<div class="Row--noLayout">
    <div class="FileUpload">
        <input name="file[]" multiple>
        <span class="FileUpload-message FileUpload--js">Drop content here</span>
        <span class="FileUpload-subMessage FileUpload--js">(or click to select)</span>
        <span class="FileUpload-message FileUpload--noJs">Click to select files</span>
        <span class="FileUpload-subMessage FileUpload--noJs">(then click the 'Upload' button)</span>
    </div>
</div>

<div class="Row Form-footer">
    <a href="{{{ URL::route($blueprint->getRouteName('getList')) }}}" class="Button Button-color--white">Close</a>
    <button type="submit" class="Button Button-color--green">Import</button>
</div>