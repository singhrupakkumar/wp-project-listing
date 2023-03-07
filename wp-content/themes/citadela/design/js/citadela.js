jQuery(document).ready(function ($) {
    $('.citadela-installation').click(function () {
        $(this).addClass('hidden').removeClass('visible');
        $(this).parent().find('.citadela-installation-progress').addClass('visible').removeClass('hidden');
        $.get($(this).attr('href'), function (response) {
            $('.citadela-installation-progress').addClass('hidden').removeClass('visible');
            if (response.success) {
                $('#citadela-installation').addClass('hidden').removeClass('visible');
                $('.citadela-installation-success').addClass('visible').removeClass('hidden');
                if (response.data.redirect) {
                    window.location.replace(response.data.redirect);
                }
            } else {
                if (response.data.message) {
                    $('.citadela-installation-error').html(response.data.message);
                }
                $('.citadela-installation-error').addClass('visible').removeClass('hidden');
            }
        });
        return false;
    });
    if ($('.setting-domain').length && navigator.clipboard && location.protocol === 'https:') {
        var input = $('#citadela_domain');
        input.addClass('with-button');
        var button = $('<button type="button">Copy</button>');
        var bubble = $('<div class="citadela-bubble">Copied!</div>');
        button.click(function () {
            navigator.clipboard.writeText(input.val());
            button.addClass('copied');
            bubble.fadeIn({
                complete: function () {
                    button.removeClass('copied');
                    setTimeout(function () {
                        bubble.fadeOut();
                    }, 2000);
                }
            });
        });
        button.insertAfter(input);
        bubble.hide().insertAfter(input);
    }

    customHeader('citadela-custom-header')

    function customHeader(pageClass) {
        var page = $('.' + pageClass);

        if (!page.length) return;

        var headerEnd = $('.wp-header-end');

        if (!'ResizeObserver' in window || !headerEnd.length) {
            page.removeClass(pageClass);
            return;
        }

        var headerWrap = $('#wpcontent');

        function setHeight() {
            var oldHeight = headerWrap.css('--ctdl-header-height'),
                newHeight = Math.floor(headerEnd.offset().top - headerWrap.offset().top) + 'px';

            if (oldHeight != newHeight) headerWrap.css('--ctdl-header-height', newHeight);
        }

        setHeight();
        page.addClass(pageClass + '-active');
        (new ResizeObserver(setHeight)).observe(headerWrap.find('#wpbody-content')[0], {box: 'border-box'});
    }
});
