var JlContentFieldsFilter = {
    params: [],
    init: function (data) {
        var id = data.form_identifier;
        this.params[id] = {
            autho_send: typeof data.autho_send !== 'undefined' ? data.autho_send : 0,
            ajax: typeof data.ajax !== 'undefined' ? data.ajax : 0,
            ajax_selector: typeof data.ajax_selector !== 'undefined' ? data.ajax_selector : '#content',
            ajax_loader: typeof data.ajax_loader !== 'undefined' && data.ajax_loader != '' ? data.ajax_loader : '/modules/mod_jlcontentfieldsfilter/assets/images/ajax_loader.gif',
            ajax_loader_width: typeof data.ajax_loader_width !== 'undefined' ? data.ajax_loader_width : 32
        };
        var $this = this;
        var params = this.params[id];
        jQuery(document).ready(function () {
            if (params.autho_send === 1) {
                var sendTimeoutID = 0;
                jQuery('input[type="radio"], input[type="checkbox"], select', '#' + id)
                    .on('change', function () {
                        params.ajax === 1 ? $this.loadData(id) : jQuery(this).parents('form').submit();
                    });
                jQuery('input[type="text"]', '#' + id)
                    .on('keyup', function () {
                        var input = jQuery(this);
                        clearTimeout(sendTimeoutID);
                        sendTimeoutID = setTimeout(function () {
                            params.ajax === 1 ? $this.loadData(id) : input.parents('form').submit();
                        }, 500);
                    });
            }
            else if (params.ajax === 1) {
                jQuery('#' + id).on('submit', function (event) {
                    event.preventDefault();
                    $this.loadData(id);
                });
            }
        });
    },
    clearForm: function (element) {
        var form = jQuery(element).parents('form');
        var id = form.attr('id');
        var params = this.params[id];
        form.find(':checked, :selected, select')
            .not('[type="button"], [type="submit"], [type="reset"], [type="hidden"]')
            .removeAttr('checked')
            .removeAttr('selected');
        form.find('input[type="text"]').val('');
        form.find('select').prop('selectedIndex', 0);
        if (params.ajax === 1 && params.autho_send === 1) {
            this.loadData(id);
        }
        else if (params.autho_send === 1) {
            jQuery(id).submit();
        }
        return false;
    },
    clearRadio: function (element) {
        var form = jQuery(element).parents('form');
        var id = form.attr('id');
        var params = this.params[id];
        jQuery(element).parent().find('input[type="radio"]:checked').removeAttr('checked');
        if (params.autho_send === 1) {
            if (params.ajax === 1) {
                this.loadData(id);
            }
            else {
                jQuery(element).parents('form').submit();
            }
        }
    },
    loadData: function (id) {
        var $this = this;
        var params = this.params[id];
        var form = jQuery('#' + id);
        $this.ShowLoadingScreen(id);
        jQuery.ajax({
            type: 'POST',
            url: form.attr('action'),
            cache: 'false',
            data: form.serialize() + '&tmpl=jlcomponent_ajax',
            dataType: 'html',
            success: function (data) {
                jQuery(params.ajax_selector).html(data);
                let event = document.dispatchEvent(new CustomEvent('JlContentFieldsFilterLoadDataSuccess'));
                $this.HideLoadingScreen();
            }
        });
    },
    ShowLoadingScreen: function (id) {
        var params = this.params[id];
        jQuery("body").css("cursor", "wait");

        var fade_div = jQuery("#id_admin_forms_fade");

        if (fade_div.length == 0) {
            // Создаем div
            fade_div = jQuery('<div></div>')
                .appendTo(document.body)
                .hide()
                .attr('id', "id_admin_forms_fade")
                .attr('className', "shadowed")
                .css('z-index', "1500")
                .css('position', "absolute")
                .css('left', "50%")
                .css('top', "50%")
                .append('<img src="' + params.ajax_loader + '" id="id_fade_div_img" />')
                .css('width', params.ajax_loader_width);
        }

        fade_div
            .show()
            .css('top', (jQuery(window).height() - fade_div.outerHeight(true)) / 2 + jQuery(window).scrollTop())
            .css('left', (jQuery(window).width() - fade_div.outerWidth(true)) / 2 + jQuery(window).scrollLeft());
    },
    HideLoadingScreen: function () {
        jQuery("body").css("cursor", "auto");
        jQuery("#id_admin_forms_fade").css('display', 'none');
    }
};



