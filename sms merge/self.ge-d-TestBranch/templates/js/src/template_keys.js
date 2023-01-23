$(document).ready(function () {
  $('.tmpl_key').click(function () {
    tinymce.activeEditor.execCommand('mceInsertContent', false, $(this).attr('title'));
  });
});