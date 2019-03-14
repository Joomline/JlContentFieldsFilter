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

                document.querySelectorAll('input[type="radio"], input[type="checkbox"], select, #' + id).forEach(function (e) {
                    e.addEventListener('change', function (el) {
                        params.ajax === 1 ? $this.loadData(id) : el.closest('form').submit();
                    });
                });

                document.querySelectorAll('input[type="text"], #' + id).forEach(function (e) {
                    e.addEventListener('change', function (el) {
                        clearTimeout(sendTimeoutID);
                        sendTimeoutID = setTimeout(function () {
                            params.ajax === 1 ? $this.loadData(id) : el.closest('form').submit();
                        }, 500);
                    });
                });
            } else if (params.ajax === 1) {
                document.getElementById(id).addEventListener('submit', function (event) {
                    event.preventDefault();
                    $this.loadData(id);
                });
            }
        });
    },

    clearForm: function (element) {
        var form = element.closest('form');
        var id = form.getAttribute('id');
        var params = this.params[id];

        form.querySelectorAll('input[type="checkbox"], select>option, select').forEach(function (el) {
            if (el.checked) {
                el.checked = false;
            }
            if (el.selected) {
                el.selected = false;
            }
        });

        form.querySelectorAll('input[type="text"]').forEach(function (el) {
            el.value = '';
        });

        form.querySelectorAll('select').forEach(function (el) {
            el.selectedIndex = 0;
        });

        if (params.ajax === 1 && params.autho_send === 1) {
            this.loadData(id);
        }
        else if (params.autho_send === 1) {
            document.getElementById(id).submit();
        }

        return false;
    },

    clearRadio: function (element) {
        var form = element.closest('form');
        var id = form.getAttribute('id');
        var params = this.params[id];

        form.querySelectorAll('input[type="radio"], select>option, select').forEach(function (el) {
            if (el.checked) {
                el.checked = false;
            }
        });

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
