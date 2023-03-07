jQuery(function ($) {
    var block = $('#citadela-claim-listing');
    var buttonShow = $('#citadela-claim-listing-button button');
    var buttonClaim = $('#citadela-claim-listing-button-claim');
    var form = $('#citadela-claim-listing-form');
    var loader = form.find('i.fa');
    function showError(text) {
        if (!text) {
            text = form.find('#citadela-claim-listing-notification-empty').text();
        }
        form.find('.msg-error p').html(text);
        form.find('.msg-error').show();
    }
    function claim(token) {
        var data = {
            action: 'citadela_claim_listing',
            id: form.find('input[name=id]').val()
        };
        if (form.find('input[name=username]').length) {
            data.username = form.find('input[name=username]').val();
            data.email = form.find('input[name=email]').val();
        }
        if (form.find('select[name=product]').length) {
            data.product = form.find('select[name=product]').val();
        }
        if (token) {
            data.token = token;
        }
        $.post(citadela.ajax.url, data, function (response) {
            buttonClaim.prop('disabled', false);
            loader.hide();
            if (response.success) {
                form.find('.msg-success').show();
                if (response.data.redirect) {
                    setTimeout(function () { location.replace(response.data.redirect); }, 2000);
                }
            } else {
                showError(response.data.message);
            }
        }).fail(function () {
            form.find('.msg-error-server').show();
        });
    }
    buttonShow.click(function () {
        if (block.hasClass('closed')) {
            block.removeClass('closed').addClass('opened');
            form.slideDown('slow');
        } else {
            form.slideUp('slow', function () {
                block.removeClass('opened').addClass('closed');
            });
        }
    });
    buttonClaim.click(function () {
        var hasError = false;
        form.find('.msg').hide();
        form.find('.input-container').removeClass('has-error');
        if (form.find('input[name=email]').length && !form.find('input[name=email]').val()) {
            form.find('.input-container.email').addClass('has-error');
            hasError = true;
        }
        if (form.find('input[name=username]').length && !form.find('input[name=username]').val()) {
            form.find('.input-container.username').addClass('has-error');
            hasError = true;
        }
        if ((form.find('input[name=terms]').length && !form.find('input[name=terms]').is(':checked'))) {
            form.find('.input-container.terms').addClass('has-error');
            hasError = true;
        }
        if (hasError) {
            showError();
            return false;
        }
        buttonClaim.prop('disabled', true);
        loader.show();
        if (form.hasClass('active-captcha')) {
            grecaptcha.ready(function () {
                grecaptcha.execute(citadela.keys.recaptchaSiteKey, { action: 'item_claim_listing' }).then(function (token) {
                    claim(token);
                });
            });
        } else {
            claim();
        }
        return false;
    });
});