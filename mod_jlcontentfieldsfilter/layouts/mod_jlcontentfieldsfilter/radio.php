<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright	(C) 2017 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

defined('_JEXEC') or die;

if (!key_exists('field', $displayData))
{
    return;
}

$moduleId = $displayData['moduleId'];
$field = $displayData['field'];
$label = JText::_($field->label);
$value = is_array($field->value) ? $field->value : array();
$options = (array)$field->fieldparams->get('options', array());
$moduleParams = $displayData['params'];
$count_cols = (int)$moduleParams->get('count_cols', 2);
$width = (int)(100/$count_cols);

if(!is_array($options) || !count($options)){
    return;
}

?>

<div class="jlmf-label"><?php echo $label; ?></div>
<div class="jlmf-list-<?php echo $count_cols; ?>">

    <?php
    $i = 1;
    $groups = array_chunk($options, ceil(count($options) / $count_cols));
    foreach($groups as $options) {
        echo '<div>';
        foreach($options as $k => $v) {
            $checked = in_array($v->value, $value) ? ' checked="checked"' : '';
    ?>
    <div>
        <input
            type="radio"
            value="<?php echo $v->value; ?>"
            id="<?php echo $field->name.'-'. $i.'-'.$moduleId; ?>"
            name="jlcontentfieldsfilter[<?php echo $field->id; ?>][]"<?php echo $checked; ?>
            class="jlmf-radio"
        />
        <label class="jlmf-sublabel" for="<?php echo $field->name.'-'. $i.'-'.$moduleId; ?>"><?php echo $v->name; ?></label>
    </div>
    <?php
            $i++;
        }
        echo '</div>';
    }
    ?>
</div>

<button type="button" class="jlmf-link" onclick="JlContentFieldsFilter.clearRadio(this);"><?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_RADIO_RESET')?></button>
