;(function ($) {
    'use strict';

    var Stars = function () {
        var methods = {
            init: function () {
                $(document).on('click', '#jsonform-0-elt-generalScore > .radio', methods.handleStar);
            },
            handleStar: function (e) {
                e.preventDefault();

                //Make unchecked all stars

                $('#jsonform-0-elt-generalScore .radio').removeClass('active');
                var stars = $('#jsonform-0-elt-generalScore .radio');
                var number = $(this).find('input:first-child').val();

                $(this).find('input:first-child').attr('checked', true);

                for (var i = 0; i < number; i++) {
                    stars.eq(i).addClass('active');
                }
            }
        };

        methods.init();
    };

    $(document).ready(function () {
        new Stars();
    });
})(window.jQuery, window);