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
use Joomla\Component\Jlcontentfieldsfilter\Administrator\Helper\JlcontentfieldsfilterHelper;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>

<form action="<?php echo Route::_('index.php?option=com_jlcontentfieldsfilter&view=item&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
    
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h3><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_ITEM_DETAILS'); ?></h3>
                    
                    <div class="mb-3">
                        <label class="form-label" for="jform_id">
                            <?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?>
                        </label>
                        <input type="text" id="jform_id" class="form-control" value="<?php echo htmlspecialchars($this->item->id ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required" for="jform_catid">
                            <?php echo Text::_('JCATEGORY'); ?>
                        </label>
                        <?php
                        // Get category name from ID
                        $catName = '';
                        if (!empty($this->item->catid)) {
                            $categories = Categories::getInstance('Content');
                            $category = $categories->get($this->item->catid);
                            $catName = $category ? $category->title : '';
                        }
                        ?>
                        <input type="text" name="jform[catid]" id="jform_catid" class="form-control" value="<?php echo htmlspecialchars($catName, ENT_QUOTES, 'UTF-8'); ?>" disabled>
                        <input type="hidden" name="jform[catid]" value="<?php echo (int)($this->item->catid ?? 0); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="jform_meta_title">
                            <?php echo Text::_('JGLOBAL_TITLE'); ?>
                        </label>
                        <input type="text" name="jform[meta_title]" id="jform_meta_title" class="form-control" value="<?php echo htmlspecialchars($this->item->meta_title ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="jform_meta_desc">
                            <?php echo Text::_('JFIELD_META_DESCRIPTION_LABEL'); ?>
                        </label>
                        <textarea name="jform[meta_desc]" id="jform_meta_desc" class="form-control" rows="3"><?php echo htmlspecialchars($this->item->meta_desc ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="jform_meta_keywords">
                            <?php echo Text::_('JFIELD_META_KEYWORDS_LABEL'); ?>
                        </label>
                        <textarea name="jform[meta_keywords]" id="jform_meta_keywords" class="form-control" rows="2"><?php echo htmlspecialchars($this->item->meta_keywords ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="jform_filter">
                            <?php echo Text::_('COM_JLCONTENTFIELDSFILTER_HEAD_FILTER'); ?>
                        </label>
                        <textarea name="jform[filter]" id="jform_filter" class="form-control" rows="2" disabled><?php echo htmlspecialchars($this->item->filter ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <small class="form-text text-muted"><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_FILTER_READONLY'); ?></small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h3><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></h3>
                    
                    <div class="mb-3">
                        <label class="form-label" for="jform_state">
                            <?php echo Text::_('JSTATUS'); ?>
                        </label>
                        <select name="jform[state]" id="jform_state" class="form-select">
                            <option value="1" <?php echo ($this->item->state == 1) ? 'selected' : ''; ?>><?php echo Text::_('JPUBLISHED'); ?></option>
                            <option value="0" <?php echo ($this->item->state == 0) ? 'selected' : ''; ?>><?php echo Text::_('JUNPUBLISHED'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($this->item->filter)) : ?>
            <div class="card mt-3">
                <div class="card-body">
                    <h3><?php echo Text::_('COM_JLCONTENTFIELDSFILTER_FILTER_VALUES'); ?></h3>
                    <?php
                    // Use FieldsHelper to get all article fields and create an ID->name mapping
                    $fields = FieldsHelper::getFields('com_content.article');
                    $fieldsMeta = array_column($fields, 'title', 'id');
                    $filterPairs = explode('&', $this->item->filter);
                    $rangeValues = [];
                    $fieldOrder = [];
                    // First pass: collect range and normal fields in order
                    foreach ($filterPairs as $pair) {
                        if (empty($pair)) continue;
                        if (preg_match('/^([^=]+)\[([^\]]+)\]=(.*)$/', $pair, $matches)) {
                            // Range field: fieldid[from]=val or fieldid[to]=val
                            $fieldIdRaw = urldecode($matches[1]);
                            $subKey = urldecode($matches[2]);
                            $value = urldecode($matches[3]);
                            if (!isset($rangeValues[$fieldIdRaw])) {
                                $rangeValues[$fieldIdRaw] = ['from' => '', 'to' => '', 'order' => count($fieldOrder)];
                                $fieldOrder[] = ['type' => 'range', 'id' => $fieldIdRaw];
                            }
                            $rangeValues[$fieldIdRaw][$subKey] = $value;
                        } else {
                            // Regular field=value1,value2
                            list($fieldIdRaw, $value) = explode('=', $pair, 2);
                            $fieldOrder[] = ['type' => 'normal', 'id' => $fieldIdRaw, 'value' => $value];
                        }
                    }

                    // Second pass: render fields in original order
                    foreach ($fieldOrder as $fieldInfo) {
                        if ($fieldInfo['type'] === 'normal') {
                            $fieldIdRaw = $fieldInfo['id'];
                            $fieldId = htmlspecialchars($fieldIdRaw, ENT_QUOTES, 'UTF-8');
                            $fieldName = isset($fieldsMeta[$fieldIdRaw]) ? $fieldsMeta[$fieldIdRaw] : $fieldId;
                            $values = explode(',', urldecode($fieldInfo['value']));
                            foreach ($values as $val) {
                                $val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                                ?>
                                <div class="mb-3">
                                    <label class="form-label d-flex align-items-center gap-2">
                                        <span class="badge rounded-pill bg-primary" style="font-size:0.9em;">ID: <?php echo $fieldId; ?></span>
                                        <span><?php echo htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </label>
                                    <input type="text" class="form-control" value="<?php echo $val; ?>" disabled>
                                </div>
                                <?php
                            }
                        } elseif ($fieldInfo['type'] === 'range') {
                            $fieldIdRaw = $fieldInfo['id'];
                            $fieldId = htmlspecialchars($fieldIdRaw, ENT_QUOTES, 'UTF-8');
                            $fieldName = isset($fieldsMeta[$fieldIdRaw]) ? $fieldsMeta[$fieldIdRaw] : $fieldId;
                            $from = isset($rangeValues[$fieldIdRaw]['from']) ? htmlspecialchars($rangeValues[$fieldIdRaw]['from'], ENT_QUOTES, 'UTF-8') : '';
                            $to = isset($rangeValues[$fieldIdRaw]['to']) ? htmlspecialchars($rangeValues[$fieldIdRaw]['to'], ENT_QUOTES, 'UTF-8') : '';
                                ?>
                                <div class="mb-3">
                                    <label class="form-label d-flex align-items-center gap-2">
                                        <span class="badge rounded-pill bg-primary" style="font-size:0.9em;">ID: <?php echo $fieldId; ?></span>
                                        <span><?php echo htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </label>
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control" style="max-width: 48%;" value="<?php echo $from; ?>" placeholder="From" disabled>
                                        <input type="text" class="form-control" style="max-width: 48%;" value="<?php echo $to; ?>" placeholder="To" disabled>
                                    </div>
                                </div>
                                <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
