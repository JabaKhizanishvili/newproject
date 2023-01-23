+function ($) {

  'use strict';


  /* NUMBER CLASS DEFINITION
   * ====================== */

  var BFHNumber = function (element, options) {
    this.options = $.extend({}, $.fn.bfhnumber.defaults, options);
    this.$element = $(element);

    this.initInput();
  };

  BFHNumber.prototype = {

    constructor: BFHNumber,

    initInput: function () {
      var value;

      if (this.options.buttons === true) {
        this.$element.wrap('<div class="input-group"></div>');
        this.$element.parent().append('<span class="input-group-addon bfh-number-btn inc"><span class="bi bi-chevron-up"></span></span>');
        this.$element.parent().append('<span class="input-group-addon bfh-number-btn dec"><span class="bi bi-chevron-down"></span></span>');
      }

      this.$element.on('change.bfhnumber.data-api', BFHNumber.prototype.change);

      if (this.options.keyboard === true) {
        this.$element.on('keydown.bfhnumber.data-api', BFHNumber.prototype.keydown);
      }

      if (this.options.buttons === true) {
        this.$element.parent()
                .on('mousedown.bfhnumber.data-api', '.inc', BFHNumber.prototype.btninc)
                .on('mousedown.bfhnumber.data-api', '.dec', BFHNumber.prototype.btndec);
      }

      this.formatNumber();
    },

    keydown: function (e) {
      var $this;

      $this = $(this).data('bfhnumber');

      if ($this.$element.is('.disabled') || $this.$element.attr('disabled') !== undefined) {
        return true;
      }

      switch (e.which) {
        case 38:
          $this.increment();
          break;
        case 40:
          $this.decrement();
          break;
        default:
      }

      return true;
    },

    mouseup: function (e) {
      var $this,
              timer,
              interval;

      $this = e.data.btn;
      timer = $this.$element.data('timer');
      interval = $this.$element.data('interval');

      clearTimeout(timer);
      clearInterval(interval);
    },

    btninc: function () {
      var $this,
              timer;

      $this = $(this).parent().find('.bfh-number').data('bfhnumber');

      if ($this.$element.is('.disabled') || $this.$element.attr('disabled') !== undefined) {
        return true;
      }

      $this.increment();

      timer = setTimeout(function () {
        var interval;
        interval = setInterval(function () {
          $this.increment();
        }, 80);
        $this.$element.data('interval', interval);
      }, 750);
      $this.$element.data('timer', timer);

      $(document).one('mouseup', {btn: $this}, BFHNumber.prototype.mouseup);

      return true;
    },

    btndec: function () {
      var $this,
              timer;

      $this = $(this).parent().find('.bfh-number').data('bfhnumber');

      if ($this.$element.is('.disabled') || $this.$element.attr('disabled') !== undefined) {
        return true;
      }

      $this.decrement();

      timer = setTimeout(function () {
        var interval;
        interval = setInterval(function () {
          $this.decrement();
        }, 80);
        $this.$element.data('interval', interval);
      }, 750);
      $this.$element.data('timer', timer);

      $(document).one('mouseup', {btn: $this}, BFHNumber.prototype.mouseup);

      return true;
    },

    change: function () {
      var $this;

      $this = $(this).data('bfhnumber');

      if ($this.$element.is('.disabled') || $this.$element.attr('disabled') !== undefined) {
        return true;
      }
      $this.formatNumber();

      return true;
    },

    increment: function () {
      var value;

      value = this.getValue();

      value = value + 1;

      this.$element.val(value).change();
    },

    decrement: function () {
      var value;

      value = this.getValue();

      value = value - 1;

      this.$element.val(value).change();
    },

    getValue: function () {
      var value;

      value = this.$element.val();
      if (value !== '-1') {
        value = String(value).replace(/\D/g, '');
      }
      if (String(value).length === 0) {
        value = this.options.min;
      }

      return parseInt(value);
    },

    formatNumber: function () {
      var value,
              maxLength,
              length,
              zero;

      value = this.getValue();

      if (value > this.options.max) {
        if (this.options.wrap === true) {
          value = this.options.min;
        } else {
          value = this.options.max;
        }
      }

      if (value < this.options.min) {
        if (this.options.wrap === true) {
          value = this.options.max;
        } else {
          value = this.options.min;
        }
      }

      if (this.options.zeros === true) {
        maxLength = String(this.options.max).length;
        length = String(value).length;
        for (zero = length; zero < maxLength; zero = zero + 1) {
          value = '0' + value;
        }
      }

      if (value !== this.$element.val()) {
        this.$element.val(value);
      }
    }

  };

  /* NUMBER PLUGIN DEFINITION
   * ======================= */

  var old = $.fn.bfhnumber;

  $.fn.bfhnumber = function (option) {
    return this.each(function () {
      var $this,
              data,
              options;

      $this = $(this);
      data = $this.data('bfhnumber');
      options = typeof option === 'object' && option;

      if (!data) {
        $this.data('bfhnumber', (data = new BFHNumber(this, options)));
      }
      if (typeof option === 'string') {
        data[option].call($this);
      }
    });
  };

  $.fn.bfhnumber.Constructor = BFHNumber;

  $.fn.bfhnumber.defaults = {
    min: 0,
    max: 9999,
    zeros: false,
    keyboard: true,
    buttons: true,
    wrap: false
  };


  /* NUMBER NO CONFLICT
   * ========================== */

  $.fn.bfhnumber.noConflict = function () {
    $.fn.bfhnumber = old;
    return this;
  };


  /* NUMBER DATA-API
   * ============== */

  $(document).ready(function () {
    $('form input[type="text"].bfh-number, form input[type="number"].bfh-number').each(function () {
      var $number;

      $number = $(this);

      $number.bfhnumber($number.data());
    });
  });


  /* APPLY TO STANDARD NUMBER ELEMENTS
   * =================================== */


}(window.jQuery);

/* ==========================================================
 * bootstrap-formhelpers-timepicker.js
 * https://github.com/vlamanna/BootstrapFormHelpers
 * ==========================================================
 * Copyright 2012 Vincent Lamanna
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

+function ($) {

  'use strict';

  var BFHTimePickerDelimiter = ':';

  var BFHTimePickerModes = {
    'am': 'AM',
    'pm': 'PM'
  };

  /* TIMEPICKER CLASS DEFINITION
   * ========================= */

  var toggle = '[data-toggle=bfh-timepicker]',
          BFHTimePicker = function (element, options) {
            this.options = $.extend({}, $.fn.bfhtimepicker.defaults, options);
            this.$element = $(element);

            this.initPopover();
          };

  BFHTimePicker.prototype = {

    constructor: BFHTimePicker,

    setTime: function () {
      var time,
              today,
              timeParts,
              hours,
              minutes,
              mode,
              currentMode;

      time = this.options.time;
      mode = '';
      currentMode = '';
      console.log(time);
      if (time === '' || time === 'now' || time === undefined) {
        today = new Date();

        hours = today.getHours();
        minutes = today.getMinutes();

        if (this.options.mode === '12h') {
          if (hours > 12) {
            hours = hours - 12;
            mode = ' ' + BFHTimePickerModes.pm;
            currentMode = 'pm';
          } else {
            mode = ' ' + BFHTimePickerModes.am;
            currentMode = 'am';
          }
        }

        if (time === 'now') {
//          this.$element.find('.bfh-timepicker-toggle > input[type="text"]').val(formatTime(hours, minutes) + mode);
        }

        this.$element.data('hour', hours);
        this.$element.data('minute', minutes);
        this.$element.data('mode', currentMode);
      } else {
        timeParts = String(time).split(BFHTimePickerDelimiter);
        hours = timeParts[0];
        minutes = timeParts[1];

        if (this.options.mode === '12h') {
          timeParts = String(minutes).split(' ');
          minutes = timeParts[0];
          if (timeParts[1] === BFHTimePickerModes.pm) {
            currentMode = 'pm';
          } else {
            currentMode = 'am';
          }
        }

        this.$element.find('.bfh-timepicker-toggle > input[type="text"]').val(time);
        this.$element.data('hour', hours);
        this.$element.data('minute', minutes);
        this.$element.data('mode', currentMode);
      }
    },

    initPopover: function () {
      var iconLeft,
              iconRight,
              iconAddon,
              modeAddon,
              modeMax;

      iconLeft = '';
      iconRight = '';
      iconAddon = '';
      if (this.options.icon !== '') {
        if (this.options.align === 'right') {
          iconRight = '<span class="input-group-addon"><i class="' + this.options.icon + '"></i></span>';
        } else {
          iconLeft = '<span class="input-group-addon"><i class="' + this.options.icon + '"></i></span>';
        }
        iconAddon = 'input-group';
      }

      modeAddon = '';
      modeMax = '23';
      if (this.options.mode === '12h') {
        modeAddon = '<td>' +
                '<div class="bfh-selectbox" data-input="' + this.options.input + '" data-value="am">' +
                '<div data-value="am">' + BFHTimePickerModes.am + '</div>' +
                '<div data-value="pm">' + BFHTimePickerModes.pm + '</div>' +
                '</div>';
        modeMax = '11';
      }

      this.$element.html(
              '<div class="' + iconAddon + ' bfh-timepicker-toggle" data-toggle="bfh-timepicker">' +
              iconLeft +
              '<input type="text" name="' + this.options.name + '" class="' + this.options.input + '" placeholder="' + this.options.placeholder + '" >' +
              iconRight +
              '</div>' +
              '<div class="bfh-timepicker-popover">' +
              '<table class="table">' +
              '<tbody>' +
              '<tr>' +
              '<td class="hour">' +
              '<input type="text" class="' + this.options.input + ' bfh-number"  data-min="0" data-max="' + modeMax + '" data-zeros="true" data-wrap="true">' +
              '</td>' +
              '<td class="separator">' + BFHTimePickerDelimiter + '</td>' +
              '<td class="minute">' +
              '<input type="text" class="' + this.options.input + ' bfh-number"  data-min="0" data-max="59" data-zeros="true" data-wrap="true">' +
              '</td>' +
              modeAddon +
              '</tr>' +
              '</tbody>' +
              '</table>' +
              '</div>'
              );

      this.$element
              .on('click.bfhtimepicker.data-api touchstart.bfhtimepicker.data-api', toggle, BFHTimePicker.prototype.toggle)
              .on('click.bfhtimepicker.data-api touchstart.bfhtimepicker.data-api', '.bfh-timepicker-popover > table', function () {
                return true;
              });

      this.$element.find('.bfh-number').each(function () {
        var $number;

        $number = $(this);

        $number.bfhnumber($number.data());

        $number.on('change', BFHTimePicker.prototype.change);
      });

      this.$element.find('.bfh-selectbox').each(function () {
        var $selectbox;

        $selectbox = $(this);

        $selectbox.bfhselectbox($selectbox.data());

        $selectbox.on('change.bfhselectbox', BFHTimePicker.prototype.change);
      });

      this.setTime();

      this.updatePopover();
    },

    updatePopover: function () {
      var hour,
              minute,
              mode;

      hour = this.$element.data('hour');
      minute = this.$element.data('minute');
      mode = this.$element.data('mode');

      this.$element.find('.hour input[type=text]').val(hour).change();
      this.$element.find('.minute input[type=text]').val(minute).change();
      this.$element.find('.bfh-selectbox').val(mode);
    },

    change: function () {
      var $this,
              $parent,
              $timePicker,
              mode;

      $this = $(this);
      $parent = getParent($this);

      $timePicker = $parent.data('bfhtimepicker');

      if ($timePicker && $timePicker !== 'undefined') {
        mode = '';
        if ($timePicker.options.mode === '12h') {
          mode = ' ' + BFHTimePickerModes[$parent.find('.bfh-selectbox').val()];
        }

        $parent.find('.bfh-timepicker-toggle > input[type="text"]').val($parent.find('.hour input[type=text]').val() + BFHTimePickerDelimiter + $parent.find('.minute input[type=text]').val() + mode);

        $parent.trigger('change.bfhtimepicker');
      }

      return false;
    },

    toggle: function (e) {
      var $this,
              $parent,
              isActive;

      $this = $(this);
      $parent = getParent($this);

      if ($parent.is('.disabled') || $parent.attr('disabled') !== undefined) {
        return true;
      }

      isActive = $parent.hasClass('open');

      clearMenus();

      if (!isActive) {
        $parent.trigger(e = $.Event('show.bfhtimepicker'));

        if (e.isDefaultPrevented()) {
          return true;
        }

        $parent
                .toggleClass('open')
                .trigger('shown.bfhtimepicker');

        $this.focus();
      }

      return false;
    }
  };

  function formatTime(hour, minute) {
    hour = String(hour);
    if (hour.length === 1) {
      hour = '0' + hour;
    }

    minute = String(minute);
    if (minute.length === 1) {
      minute = '0' + minute;
    }

    return hour + BFHTimePickerDelimiter + minute;
  }

  function clearMenus() {
    var $parent;

    $(toggle).each(function (e) {
      $parent = getParent($(this));

      if (!$parent.hasClass('open')) {
        return true;
      }

      $parent.trigger(e = $.Event('hide.bfhtimepicker'));

      if (e.isDefaultPrevented()) {
        return true;
      }

      $parent
              .removeClass('open')
              .trigger('hidden.bfhtimepicker');
    });
  }

  function getParent($this) {
    return $this.closest('.bfh-timepicker');
  }


  /* TIMEPICKER PLUGIN DEFINITION
   * ========================== */

  var old = $.fn.bfhtimepicker;

  $.fn.bfhtimepicker = function (option) {
    return this.each(function () {
      var $this,
              data,
              options;

      $this = $(this);
      data = $this.data('bfhtimepicker');
      options = typeof option === 'object' && option;
      this.type = 'bfhtimepicker';

      if (!data) {
        $this.data('bfhtimepicker', (data = new BFHTimePicker(this, options)));
      }
      if (typeof option === 'string') {
        data[option].call($this);
      }
    });
  };

  $.fn.bfhtimepicker.Constructor = BFHTimePicker;

  $.fn.bfhtimepicker.defaults = {
    icon: 'bi bi-clock',
    align: 'left',
    input: 'form-control',
    placeholder: '',
    name: '',
    time: '',
    mode: '24h'
  };


  /* TIMEPICKER NO CONFLICT
   * ========================== */

  $.fn.bfhtimepicker.noConflict = function () {
    $.fn.bfhtimepicker = old;
    return this;
  };


  /* TIMEPICKER VALHOOKS
   * ========================== */

  var origHook;
  if ($.valHooks.div) {
    origHook = $.valHooks.div;
  }
  $.valHooks.div = {
    get: function (el) {
      if ($(el).hasClass('bfh-timepicker')) {
        return $(el).find('.bfh-timepicker-toggle > input[type="text"]').val();
      } else if (origHook) {
        return origHook.get(el);
      }
    },
    set: function (el, val) {
      var $timepicker;
      if ($(el).hasClass('bfh-timepicker')) {
        $timepicker = $(el).data('bfhtimepicker');
        $timepicker.options.time = val;
        $timepicker.setTime();
        $timepicker.updatePopover();
      } else if (origHook) {
        return origHook.set(el, val);
      }
    }
  };


  /* TIMEPICKER DATA-API
   * ============== */

  $(document).ready(function () {
    $('div.bfh-timepicker').each(function () {
      var $timepicker;

      $timepicker = $(this);

      $timepicker.bfhtimepicker($timepicker.data());
    });
  });


  /* APPLY TO STANDARD TIMEPICKER ELEMENTS
   * =================================== */




//some additional code
$(document).on("click", ".bfh-timepicker-popover", function(){
    return false;
});
$(document).on("click", clearMenus);

}(window.jQuery);
