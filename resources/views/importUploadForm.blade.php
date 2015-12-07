<div class="Row--visual">
    <h2 class="heading-gamma">Import</h2>
    <p>
        Imports content into the database, overwriting any existing content.
    </p>
    <p>
        <strong>WARNING:</strong><br>
        This will <strong>erase all content</strong> currently in the database.
        Make sure that you have backed up any existing content and are absolutely sure you want to overwrite it with new data.<br>
        <strong>Do not use content dumps from an untrusted source</strong>, as the import process executes raw database queries from the dump file
    </p>
</div>

<div class="Row--visual">
    <div class="ProgressBar FileUpload-progress FileUpload--js">
        <span class="ProgressBar-fill"></span>
    </div>
    <div class="FileUpload">
        <div class="FileUpload-dropzone">
            <div class="FileUpload-drop FileUpload--js">Drop files here</div>
            <div class="FileUpload-click Button Button-color--grey">
                Click to select files
                <input name="file[]" type="file" multiple>
            </div>
        </div>
    </div>
</div>

<div class="Row Form-footer">
    <a href="{{ URL::route($blueprint->getRouteName('getList')) }}" class="Button Button-color--white">Close</a>
    <button type="submit" class="Button Button-color--green">Import</button>
</div>