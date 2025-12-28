<?php
/**
 * JL Content Fields Filter
 *
 * @version          @version@
 * @author           Joomline
 * @copyright  (C) 2017-2025 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license          GNU General Public License version 2 or later; see    LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

HTMLHelper::_('bootstrap.modal');
?>

<form action="<?php echo Route::_('index.php?option=com_jlcontentfieldsfilter&view=items'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="alert alert-info">
                    <strong><?php echo Text::_('COM_JLCONTENTFIELDSFILTER'); ?></strong>
                    <p><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_DESC'); ?></p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label" for="filter-category"><?php echo Text::_('JCATEGORY'); ?></label>
                    <select name="filter_category" id="filter-category" class="form-select">
                        <option value=""><?php echo Text::_('SELECT_CATEGORY'); ?></option>
                        <?php echo $this->categoryOptions; ?>
                    </select>
                </div>
                
                <div id="items-container" class="mt-4">
                    <p class="text-muted"><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_SELECT_CATEGORY_FIRST'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('filter-category');
    const itemsContainer = document.getElementById('items-container');
    
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            
            if (!categoryId) {
                itemsContainer.innerHTML = '<p class="text-muted"><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_SELECT_CATEGORY_FIRST'); ?></p>';
                return;
            }
            
            // Show loading
            itemsContainer.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>';
            
            // Load items for this category
            fetch('index.php?option=com_jlcontentfieldsfilter&task=items.getItems&cid=' + categoryId + '&format=json')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        displayItems(data.data);
                    } else {
                        itemsContainer.innerHTML = '<div class="alert alert-warning"><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_NO_ITEMS_FOUND'); ?></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading items:', error);
                    itemsContainer.innerHTML = '<div class="alert alert-danger">Error loading items</div>';
                });
        });
    }
    
    function displayItems(items) {
        if (!items || items.length === 0) {
            itemsContainer.innerHTML = '<div class="alert alert-info"><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_NO_ITEMS_FOUND'); ?></div>';
            return;
        }
        
        let html = '<table class="table table-striped">';
        html += '<thead><tr>';
        html += '<th><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_HEAD_ID'); ?></th>';
        html += '<th><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_HEAD_META_TITLE'); ?></th>';
        html += '<th><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_HEAD_FILTER'); ?></th>';
        html += '<th><?php echo Text::_('JSTATUS'); ?></th>';
        html += '<th><?php echo Text::_('JACTION'); ?></th>';
        html += '</tr></thead><tbody>';
        
        items.forEach(function(item) {
            const statusBadge = item.state == 1 
                ? '<span class="badge bg-success"><?php echo Text::_('JPUBLISHED'); ?></span>'
                : '<span class="badge bg-danger"><?php echo Text::_('JUNPUBLISHED'); ?></span>';
                
            html += '<tr>';
            html += '<td>' + item.id + '</td>';
            html += '<td>' + (item.meta_title || '') + '</td>';
            html += '<td><small>' + (item.filter || '') + '</small></td>';
            html += '<td>' + statusBadge + '</td>';
            html += '<td>';
            html += '<a href="#" class="btn btn-sm btn-primary" onclick="editItem(' + item.id + '); return false;"><?php echo Text::_('JACTION_EDIT'); ?></a> ';
            html += '<a href="#" class="btn btn-sm btn-danger" onclick="deleteItem(' + item.id + '); return false;"><?php echo Text::_('JACTION_DELETE'); ?></a>';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table>';
        itemsContainer.innerHTML = html;
    }
});

function editItem(itemId) {
    // TODO: Implement edit functionality
    alert('Edit item ' + itemId);
}

function deleteItem(itemId) {
    if (confirm('<?php echo Text::_('COM_JLCONTENTFIELDSFILTER_CONFIRM_DELETE'); ?>')) {
        // TODO: Implement delete functionality
        alert('Delete item ' + itemId);
    }
}
</script>
