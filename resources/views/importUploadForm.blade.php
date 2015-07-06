<div class="Row--visual">
    <h2 class="heading-gamma">Import</h2>
    <p>
        Imports content into the database, overwriting any existing content.
    </p>
</div>

<div class="Row--visual">
    <div class="FileUpload">
        <input name="file[]" type="file" multiple>
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