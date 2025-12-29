<?php
/**
 * JL Content Fields Filter
 *
 * @version    @version@
 * @author     Joomline
 * @copyright  (C) 2017-2025 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useScript('webcomponent.field-fancy-select');

?>

<form action="<?php echo Route::_('index.php?option=com_jlcontentfieldsfilter&view=item&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
    
    <!-- Title Section -->
    <div class="row title-alias form-vertical mb-3">
        <div class="col-12 col-md-6">
            <?php echo $this->form->renderField('meta_title'); ?>
        </div>
    </div>

    <!-- Main Card with Tabs -->
    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general', 'recall' => true, 'breakpoint' => 768]); ?>
        
        <!-- General Tab -->
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_JLCONTENTFIELDSFILTER_FIELDSET_GENERAL')); ?>
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-9">
                <?php echo $this->form->renderField('meta_desc'); ?>
                
                <?php echo $this->form->renderField('meta_keywords'); ?>
                
                <?php echo $this->form->renderField('filter'); ?>
            </div>
            
            <!-- Right Column -->
            <div class="col-lg-3">
                <fieldset class="form-vertical">
                    <legend class="visually-hidden"><?php echo Text::_('JGLOBAL_FIELDSET_GLOBAL'); ?></legend>
                    
                    <?php echo $this->form->renderField('state'); ?>
                    
                    <div class="mb-3">
                        <label class="form-label" for="jform_extension">
                            <?php echo Text::_('COM_JLCONTENTFIELDSFILTER_HEADING_EXTENSION'); ?>
                        </label>
                        <?php
                        $extension = '';
                        if (!empty($this->item->catid)) {
                            $db = Factory::getContainer()->get('DatabaseDriver');
                            $query = $db->getQuery(true)
                                ->select($db->quoteName('extension'))
                                ->from($db->quoteName('#__categories'))
                                ->where($db->quoteName('id') . ' = ' . (int)$this->item->catid);
                            $db->setQuery($query);
                            $extension = $db->loadResult();
                        }
                        ?>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($extension, ENT_QUOTES, 'UTF-8'); ?>" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required" for="jform_catid">
                            <?php echo Text::_('JCATEGORY'); ?>
                        </label>
                        <?php
                        $catName = '';
                        if (!empty($this->item->catid)) {
                            $categories = Categories::getInstance('Content');
                            $category = $categories->get($this->item->catid);
                            $catName = $category ? $category->title : '';
                        }
                        ?>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($catName, ENT_QUOTES, 'UTF-8'); ?>" disabled>
                        <input type="hidden" name="jform[catid]" value="<?php echo (int)($this->item->catid ?? 0); ?>">
                        <small class="form-text text-muted"><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_FIELD_CATEGORY_DESC'); ?></small>
                    </div>
                    
                    <?php echo $this->form->renderField('id'); ?>
                </fieldset>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>
        
        <!-- Filter Details Tab -->
        <?php if (!empty($this->item->filter)) : ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'filterdetails', Text::_('COM_JLCONTENTFIELDSFILTER_FIELDSET_FILTERDETAILS')); ?>
        <div class="row">
            <div class="col-12">
                <fieldset class="options-form">
                    <legend><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_FILTER_VALUES'); ?></legend>
                    <div class="row">
                        <?php
                        // Use FieldsHelper to get all article fields and create an ID->name mapping
                        $fields = FieldsHelper::getFields('com_content.article');
                        $fieldsMeta = array_column($fields, 'title', 'id');
                        $filterPairs = explode('&', $this->item->filter);
                        $rangeValues = [];
                        $fieldGroups = [];
                        
                        // First pass: group values by field ID
                        foreach ($filterPairs as $pair) {
                            if (empty($pair)) continue;
                            
                            if (preg_match('/^([^=]+)\[([^\]]+)\]=(.*)$/', $pair, $matches)) {
                                // Range field: fieldid[from]=val or fieldid[to]=val
                                $fieldIdRaw = urldecode($matches[1]);
                                $subKey = urldecode($matches[2]);
                                $value = urldecode($matches[3]);
                                
                                if (!isset($fieldGroups[$fieldIdRaw])) {
                                    $fieldGroups[$fieldIdRaw] = ['type' => 'range', 'values' => ['from' => '', 'to' => '']];
                                }
                                $fieldGroups[$fieldIdRaw]['values'][$subKey] = $value;
                            } else {
                                // Regular field=value1,value2
                                list($fieldIdRaw, $valueStr) = explode('=', $pair, 2);
                                $values = explode(',', urldecode($valueStr));
                                
                                if (!isset($fieldGroups[$fieldIdRaw])) {
                                    $fieldGroups[$fieldIdRaw] = ['type' => 'normal', 'values' => []];
                                }
                                // Aggiungi valori (evita duplicati)
                                foreach ($values as $val) {
                                    if (!in_array($val, $fieldGroups[$fieldIdRaw]['values'])) {
                                        $fieldGroups[$fieldIdRaw]['values'][] = $val;
                                    }
                                }
                            }
                        }

                        // Second pass: render fields as cards
                        $colCount = 0;
                        foreach ($fieldGroups as $fieldIdRaw => $fieldData) {
                            $fieldId = htmlspecialchars($fieldIdRaw, ENT_QUOTES, 'UTF-8');
                            $fieldName = isset($fieldsMeta[$fieldIdRaw]) ? $fieldsMeta[$fieldIdRaw] : $fieldId;
                            $colCount++;
                            ?>
                            <div class="col-lg-6 mb-3">
                                <div class="card border">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary">ID: <?php echo $fieldId; ?></span>
                                            <strong><?php echo htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?></strong>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($fieldData['type'] === 'range') : ?>
                                            <!-- Range Field (From/To) -->
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label class="form-label small text-muted"><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_FROM'); ?></label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($fieldData['values']['from'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small text-muted"><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_TO'); ?></label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($fieldData['values']['to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                                </div>
                                            </div>
                                        <?php else : ?>
                                            <!-- Normal Field (Single or Multiple Values) -->
                                            <?php if (count($fieldData['values']) === 1) : ?>
                                                <!-- Single Value -->
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($fieldData['values'][0], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                            <?php else : ?>
                                                <!-- Multiple Values -->
                                                <label class="form-label small text-muted"><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_SELECTED_VALUES'); ?>:</label>
                                                <ul class="list-group list-group-flush">
                                                    <?php foreach ($fieldData['values'] as $val) : ?>
                                                        <li class="list-group-item px-0 py-1">
                                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </fieldset>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>
        
        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>
    
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
