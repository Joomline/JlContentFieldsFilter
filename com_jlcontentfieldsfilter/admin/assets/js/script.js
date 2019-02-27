

window.addEventListener("load", function(event) {

    Vue.component('modal', {
        template: '#modal-template'
    });

    // register the grid component
    Vue.component('demo-grid', {
        template: '#grid-template',
        props: {
            rows: Array,
            columns: Array
        },
        data: function () {
            return {};
        },
        computed: {
            sortedRows: function () {
                return this.rows;
            }
        },
        filters: {
            capitalize: function (str) {
                return str.charAt(0).toUpperCase() + str.slice(1)
            }
        },
        methods: {
            EditRow: function (id) {
                app.EditRow(id);

            },
            DeleteRow: function (id) {
                app.DeleteRow(id);
            }
        }
    });

    var app = new Vue({
        el: '#app',
        data: {
            id: '',
            cid: '',
            title: '',
            meta_desc: '',
            meta_keywords: '',
            publish: 0,
            fields: [],
            getFormUrl: '/administrator/index.php?option=com_jlcontentfieldsfilter&task=items.get_form&cid=',
            getRowsUrl: '/administrator/index.php?option=com_jlcontentfieldsfilter&task=items.get_rows',
            saveUrl: '/administrator/index.php?option=com_jlcontentfieldsfilter&task=items.save',
            deleteUrl: '/administrator/index.php?option=com_jlcontentfieldsfilter&task=items.delete&id=',
            formData: {},
            button: '',
            showModal: false,
            gridColumns: ['catid', 'filter', 'meta_title', 'meta_desc', 'meta_keywords'],
            gridData: [],
            task: '',
            formSelector: '#data-form'
        },
        methods: {
            loadFilter: function () {
                var $this = this;
                if(this.cid == ''){
                    this.form = '';
                    this.button = '';
                    return;
                }

                axios.get(this.getFormUrl+this.cid)
                    .then(function (response) {
                        if(response.data.error){
                            alert(response.data.message);
                            $this.button = '';
                        }
                        else{
                            $this.fields = response.data.fields;
                            $this.button = '<br><button>Отобрать</button>';
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .then(function () {
                        // always executed
                    });
            },
            AddRow: function (submitEvent) {
                submitEvent.preventDefault();
                if(this.cid == ''){
                    alert('Select Category');
                    return false;
                }
                this.id = '';
                this.title = '';
                this.meta_desc = '';
                this.meta_keywords = '';
                this.publish = 1;
                this.showModal = true;
            },
            Chancel: function (submitEvent) {
                submitEvent.preventDefault();
                this.showModal = false;
            },
            EditRow: function (id) {
                var data = this.gridData[id];
                this.id = data.id;
                this.title = data.meta_title;
                this.meta_desc = data.meta_desc;
                this.meta_keywords = data.meta_keywords;
                this.publish = data.publish;
                this.showModal = true;
            },
            DeleteRow: function (id) {
                if(!confirm('Delete?')){
                    return;
                }
                var $this = this;
                axios.get(this.deleteUrl+id)
                    .then(function (response) {
                        if(response.data.error){
                            alert(response.data.message);
                        }
                        else{
                            $this.loadRows(event);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .then(function () {
                        // always executed
                    });
            },
            SaveRow: function (submitEvent) {
                var $this = this;
                submitEvent.preventDefault();
                this.formData = jQuery(this.formSelector).serialize();
                axios.post(this.saveUrl, this.formData)
                    .then(function (response) {
                        if(response.data.error){
                            alert(response.data.message);
                        }
                        else{
                            $this.showModal = false;
                            $this.loadRows(submitEvent);

                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .then(function () {
                        // always executed
                    });
            },
            loadRows: function (submitEvent) {
                submitEvent.preventDefault();
                var $this = this;
                this.formData = jQuery(this.formSelector).serialize();
                axios.post(this.getRowsUrl, this.formData)
                    .then(function (response) {
                        if(response.data.error){
                            alert(response.data.message);
                        }
                        else{
                            $this.gridData = response.data.rows;
                            var qwe = 1;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .then(function () {
                        // always executed
                    });
            }
        }
    });
});
