<?php
/**
 * JL Content Fields Filter
 *
 * @version          @version@
 * @author           Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license          GNU General Public License version 2 or later; see    LICENSE.txt
 */

/** @var $this JlcontentfieldsfilterViewItems */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$doc = Factory::getApplication()->getDocument();
$doc->addScript(Uri::root() . 'media/com_jlcontentfieldsfilter/js/vue.js');
$doc->addScript(Uri::root() . 'media/com_jlcontentfieldsfilter/js/axios.min.js');
$doc->addScript(Uri::root() . 'media/com_jlcontentfieldsfilter/js/script.js');
$doc->addStyleSheet(Uri::root() . 'media/mod_jlcontentfieldsfilter/css/jlcontentfilter.css');
$doc->addStyleSheet(Uri::root() . 'media/com_jlcontentfieldsfilter/css/style.css');
?>

<div id="app">
    <form id="data-form" v-on:submit.prevent="loadRows" class="row">
        <div id="j-sidebar-container" class="col-12 col-md-3 bg-light px-2 border">
            <div id="j-toggle-sidebar-wrapper">
                <div id="sidebar" class="sidebar">
                    <div class="sidebar-nav">
                        <ul id="submenu" class="nav nav-list">
                            <li class="mb-3">
                                <label class="form-label"><?php echo Text::_('JCATEGORY'); ?></label>
                                <select name="cid" class="form-select" v-model="cid" v-on:change="loadFilter">
                                    <option value=""><?php echo Text::_('SELECT_CATEGORY'); ?></option>
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
        <div id="j-main-container" class="col-12 col-md-9">
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
                <div class="mb-3">
                    <label class="form-label" for="meta_title">Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control" v-bind:value="title">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="meta_desc">Meta Description</label>
                    <textarea name="meta_desc" id="meta_desc" class="form-control">{{meta_desc}}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="meta_keywords">Meta Keywords</label>
                    <textarea name="meta_keywords" id="meta_keywords" class="form-control">{{meta_keywords}}</textarea>
                </div>
                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" name="publish" id="publish" class="form-check-input" value="1"
                           v-bind:checked="publish == 1">
                    <label class="form-check-label" for="publish">Publish</label>
                </div>
            </div>
            <div slot="footer">
                <button class="modal-default-button btn btn-sm btn-danger" @click="SaveRow">Save</button>
                <button class="modal-default-button btn btn-sm btn-success" @click="Chancel">Cancel</button>
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
