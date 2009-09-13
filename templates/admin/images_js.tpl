$(document).ready(function(){ldelim}refreshImages();{rdelim});
  //$('#upload-form').hide();
  //$('#refresh-images-button').click(function(){ldelim}refreshImages();{rdelim});
  //$('#upload-image-button').toggle(function(){ldelim}$('#upload-form').show('fast');{rdelim}, function(){ldelim}$('#upload-form').hide('fast');{rdelim});

  function uploadFinished() {ldelim}
    alert('Image uploaded to '+$('#current-folder').html());
    resetUpload($('#current-folder').html());
    refreshImages($('#current-folder').html());
  {rdelim}

  function refreshImages(folder) {ldelim}
    frajax('admin-get-folder-contents',folder);
    resetUpload(folder);
  {rdelim}

  function listColumns() {ldelim}
    $('#files ul').after('<ul></ul>');
    var repeat = Math.floor($('#files ul:first li').length / 2);
    for (i=1;i<=repeat;i++)
    {ldelim}
    $('#files ul:first li:last').prependTo('#files ul:last');
    {rdelim}
  {rdelim}

  function closeJpop() {ldelim}
     parent.$('#jpop_loading').hide();
     parent.$('#jpop_overlay').hide();
     parent.$('.jpop_content').hide();
     parent.$('select.jpop_select').removeClass('jpop_select');
  {rdelim}

  function resetUpload(folder) {ldelim}
  $('#html_uploader').attr('src', "{$SITEURL}/actions/admin-upload-image.php?folder="+folder);
  $("#example1").jqUploader({ldelim}
    uploadScript: "{$SITEURL}/actions/admin-upload-image.php?jqUploader=1&key={$key}&folder="+folder,
    background: "F3F3F3",
    hideSubmit: true,
    barColor: "4E629F",
    width: 300,
    allowedExt: "*.gif; *.jpg; *.jpeg; *.png; *.GIF; *.JPG; *.JPEG; *.PNG",
    allowedExtDescr: "Images: jpg, gif, png",
    afterScript: "javascript:uploadFinished();",
    params: {ldelim}quality:'low'{rdelim},
    validFileMessage: "- ready to upload",
    startMessage: "Please select an image to upload",
    endMessage: "Upload completed",
    hideSubmit: false,
    endHtml: '<strong style="text-decoration:underline">Upload complete</strong>'
    {rdelim});
  {rdelim}

  $(document).ready(function(){ldelim}resetUpload('{$relativefolder}');{rdelim});