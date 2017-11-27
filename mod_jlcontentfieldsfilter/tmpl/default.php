<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_finder
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JFactory::getDocument()->addScript(JUri::root().'modules/mod_jlcontentfieldsfilter/assets/javascript/jlcontentfilter.js');
?>

<form id="mod-finder-searchform" action="<?php echo $action; ?>" method="<?php echo $form_method; ?>" class="form-search">
	<div class="jlcontentfieldsfilter<?php echo $moduleclass_sfx; ?> row-fluid">
        <?php foreach($fields as $v) : ?>
            <?php echo $v; ?>
            <div style="clear: both;"></div>
        <?php endforeach; ?>
        <input type="submit" title="Submit" class="btn btn-primary btn-block"/>
        <button class="btn btn-info btn-block" onclick="clearJlContentFieldsFilterForm();">
            <?php echo JText::_('MOD_JLCONTENTFIELDSFILTER_RESET'); ?>
        </button>
	</div>
    <input type="hidden" name="jlcontentfieldsfilter[is_minicck_filter]" value="1"/>
</form>
