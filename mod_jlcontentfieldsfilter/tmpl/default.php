<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('jquery.framework');
$doc = JFactory::getDocument();
$doc->addScript(JUri::root().'modules/mod_jlcontentfieldsfilter/assets/javascript/jlcontentfilter.js', array('version' => 'auto'));
$doc->addScriptDeclaration('
	JlContentFieldsFilter.init({
		"autho_send" : '.$autho_send.',
		"form_identifier" : "#mod-finder-searchform",
		"ajax" : '.$ajax.',
		"ajax_selector" : "'.$ajax_selector.'"
	});
');
?>

<form id="mod-finder-searchform" action="<?php echo $action; ?>" method="<?php echo $form_method; ?>" class="form-search">
	<div class="jlcontentfieldsfilter<?php echo $moduleclass_sfx; ?> row-fluid">
        <?php foreach($fields as $v) : ?>
            <?php echo $v; ?>
            <div style="clear: both;"></div>
        <?php endforeach; ?>
        <?php if(!$autho_send) : ?>
            <input type="submit"
                   title="<?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_SUBMIT'); ?>"
                   value="<?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_SUBMIT'); ?>"
                   class="btn btn-primary btn-block"/>
        <?php endif; ?>
        <button class="btn btn-info btn-block" onclick="return JlContentFieldsFilter.clearForm(this);">
            <?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_RESET'); ?>
        </button>
	</div>
</form>
