$(document).ready(function()
{
    $('.pagination_inp_span', '.pagination').hover(
            function() {
                $('.pagination_drop_list', this).css('display', 'block');
            },
            function() {
                $('.pagination_drop_list', this).css('display', 'none');
            });
});