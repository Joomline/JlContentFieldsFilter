<?php
/** @var $this JlcontentfieldsfilterViewItems */
defined('_JEXEC') or die;// No direct access
$doc = JFactory::getDocument();
$doc->addScript(JUri::root() . 'administrator/components/com_jlcontentfieldsfilter/assets/js/vue.js');
$doc->addScript(JUri::root() . 'administrator/components/com_jlcontentfieldsfilter/assets/js/axios.min.js');
$doc->addScript(JUri::root() . 'administrator/components/com_jlcontentfieldsfilter/assets/js/script.js');
$doc->addStyleSheet(JUri::root() . 'modules/mod_jlcontentfieldsfilter/assets/css/jlcontentfilter.css');
$doc->addStyleSheet(JUri::root() . 'administrator/components/com_jlcontentfieldsfilter/assets/css/style.css');
?>

<div id="app">
    <form id="data-form" v-on:submit.prevent="loadRows">
        <div id="j-sidebar-container" class="span2">
            <div id="j-toggle-sidebar-wrapper">
                <div id="j-toggle-button-wrapper" class="j-toggle-button-wrapper j-toggle-visible">
                    <div id="j-toggle-sidebar-button" class="j-toggle-sidebar-button hidden-phone hasTooltip"
                         onclick="toggleSidebar(false); return false;" data-original-title="Скрыть боковую панель">
                    <span id="j-toggle-sidebar-icon" class="icon-arrow-left-2 j-toggle-visible"
                          aria-hidden="true"></span>
                    </div>
                </div>
                <div id="sidebar" class="sidebar">
                    <div class="sidebar-nav">
                        <ul id="submenu" class="nav nav-list">
                            <li>
                                <label></label>
                                <select name="cid" v-model="cid" v-on:change="loadFilter">
                                    <option value=""><?php echo JText::_('SELECT_CATEGORY'); ?></option>
                                    <?php echo $this->categoryOptions; ?>
                                </select>
                            </li>

                            <li v-for="field in fields">
                                <span v-html="field"></span>
                            </li>

                            <li>
                                <span v-html="button"></span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="j-toggle-sidebar"></div>
            </div>

        </div>
        <div id="j-main-container" class="span10">
            <button class="btn btn-success" id="show-modal" v-on:click="AddRow">Add New</button>
            <br>
            <br>
            <demo-grid :rows="gridData" :columns="gridColumns"></demo-grid>
        </div>

        <!-- use the modal component, pass in the prop -->
        <modal v-if="showModal" @close="showModal = false">
            <h3 slot="header">Edit item</h3>
            <div slot="body">
                <input type="hidden" name="id" v-bind:value="id">
                <!--            <input type="hidden" name="cid" v-bind:value="cid">-->
                <label>Title</label>
                <input type="text" name="meta_title" v-bind:value="title">
                <label>Meta Description</label>
                <textarea name="meta_desc">{{meta_desc}}</textarea>
                <label>Meta Keywords</label>
                <textarea name="meta_keywords">{{meta_keywords}}</textarea>
                <label>Publish</label>
                <input type="checkbox" name="publish" value="1" v-bind:checked="publish == 1">
            </div>
            <div slot="footer">
                <button class="modal-default-button" @click="SaveRow">Save</button>
                <button class="modal-default-button" @click="Chancel">Chancel</button>
            </div>
        </modal>
    </form>
</div>


<!-- template for the modal component -->
<script type="text/x-template" id="modal-template">
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="vue-modal-container">

                    <div class="vue-modal-header">
                        <slot name="header">
                            default header
                        </slot>
                    </div>

                    <div class="vue-modal-body">
                        <slot name="body">
                            default body
                        </slot>
                    </div>

                    <div class="vue-modal-footer">
                        <slot name="footer">
                            <button class="modal-default-button" @click="$emit('close')">OK</button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</script>

<!-- grid template -->
<script type="text/x-template" id="grid-template">
    <table width="100%">
        <thead>
        <tr>
            <th v-for="key in columns" class="key">{{ key | capitalize }}</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="entry in sortedRows">
            <td v-for="key in columns">
                {{entry[key]}}
            </td>
            <td>
                <a class="btn btn-small btn-success" href="#" v-on:click="EditRow(entry.id);">Edit</a>
                <a class="btn btn-small btn-danger" href="#" v-on:click="DeleteRow(entry.id);">Delete</a>
            </td>
        </tr>
        </tbody>
    </table>
</script>
