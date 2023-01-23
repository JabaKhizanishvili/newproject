/**
 * 
 * @param {type} $
 * @param {type} undefined
 * @returns {undefined}
 */
var $Count = 1;
(function ($, undefined) {
  $.fn.geokbd = function (options) {
    var isOn,
            inputs = $([]),
            defaults = {
              on: true,
              hotkey: '`'
            },
            settings = (typeof options === 'object' ? $.extend({}, defaults, options) : defaults);
    settings.TimeOut = [];
    // first come up with affected set of input elements
    this.each(function () {
      var $this = $(this);
      if ($this.is(':text, textarea')) {
        inputs = inputs.add($this);
      } else
      {
        return true;
      }

      if (!GlobalGeoKBD)
      {
        return true;
      }
      // mutate switchers
      $SwitcherHTML = '<span class="gk-switcher gk_area"><div class="gk-ka" /><div class="gk-us" /></span>';
      if ($this.is(':text'))
      {
        $SwitcherHTML = '<span class="gk-switcher gk_text"><div class="gk-ka" /><div class="gk-us" /></span>';
      }
      $this.on('focus', function () {
        var $Key = $('.geokbd-switcher', $this.parent()).attr('rel');
        clearTimeout(settings.TimeOut[$Key]);
        $('.geokbd-switcher', $this.parent()).css('display', 'block');
      });
      $this.on('blur', function () {
        var $Key = $('.geokbd-switcher', $this.parent()).attr('rel');
        clearTimeout(settings.TimeOut[$Key]);
        settings.TimeOut[$Key] = setTimeout(function () {
          $('.geokbd-switcher', $this.parent()).css('display', 'none');
        }, 500);
      });
      $this.parent().addClass('from-group-right');
      $('input, textarea', $this.parent()).after('<span class="geokbd-switcher from-group-addon-right" rel="' + $Count + '">' + $SwitcherHTML + '</span>');
//      $this.parent().append('<span class="input-group-addon">' + $SwitcherHTML + '</span>');
      var switcher = $('.gk-switcher', $this.parent());
      switcher.click(function () {
        var $Key = $('.geokbd-switcher', $this.parent()).attr('rel');
        clearTimeout(settings.TimeOut[$Key]);
        toggleLang($(this));
        $(':text, textarea', $this.parent()).select();
      });

      isOn = parseInt(settings.on);
      if (isOn === 0)
      {
        switcher.attr('rel', '0');
      } else
      {
        switcher.attr('rel', '1').addClass('gk-on');
      }
      $this.keypress(function (e) {
        if (e.ctrlKey || e.altKey)
        {
          return;
        }
        var ch = String.fromCharCode(e.which), kach,
                toggler = $('.gk-switcher', $(this).parent());
        if (settings.hotkey === ch) {
          toggleLang(toggler);
          e.preventDefault();
        }
        if (toggler.attr('rel') === '0') {
          return;
        }
        kach = translateToKa.call(ch);
        if (ch != kach) {
          if ($.browser.msie) {
            window.event.keyCode = kach.charCodeAt(0);
          } else {
            pasteTo.call(kach, e.target);
            e.preventDefault();
          }
        }
      });
      settings.TimeOut[$Count] = null;
      $Count++;
    });
    function toggleLang($this) {
      if ($this.attr('rel') == '0')
      {
        $this.addClass('gk-on');
        $this.attr('rel', '1');
      } else {
        $this.removeClass('gk-on');
        $this.attr('rel', '0');
      }

    }

    // the following functions come directly from Ioseb Dzmanashvili's GeoKBD (https://github.com/ioseb/geokbd)

    function translateToKa() {
      /**
       * Original idea by Irakli Nadareishvili
       * http://www.sapikhvno.org/viewtopic.php?t=47&postdays=0&postorder=asc&start=10
       */
      var index, chr, text = [], symbols = "abgdevzTiklmnopJrstufqRySCcZwWxjh";

      for (var i = 0; i < this.length; i++) {
        chr = this.substr(i, 1);
        if ((index = symbols.indexOf(chr)) >= 0) {
          text.push(String.fromCharCode(index + 4304));
        } else {
          text.push(chr);
        }
      }
      return text.join('');
    }

    function pasteTo(field) {
      field.focus();
      if (document.selection) {
        var range = document.selection.createRange();
        if (range) {
          range.text = this;
        }
      } else if (field.selectionStart != undefined) {
        var scroll = field.scrollTop, start = field.selectionStart, end = field.selectionEnd;
        var value = field.value.substr(0, start) + this + field.value.substr(end, field.value.length);
        field.value = value;
        field.scrollTop = scroll;
        field.setSelectionRange(start + this.length, start + this.length);
      } else {
        field.value += this;
        field.setSelectionRange(field.value.length, field.value.length);
      }
    }

    function getValue($this, $cssRule)
    {
      var $value = parseInt($this.css($cssRule));
      if (isNaN($value))
      {
        return 0;
      } else
      {
        return $value;
      }

    }
  };
}(jQuery));
