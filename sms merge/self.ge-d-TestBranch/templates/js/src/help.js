function getHelp()
{
    $('.help_content').each(function () {
        if ($(this).html())
        {
            $('.help_block', this).css('display', 'block');
            if ($(this).is(':visible'))
            {
                var $diff = 100;
                $('html, body').animate({scrollTop: $(this).offset().top - $diff}, 500);
            }
            $.cookie("showHelp", 'block', {
                path: '/'
            });
            LoadHelp();
        }
    });

}

function LoadHelp()
{
    $('.help_content').each(function () {
        $('iframe', this).css('height', 100);
        $('iframe', this).attr('src', $('.helpsource', this).val());
    });
}

function SetHelp()
{
    $('.help_block').css('display', 'none');
    if ($.cookie('showHelp'))
    {
        $('.help_block').css('display', $.cookie('showHelp'));
        LoadHelp();
    }
}

$(document).ready(function ()
{
    $('.help_close').click(function () {
        $('.close_tooltip').fadeTo(100, 0, function () {
            $('.help_block').slideUp(800, function () {
                $.cookie("showHelp", $('.help_block').css('display'), {path: '/'});
            });
        });
    });
    $('.close_tooltip').fadeTo(0, 0);
    $('.help_close').hover(
            function () {
                $('.close_tooltip').stop().fadeTo(500, 1);
            },
            function () {
                $('.close_tooltip').stop().fadeTo(500, 0);
            }
    );
    //Set help Content
    SetHelp();
});