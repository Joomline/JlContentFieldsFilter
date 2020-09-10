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

        document.addEventListener('DOMContentLoaded', function () {
            if (params.autho_send === 1) {
                var sendTimeoutID = 0;

                var els = document.querySelectorAll('input[type="radio"], input[type="checkbox"], select');
                for (var i = 0; i < els.length; i++) {
                    els[i].addEventListener('change', function (el) {
                        params.ajax === 1 ? $this.loadData(id) : el.target.form.submit();
                    });
                }

                var els = document.querySelectorAll('input[type="text"], #' + id);
                for (var i = 0; i < els.length; i++) {
                    els[i].addEventListener('change', function (el) {
                        clearTimeout(sendTimeoutID);
                        sendTimeoutID = setTimeout(function () {
                            params.ajax === 1 ? $this.loadData(id) : el.target.form.submit();
                        }, 500);
                    });
                }
            } else if (params.ajax === 1) {
                document.getElementById(id).addEventListener('submit', function (event) {
                    event.preventDefault();
                    $this.loadData(id);
                });
            }
        });
    },

    clearForm: function (element) {
        var form = element.form;
        var id = form.getAttribute('id');
        var params = this.params[id];

         var els = form.querySelectorAll('input[type="checkbox"], select > option');
        for (var i = 0; i < els.length; i++) {
            if (els[i].checked) {
                els[i].checked = false;
            }
            if (els[i].selected) {
                els[i].selected = false;
            }          
        }

        var els = form.querySelectorAll('input[type="text"]');
        for (var i = 0; i < els.length; i++) {
            els[i].value = ''; 
        }

        var els = form.querySelectorAll('select');
        for (var i = 0; i < els.length; i++) {
            els[i].selectedIndex = 0;
        }

        if (params.ajax === 1 && params.autho_send === 1) {
            this.loadData(id);
        }
        else if (params.autho_send === 1) {
            form.submit();
        }

        return false;
    },

    clearRadio: function (element) {
        var form = element.form;
        var id = form.getAttribute('id');
        var params = this.params[id];

        var els = form.querySelectorAll('input[type="radio"]');
        for (var i = 0; i < els.length; i++) {
            if (els[i].checked) {
                els[i].checked = false;
            }          
        }

        if (params.autho_send === 1) {
            if (params.ajax === 1) {
                this.loadData(id);
            } else {
                document.getElementById(id).submit();
            }
        }
    },

    loadData: function (id) {
        var
            $this = this,
            params = this.params[id],
            form = document.getElementById(id),
            formData = new FormData(form),
            request = new XMLHttpRequest();

        $this.ShowLoadingScreen(id);

        formData.append('tmpl', 'jlcomponent_ajax');

        request.open('POST', form.getAttribute('action'));
        request.send(formData);

        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                try {
                    document.querySelector(params.ajax_selector).innerHTML = this.response;
                } catch (e) {
                    console.log(this.response);
                }
            }
            let event = document.dispatchEvent(new CustomEvent('JlContentFieldsFilterLoadDataSuccess'));
            $this.HideLoadingScreen();
        };

    },

    ShowLoadingScreen: function (id) {
        var params = this.params[id];
        document.body.style.cursor = 'wait';

        var fade_div = document.getElementById('id_admin_forms_fade');

        if (fade_div == null) {
            // Create div
            fade_div = document.createElement('div');
            fade_div.setAttribute('id', 'id_admin_forms_fade');
            fade_div.className = 'shadowed';
            fade_div.setAttribute('style', 'display:none;z-index:1500;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:' + params.ajax_loader_width);
            fade_div.innerHTML = '<img src="' + params.ajax_loader + '" id="id_fade_div_img" />';
            document.body.appendChild(fade_div);
        }

        fade_div.style.display = '';
    },

    HideLoadingScreen: function () {
        document.body.style.cursor = 'auto';
        document.getElementById('id_admin_forms_fade').style.display = 'none';
    }
};
