var initDepSel = new Array;
function DependentSelect(ParentSelect, ChildSelect, data)
{
    if (!initDepSel[ParentSelect])
    {
        initDependentSelect(ParentSelect, ChildSelect, data);
        initDepSel[ParentSelect] = 1;
    }

    var ND = $('#' + ParentSelect).val();
    var ChildSelectEl = $('#' + ChildSelect);
    ChildSelectEl.html('');
    if (data[ND])
    {
        $('option', '#' + ChildSelect + '_container').each(function(i, v) {
            var thisid = parseInt($(this).attr('value'));
            if (jQuery.inArray(thisid, data[ND]) != -1)
            {
                var clone = $(this).clone();
                ChildSelectEl.append(clone);
            }
        });
//        $("select").uniform();
    }
}

function initDependentSelect(ParentSelect, ChildSelect, data)
{
    var SBItems = $('#' + ChildSelect).html();
    var html = '<select id="' + ChildSelect + '_container" style="position:absolute;top:-999999px;visibility: hidden;display:none;"></select>';
    $('body').append(html);
    $('#' + ChildSelect + '_container').html(SBItems);
    $('#' + ParentSelect).change(function() {
        DependentSelect(ParentSelect, ChildSelect, data);
    });
}