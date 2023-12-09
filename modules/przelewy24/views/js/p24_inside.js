/*
* @author Przelewy24
* @copyright Przelewy24
* @license https://www.gnu.org/licenses/lgpl-3.0.en.html
*/

var p24GetRawAdditionalFormsContainer = function () {
    var idVar = 'p24-additional-forms';
    var rawContainer = document.getElementById(idVar);
    if (!rawContainer) {
        rawContainer = document.createElement('div');
        rawContainer.id = idVar;
        document.body.appendChild(rawContainer);
    }

    return rawContainer;
};

/* BLIK */
$(function(){
    var $configElement;
    var armed = false;

    var commonError = function(selector) {
        var $section = $('#p24-blik-section');
        var $error = $section.find(selector);
        $error.show();
        $error.addClass('animate');
        setTimeout(function () {
            $error.removeClass('animate');
        }, 2000);
    };

    var blikError = function() {
        commonError('.error-code');
    };

    var regulationError = function() {
        commonError('.error-regulation');
    };

    var regulationNoError = function() {
        var $section = $('#p24-blik-section');
        var $error = $section.find('.error-regulation');
        $error.hide();
    };

    var prRegulationError = function() {
        commonError('.error-pr-regulation');
    };

    var prRegulationNoError = function() {
        var $section = $('#p24-blik-section');
        var $error = $section.find('.error-pr-regulation');
        $error.hide();
    };

    var executePaymentByBlikCode = function($form, cartId, blikCode) {
        $form.find('button').prop('disabled', true);
        var request = {
            'action': 'executeBlik',
            'cartId': cartId,
            'blikCode': blikCode
        };
        $.ajax($configElement.data('ajaxurl'), {
            method: 'POST', type: 'POST',
            data: request,
        }).success(function (response) {
            var response = JSON.parse(response);
            if (response.success || response.reload) {
                var returnUrl = response.returnUrl;
                /* We are giving few seconds for user to accept transaction. */
                setTimeout(function() {window.location = returnUrl;}, 3000);
            } else {
                blikError();
                $form.find('button').prop('disabled', false);
            }
        }).error(function () {
            blikError();
            $form.find('button').prop('disabled', false);
        });
    };

    var checkRegulations = function ()
    {
        var $regulation;
        $regulation = $('#p24_regulation_accept');
        if (!$regulation.length) {
            $regulation = $('#p24-blik-regulation-accept');
        }
        if (!$regulation.length) {
            /* Not possible on valid site. */
            return false;
        }
        if ($regulation.prop('checked')) {
            regulationNoError();
            return true;
        } else {
            regulationError();
            return false;
        }
    };

    var checkPrRegulations = function ()
    {
        var $regulation;
        $regulation = $('#conditions_to_approve\\[terms-and-conditions\\]');
        if (!$regulation.length) {
            /* Accepted or not needed. */
            return true;
        }
        if ($regulation.prop('checked')) {
            prRegulationNoError();
            return true;
        } else {
            prRegulationError();
            return false;
        }
    };

    var showBlikSection  = function() {
        var $section = $('#p24-blik-section');
        var $pad = $('#p24-additional-forms');
        $section.appendTo($pad);
        $section.show();
        var cartId = $configElement.data('cartid');
        var $form = $section.find('form');
        $form.on('submit', function (e) {
            e.preventDefault();
            var regulations = checkRegulations();
            var prRegulations = checkPrRegulations();
            if (regulations && prRegulations) {
                var blikCode = $section.find('input[name=blik]').val();
                executePaymentByBlikCode($form, cartId, blikCode);
            }
        });
    };

    var hideBlikSection = function () {
        var $section = $('#p24-blik-section');
        $section.hide();
    }

    var tryArmBlikBoxPayment = function() {
        var $masterMethodId = $('#master-active-payment-method');
        $masterMethodId.on('change', function () {
            var val = parseInt($masterMethodId.val());
            if (181 === val) {
                showBlikSection();
            } else {
                hideBlikSection();
            }
        });
        armed = true;
    };

    var tryArmBlikBoxConfirmation = function() {
        /* The id is too random to use. */
        var $input = $('input[data-module-name=przelewy24-method-181]');
        if ($input.length) {
            var rawFormContainer = p24GetRawAdditionalFormsContainer();
            $input.on('change', function (e) {
                if (!$input.prop('checked')) {
                    /* Nothing to do. */
                    return;
                }

                var randomId = $input.attr('id');
                var $container = $('#' + randomId + '-container');
                var $formContainer = $(rawFormContainer);
                $container.append($formContainer);
                $formContainer.trigger('hide-old');
                var hideOldEvent = new Event('hide-old');
                rawFormContainer.dispatchEvent(hideOldEvent);
                showBlikSection();
            });
            rawFormContainer.addEventListener('hide-old', function() {
                hideBlikSection();
            });

            armed = true;
        }
    };

    var tryArmBlikBox = function(retries) {
        if (armed || retries <= 0) {
            return;
        }

        $configElement = $('#p24-blik-config-element');
        if ($configElement.length) {
            var pageType = $configElement.data('pagetype');
            switch (pageType) {
                case 'payment':
                    tryArmBlikBoxPayment();
                    break;
                case 'confirmation':
                    tryArmBlikBoxConfirmation();
                    break;
            }
        }

        if (!armed) {
            setTimeout(tryArmBlikBox, 1000, retries - 1);
        }
    };

    tryArmBlikBox(10);
});

/* Card */
$(function(){
    var $configElement;
    var armed = false;

    var commonError = function(selector) {
        var $section = $('#p24-card-section');
        var $error = $section.find(selector);
        $error.show();
        $error.addClass('animate');
        setTimeout(function () {
            $error.removeClass('animate');
        }, 2000);
    };

    var otherError = function () {
        commonError('error-other');
    }

    var regulationError = function() {
        commonError('.error-regulation');
    };

    var regulationNoError = function() {
        var $section = $('#p24-card-section');
        var $error = $section.find('.error-regulation');
        $error.hide();
    };

    var prRegulationError = function() {
        commonError('.error-pr-regulation');
    };

    var prRegulationNoError = function() {
        var $section = $('#p24-card-section');
        var $error = $section.find('.error-pr-regulation');
        $error.hide();
    };

    var executePaymentByCard = function($form, cartId, holder, number, date_y, date_m, cvv_nr, method) {
        $form.find('button').prop('disabled', true);
        var request = {
            action: 'executeCardParams',
            cartId: cartId,
            holder: holder,
            number: number,
            date_m: date_m,
            date_y: date_y,
            cvv_nr: cvv_nr,
            method: method
        };
        $.ajax($configElement.data('ajaxurl'), {
            method: 'POST', type: 'POST',
            data: request,
        }).success(function (response) {
            var response = JSON.parse(response);
            if (response.success || response.reload) {
                var returnUrl = response.returnUrl;
                /* We are giving few seconds for user to accept transaction. */
                setTimeout(function() {window.location = returnUrl;}, 3000);
            } else {
                commonError();
                $form.find('button').prop('disabled', false);
            }
        }).error(function () {
            commonError();
            $form.find('button').prop('disabled', false);
        });
    };

    var checkRegulations = function ()
    {
        var $regulation;
        $regulation = $('#p24_regulation_accept');
        if (!$regulation.length) {
            $regulation = $('#p24-card-regulation-accept');
        }
        if (!$regulation.length) {
            /* Not possible on valid site. */
            return false;
        }
        if ($regulation.prop('checked')) {
            regulationNoError();
            return true;
        } else {
            regulationError();
            return false;
        }
    };

    var checkPrRegulations = function ()
    {
        var $regulation;
        $regulation = $('#conditions_to_approve\\[terms-and-conditions\\]');
        if (!$regulation.length) {
            /* Accepted or not needed. */
            return true;
        }
        if ($regulation.prop('checked')) {
            prRegulationNoError();
            return true;
        } else {
            prRegulationError();
            return false;
        }
    };

    var showCardSection  = function() {
        var $section = $('#p24-card-section');
        var $pad = $('#p24-additional-forms');
        $section.appendTo($pad);
        $section.show();
        var cartId = $configElement.data('cartid');
        var $form = $section.find('form');
        $form.on('submit', function (e) {
            e.preventDefault();
            var regulations = checkRegulations();
            var prRegulations = checkPrRegulations();
            if (regulations && prRegulations) {
                var $masterMethodId = $('#master-active-payment-method');
                var holder = $form.find('input[name=card-holder]').val();
                var number = $form.find('input[name=card-number]').val();
                var date_y = $form.find('select[name=exp-date-year]').val();
                var date_m = $form.find('select[name=exp-date-month]').val();
                var cvv_nr = $form.find('input[name=cvv]').val();
                var method = parseInt($masterMethodId.val());
                executePaymentByCard($form, cartId, holder, number, date_y, date_m, cvv_nr, method);
            }
        });
    };

    var hideCardSection = function () {
        var $section = $('#p24-card-section');
        $section.hide();
    }

    var tryArmCardBoxPayment = function($configElement) {
        var ids = $configElement.data('ids').split(',');

        var $masterMethodId = $('#master-active-payment-method');
        $masterMethodId.on('change', function () {
            var val = parseInt($masterMethodId.val());
            var notSelected = ids.every(function (id) {
                id = parseInt(id);
                return id !== val;
            });
            if (notSelected) {
                hideCardSection();
            } else {
                showCardSection();
            }
        });

        armed = true;
    }

    var tryArmCardBoxConfirmationOne = function(id) {
        /* It could be armed for different card id. */
        if (armed) {
            return;
        }

        /* The id is too random to use. */
        var $input = $('input[data-module-name=przelewy24-method-' + id + ']');
        if ($input.length) {
            var rawFormContainer = p24GetRawAdditionalFormsContainer();
            $input.on('change', function (e) {
                if (!$input.prop('checked')) {
                    /* Nothing to do. */
                    return;
                }

                var randomId = $input.attr('id');
                var $container = $('#' + randomId + '-container');
                var $formContainer = $(rawFormContainer);
                $container.append($formContainer);
                $formContainer.trigger('hide-old');
                var hideOldEvent = new Event('hide-old');
                rawFormContainer.dispatchEvent(hideOldEvent);
                showCardSection();
            });
            rawFormContainer.addEventListener('hide-old', function() {
                hideCardSection();
            });

            armed = true;
        }
    };

    var tryArmCardBoxConfirmation = function() {
        var ids = $configElement.data('ids').split(',');
        ids.forEach(function (id) {
            tryArmCardBoxConfirmationOne(id);
        });
    };

    var tryArmCardBox = function(retries) {
        if (armed || retries <= 0) {
            return;
        }

        $configElement = $('#p24-card-config-element');
        if ($configElement.length) {
            var pageType = $configElement.data('pagetype');
            switch (pageType) {
                case 'payment':
                    tryArmCardBoxPayment($configElement);
                    break;
                case 'confirmation':
                    tryArmCardBoxConfirmation();
                    break;
            }
        }

        if (!armed) {
            setTimeout(tryArmCardBox, 1000, retries - 1);
        }
    };

    tryArmCardBox(10);
});

/* Other methods: from Przelewy24 and other vendors */
$(function(){
    var armed = false;

    var checkForBlikOrCard = function ($configElement, number) {
        /* This list is not empty. */
        var ids = $configElement.data('cardIds').split(',');
        ids.push('181'); /* Blik */
        number = number.toString();

        return ids.indexOf(number) >= 0;
    };

    var tryArmOtherConfirmation = function ($configElement) {
        var rawFormContainer = p24GetRawAdditionalFormsContainer();
        var paymentOptions = document.querySelectorAll('input[name=payment-option]');
        paymentOptions.forEach(function (elm) {
            var needArming;
            var result = /^przelewy24-method-(\d+)$/.exec(elm.dataset.moduleName);
            if (result) {
                needArming = !checkForBlikOrCard($configElement, parseInt(result[1]));
            } else {
                /* Different module. */
                needArming = true;
            }

            console.log(elm.dataset.moduleName, needArming)

            if (needArming) {
                elm.addEventListener('change', function (e) {
                    var hideOldEvent = new Event('hide-old');
                    rawFormContainer.dispatchEvent(hideOldEvent);
                });
            }
        });

        armed = true;
    };

    var tryArmOther = function(retries) {
        if (armed || retries <= 0) {
            return;
        }

        $configElement = $('#p24-other-config-element');
        if ($configElement.length) {
            var pageType = $configElement.data('pagetype');
            switch (pageType) {
                case 'confirmation':
                    tryArmOtherConfirmation($configElement);
                    break;
            }
        }

        if (!armed) {
            setTimeout(tryArmOther, 1000, retries - 1);
        }
    };

    tryArmOther(10);
});
