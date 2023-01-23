/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
  var $Height = ($('div.footer_wrapper').height());
  var $margin = 0 - $Height;
  $('div.footer_wrapper').css('margin-top', $margin + 'px');
  $('div.top').css('padding-bottom', $Height + 'px');
//  $('#login-block').css('height', 'calc(100vh - 80px)');
});
