<form enctype="multipart/form-data" action="{$SITEURL}/actions/admin-upload-image.php{if $relativefolder}?folder={$relativefolder}{/if}" target="frajax-iframe" method="post" class="a_form">
  <h3>Upload images</h3>
  <div id="example1">
    <label for="example1_field">Upload image: </label>
    <input name="MAX_FILE_SIZE" value="2000000" type="hidden" />
    <input name="myFile" id="example1_field" type="file" />
  </div>
  <button onclick="frajax('admin-upload-image','reset');return false;">Reset</button>
</form>

<form id="html_uploader" enctype="multipart/form-data" action="{$SITEURL}/actions/admin-upload-image.php{if $relativefolder}?folder={$relativefolder}{/if}" target="frajax-iframe" method="post">
  <h3>HTML Uploader</h3>
  <div>
    <p>Use this uploader if the Flash uploader above does not work</p>
    <label>Upload image: </label>
    <input name="MAX_FILE_SIZE" value="2000000" type="hidden" />
    <input name="uploadimage" type="file" />
  </div>
  <input type="submit" name="submit" value="Upload" />
</form>