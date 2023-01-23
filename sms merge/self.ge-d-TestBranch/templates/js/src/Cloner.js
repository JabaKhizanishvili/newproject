(function ($) {
    $.fn.FieldCloner = function (options) {
        var settings = $.extend({
            markup: null,
            maxClone: null,
            cloneID: null,
            target: null,
            index: 1,
            count: 1,
            replacer: '{ID}',
            CallBack: function (s) {
            }
        }, options);
        $(this).click(function ()
        {
            if (settings.count < settings.maxClone)
            {
                var html = settings.markup.replace(new RegExp(settings.replacer, 'g'), settings.index);
                $(settings.target).append(html);
                var ClonedObject = $(settings.cloneID + settings.index);
                settings.CallBack(ClonedObject);
                settings.count++;
                settings.index++;
            }
            return false;
        });
        $("body").on("click", ".removeclass", function (e) {
            $(this).parent('div').remove();
            settings.count--;
            return false;
        });
    };
}(jQuery));

