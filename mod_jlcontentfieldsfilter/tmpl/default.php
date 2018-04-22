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
JHtml::_('jquery.framework');
$doc = JFactory::getDocument();
$doc->addScript(JUri::root().'modules/mod_jlcontentfieldsfilter/assets/javascript/jlcontentfilter.js', array('version' => 'auto'));
$doc->addScriptDeclaration('
	JlContentFieldsFilter.init({
		"autho_send" : '.$autho_send.',
		"form_identifier" : "mod-finder-searchform-'.$module->id.'",
		"ajax" : '.$ajax.',
		"ajax_selector" : "'.$ajax_selector.'",
		"ajax_loader" : "'.$ajax_loader.'",
		"ajax_loader_width" : '.$ajax_loader_width.'
	});
');
?>

<form id="mod-finder-searchform-<?php echo $module->id; ?>" action="<?php echo $action; ?>" method="<?php echo $form_method; ?>" class="form-search">
	<div class="jlcontentfieldsfilter<?php echo $moduleclass_sfx; ?> row-fluid">
        <?php foreach($fields as $v) : ?>
            <?php echo $v; ?>
            <div style="clear: both;"></div>
        <?php endforeach; ?>
        <?php if($enableOrdering) : ?>
            <?php echo $orderingSelect; ?>
        <?php endif; ?>
		<div class="btn-group">
			<?php if(!$autho_send) : ?>
				<input type="submit"
					   title="<?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_SUBMIT'); ?>"
					   value="<?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_SUBMIT'); ?>"
					   class="btn btn-primary"/>
			<?php endif; ?>
		
			<button class="btn btn-default" onclick="return JlContentFieldsFilter.clearForm(this);">
				<?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_RESET'); ?>
			</button>
		</div>	
	</div>
    <input type="hidden" name="jlcontentfieldsfilter[is_filter]" value="1">
</form>
