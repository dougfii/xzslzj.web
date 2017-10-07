!function (window, undefined) {
    "use strict";

    var $ = jQuery, win = $(window);

    //roll background image
    var roll = function (settings) {
        var defaults = {
            delay: 3000,
            images: []
        };

        var current = 0;

        settings = $.extend({}, defaults, settings);

        var init = function () {
            setInterval(show, settings.delay);
        };

        var show = function () {
            $('.advs').css('background', "url('" + settings.images[current] + "') no-repeat center top").animate({opacity: 'show'}, settings.delay);
            current++;
            if (current >= settings.images.length)current = 0;
        };

        init();
    };

    $.fn.extend({roll: roll});
}
(window);