$(document).ready(function () {

  var ul = $('#uploads ul');
  $('#drops a').click(function () {
// Simulate a click on the file input button
// to show the file browser dialog
    $(this).parent().find('input').click();
  });
  // Initialize the jQuery File Upload plugin
  $('#uploads').fileupload({
// This element will accept file drag/drop uploading
    dropZone: $('#drops'),
    // This function is called when a file is added to the queue;
    // either via the browse button, or via drag/drop:
    add: function (e, data) {

      var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"' +
              ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');
      // Append the file name and file size
      tpl.find('p').text(data.files[0].name)
              .append('<i>' + formatFileSize(data.files[0].size) + '</i>');
      // Add the HTML to the UL element
      data.context = tpl.appendTo(ul);
      // Initialize the knob plugin
      tpl.find('input').knob();
      // Listen for clicks on the cancel icon
      tpl.find('span').click(function () {

        if (tpl.hasClass('working')) {
          jqXHR.abort();
        }

        tpl.fadeOut(function () {
          tpl.remove();
        });
      });
      // Automatically upload the file once it is added to the queue
      var jqXHR = data.submit();
    },
    progress: function (e, data) {

      // Calculate the completion percentage of the upload
      var progress = parseInt(data.loaded / data.total * 100, 10);
      // Update the hidden input field and trigger a change
      // so that the jQuery knob plugin knows to update the dial
      data.context.find('input').val(progress).change();
      if (progress == 100) {
        data.context.removeClass('working');
      }
    },
    fail: function (e, data) {
      // Something has gone wrong!
      data.context.addClass('error');
    },
    done: function (e, data) {
      var $Result = $.parseJSON(data.result);
      if ($Result.status === 'error')
      {
        if (typeof $Result.alert !== 'unknown')
        {
          alert($Result.alert);
          ul.html('');
        }
      }
      if ($Result.status === 'success')
      {
        if (typeof $Result.file !== 'unknown')
        {
          var $Files = $('#files_data').val();
          if ($Files == '')
          {
            $('#files_data').val($Result.file);
          } else {
            $('#files_data').val($Files + '|' + $Result.file);
          }

        }
      }

    }

  });
  $('#done_upload').click(function () {
    var $Param = $('#param').val();
    console.log($('#files_data').val());
    window.parent.setUploadedFiles($('#files_data').val(), $Param);
    window.parent.$(".lity-close").click();
    //window.parent.$.prettyPhoto.close();
  });
  // Prevent the default action when a file is dropped on the window
  $(document).on('drop dragover', function (e) {
    e.preventDefault();
  });
  // Helper function that formats the file sizes
  function formatFileSize(bytes) {
    if (typeof bytes !== 'number') {
      return '';
    }

    if (bytes >= 1000000000) {
      return (bytes / 1000000000).toFixed(2) + ' GB';
    }

    if (bytes >= 1000000) {
      return (bytes / 1000000).toFixed(2) + ' MB';
    }

    return (bytes / 1000).toFixed(2) + ' KB';
  }

});
function setUploadedFiles($files, $param)
{
  console.log($param);

  var $name = $('.uploadFilesdata_' + $param).attr('value') + '[]';
  var $Container = $('#uploadFilescontainer_' + $param);
  $FilesData = $files.split('|');
  $.each($FilesData, function (index, $file) {
    if ($file != '')
    {
      var $fileName = substr($file, 33);
      var $href = 'download?f=' + $file;
      $html = '<div class="uploadFileItem">\n\
<div class="uploadFileItem_name">\n\
      <a href="' + $href + '" target="_blank" class="upload_title">' + $fileName + '</a>\n\
<input type="hidden" name="' + $name + '" value="' + $file + '"/>\n\
</div>\n\
<div class="uploadFiletools">\n\
<span class="uploadFiletool uploadFiletool_delete">\n\
<a  href="javascript:void(0);" onclick="RemoveFileItem(this);" class="upload_view">\n\
<i class="bi bi-x-lg X-ico"></i>\n\
</a></span>\n\
<span class="uploadFiletool uploadFiletool_search">\n\
<a  href="' + $href + '" target="_blank" class="upload_view">\n\
<i class="bi bi-search search-ico"></i>\n\
</a></span>\n\
</div>\n\
<div class="cls"></div>\n\
</div>\n\
';
      $Container.append($html);
    }
  });
}
function RemoveFileItem($Item)
{
  $($Item).parents('.uploadFileItem').remove();
}