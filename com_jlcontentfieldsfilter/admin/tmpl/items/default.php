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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$user = Factory::getApplication()->getIdentity();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
$listDirn = $this->escape($this->state->get('list.direction', 'DESC'));
?>

<form action="<?php echo Route::_('index.php?option=com_jlcontentfieldsfilter&view=items'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
                
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table table-striped" id="itemsList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_JLCONTENTFIELDSFILTER_ITEMS_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.meta_title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_JLCONTENTFIELDSFILTER_HEADING_EXTENSION', 'category_extension', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-15 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) :
                                $canEdit    = $user->authorise('core.edit', 'com_jlcontentfieldsfilter');
                                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                $canEditOwn = $user->authorise('core.edit.own', 'com_jlcontentfieldsfilter') && $item->created_by == $userId;
                                $canChange  = $user->authorise('core.edit.state', 'com_jlcontentfieldsfilter') && $canCheckin;
                            ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->meta_title); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'items.', $canChange, 'cb'); ?>
                                    </td>
                                    <th scope="row" class="has-context">
                                        <div class="mb-1">
                                            <?php if ($canEdit || $canEditOwn) : ?>
                                                <a href="<?php echo Route::_('index.php?option=com_jlcontentfieldsfilter&task=item.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->meta_title); ?>">
                                                    <?php echo $this->escape($item->meta_title); ?>
                                                </a>
                                            <?php else : ?>
                                                <?php echo $this->escape($item->meta_title); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="small break-word">
                                            <?php echo Text::_('COM_JLCONTENTFIELDSFILTER_FILTER_VALUES'); ?>: <?php echo $this->escape($item->filter); ?>
                                        </div>
                                    </th>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo $this->escape($item->category_extension); ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo $this->escape($item->category_title); ?>
                                    </td>
                                    <td class="d-none d-md-table-cell text-center">
                                        <?php echo (int) $item->id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php echo $this->pagination->getListFooter(); ?>
                <?php endif; ?>
                
                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
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
            itemsContainer.innerHTML = '<div class="alert alert-info"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>';
            return;
        }
        
        let html = '<table class="table table-striped">';
        html += '<thead><tr>';
        html += '<th><?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>';
        html += '<th><?php echo Text::_('JGLOBAL_TITLE'); ?></th>';
        html += '<th><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_HEAD_FILTER'); ?></th>';
        html += '<th><?php echo Text::_('JSTATUS'); ?></th>';
        html += '<th><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_ACTIONS'); ?></th>';
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
    window.location.href = 'index.php?option=com_jlcontentfieldsfilter&view=item&layout=edit&id=' + itemId;
}

function deleteItem(itemId) {
    if (confirm('<?php echo Text::_('COM_JLCONTENTFIELDSFILTER_CONFIRM_DELETE'); ?>')) {
        // Send delete request
        fetch('index.php?option=com_jlcontentfieldsfilter&task=items.delete&id=' + itemId + '&<?php echo \Joomla\CMS\Session\Session::getFormToken(); ?>=1', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                // Reload the current category items
                const categorySelect = document.getElementById('filter-category');
                if (categorySelect && categorySelect.value) {
                    categorySelect.dispatchEvent(new Event('change'));
                }
            } else {
                alert('Error deleting item: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error deleting item:', error);
            alert('Error deleting item');
        });
    }
}
</script>
