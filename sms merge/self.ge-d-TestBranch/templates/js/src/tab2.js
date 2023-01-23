(function ($) {
    $.fn.skinableTabs = function () {
        $('ul li', this).each(function () {
            if ($(this).parent().parent().hasClass('tabs_holder')) {
                $(this).click(function () {
                    var ID = $('a', this).attr('href');
                    $('.tab_selected').removeClass('tab_selected');
                    $(this).addClass('tab_selected');
                    $('div[id^="graph"]').css('display', 'none');
                    $(ID).css('display', 'block');
                    $('.tabs_holder .content_holder').css('display', 'block');
                    $.cookie("tabs_holder", $(this).attr('id'), {path: '/'});
                    SetHelp();
                    return false;
                });
            }
        });
        var $ID = $.cookie("tabs_holder");
        if ($ID)
        {
            return $('#' + $ID, this).click();
        }
        else
        {
            return $('ul li:first-child', this).click();
        }
    };
})(jQuery);