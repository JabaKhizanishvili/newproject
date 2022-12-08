$(document).ready(function () {
    // Basic
    $('.dropify').dropify();

    // Translated
    $('.dropify-fr').dropify({
        messages: {
            default: 'Glissez-déposez un fichier ici ou cliquez',
            replace: 'Glissez-déposez un fichier ou cliquez pour remplacer',
            remove: 'Supprimer',
            error: 'Désolé, le fichier trop volumineux'
        }
    });

    // Used events
    var drEvent = $('.dropify-event').dropify();
    var dropvent_1 = $('.dropify-1').dropify();
    var dropvent_2 = $('.dropify-2').dropify();


    drEvent.on('dropify.beforeClear', function (event, element) {
        return confirm("Do you really want to delete \"" + element.filename + "\" ?");
    });

    drEvent.on('dropify.afterClear', function (event, element) {
        alert('File deleted');
    });

    dropvent_1.on('dropify.afterClear', function (event, elemtn) {
        let image = $("#old-main-1");
        image.val("")

    });
    dropvent_2.on('dropify.afterClear', function (event, elemtn) {
        let image = $("#old-main-2");
        image.val("")

    });

    $("#input-file-now-1").change(function () {
        let image = document.querySelector('#old-main-1');
        image.value = "";
    });
    $("#input-file-now-2").change(function () {
        let image = document.querySelector('#old-main-2');
        image.value = "";
    });
});
