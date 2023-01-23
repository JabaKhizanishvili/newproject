$(document).ready(function ()
{
  $('.my_tooltip').each(
          function ()
          {
            var content = $('.my_tip', this).html();
            var width = $(this).width();
//  
            if (!width) {
              width = '250px';
            }
            if (width < 250) {
              width = '250px';
            }
//            var width = '350px';
            $('span', this).qtip({
              content: {
                text: content
              },
              position: {
                corner: {
                  tooltip: 'bottomLeft', // Use the co0rner...
                  target: 'topLeft' // ...and opposite corner
                }
              },
              style: {
                border: {
                  width: 5,
                  radius: 5,
                  color: '#49477a'
                },
                padding: 10,
                name: 'cream',
                tip: true,
                width: width,
                background: '#49477a',
                color: '#fff'
              },
              show: {
                delay: 2
              }
            });
          }
  );
});
