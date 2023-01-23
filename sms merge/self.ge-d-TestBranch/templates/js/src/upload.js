function setUpload($id)
{
    $("#" + $id).on("change", function ()
    {
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader)
        {
            return; // no file selected, or no FileReader support
        }
        var $id = $(this).attr('id');
        if (/^image/.test(files[0].type)) { // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file
            reader.onloadend = function () {
                var img = $('<img id="dynamic">');
                img.attr('src', this.result);
                img.css('width', '100%');
                img.css('height', 'auto');
                $("#" + $id + "Preview").html(img);
                $("#" + $id + "Source").val(img.attr('src'));
//                $("#" + $id + "Preview").css("background-image", "url(" + this.result + ")");
            }
        }
    });
}