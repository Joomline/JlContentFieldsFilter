var JlContentFieldsFilter = {
    autho_send: 0,
    ajax: 0,
    ajax_selector: '#content',
    ajax_loader: '/modules/mod_jlcontentfieldsfilter/assets/images/ajax_loader.gif',
    ajax_loader_width: 32,
    form_identifier: '#mod-finder-searchform',
    init: function (data) {
        if (typeof data.autho_send !== 'undefined') {
            this.autho_send = data.autho_send;
        }
        if (typeof data.ajax !== 'undefined') {
            this.ajax = data.ajax;
        }
        if (typeof data.ajax_selector !== 'undefined') {
            this.ajax_selector = data.ajax_selector;
        }
        if (typeof data.ajax_loader !== 'undefined' && data.ajax_loader != '') {
            this.ajax_loader = data.ajax_loader;
        }
        if (typeof data.form_identifier !== 'undefined') {
            this.form_identifier = data.form_identifier;
        }
        var $this = this;
        jQuery(document).ready(function () {
            if ($this.autho_send === 1) {
                var sendTimeoutID = 0;
                jQuery('input[type="radio"], input[type="checkbox"], select', $this.form_identifier)
                    .on('change', function () {
                        $this.ajax === 1 ? $this.loadData() : jQuery(this).parents('form').submit();
                    });
                jQuery('input[type="text"]', $this.form_identifier)
                    .on('keyup', function () {
                        var input = jQuery(this);
                        clearTimeout(sendTimeoutID);
                        sendTimeoutID = setTimeout(function () {
                            $this.ajax === 1 ? $this.loadData() : input.parents('form').submit();
                        }, 500);
                    });
            }
            else if($this.ajax === 1){
                jQuery('input[type="submit"]', $this.form_identifier).on('click', function (event) {
                        event.preventDefault();
                        $this.loadData();
                    });
            }
        });
    },
    clearForm: function (element) {
        var form = jQuery(element).parents('form');
        jQuery(':checked, :selected, select', form)
            .not(':button, :submit, :reset, :hidden')
            .removeAttr('checked')
            .removeAttr('selected');
        jQuery('input[type="text"]', form).val('');
        if (this.ajax === 1 && this.autho_send === 1) {
            this.loadData();
        }
        else if(this.autho_send === 1){
            jQuery($this.form_identifier).submit();
        }
        return false;
    },
    clearRadio: function (element) {
        jQuery(element).parent().find('input[type="radio"]:checked').removeAttr('checked');
        if (this.autho_send === 1 ) {
            if (this.ajax === 1) {
                this.loadData();
            }
            else{
                jQuery(element).parents('form').submit();
            }
        }
    },
    loadData: function () {
        var $this = this;
        $this.ShowLoadingScreen();
        var form = jQuery($this.form_identifier);
        jQuery.ajax({
            type: 'POST',
            url: form.attr('action'),
            cache: 'false',
            data: form.serialize() + '&tmpl=component',
            dataType: 'html',
            success: function (data) {
                jQuery($this.ajax_selector).html(data);
                $this.HideLoadingScreen();
            }
        });
    },
    ShowLoadingScreen: function () {
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
                .append('<img src="'+this.ajax_loader+'" id="id_fade_div_img" />')
                .css('width', this.ajax_loader_width);
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



