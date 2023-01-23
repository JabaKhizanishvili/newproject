function LoadData(link, dest)
{
    $.get(link, function(data) {
        $(dest).html(data);
    });
}

