<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_jlcontentfieldsfilter
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @var $module object. From there you can take the module id ($module->id), the module title, its position, etc.
 * @var $app object - the application object. This means that you do not need to call Joomla\CMS\Factory::getApplication() by yourself. It is already there for your convenience.
 * @var $input object - also, the Input object is now immediately available in the module layout (through it we get GET, POST parameters, SERVER, etc.), which previously had to be called independently.
 * @var $params object - module parameters. We get them as before: $params->get('param_name' , 'default_value_if_value_is_empty'). We collect these parameters using various types of Joomla fields in the module's xml manifest.
 * @var $template object - parameters of the style settings of the current template. Joomla templates have templateDetails.xml in which you can set various template parameters: logos, fonts, custom scripts in <head> and <body> and whatever your heart desires. Now in the module you have the opportunity to access these parameters without unnecessary movements. However, it is worth remembering that many studio templates (JoomShaper Helix and others like them) do not use a standard parameter storage location, so it may be empty there.
 */

if(empty($fields)){
	return '';
}

$doc = $app->getDocument();
$wa  = $doc->getWebAssetManager();
if ($params->get('enable_no_jq', 0))
{
	$wa->useScript('jquery')
		->registerAndUseScript('mod_jlcontentfieldsfilter.jlcontentfilter.script', 'mod_jlcontentfieldsfilter/jlcontentfilter.js', ['version' => 'auto'], ['defer' => true], ['jquery']);
}
else
{
	$wa->registerAndUseScript('mod_jlcontentfieldsfilter.nojq_jlcontentfilter.script', 'mod_jlcontentfieldsfilter/nojq_jlcontentfilter.js', ['version' => 'auto'], ['defer' => true]);
}

$wa->addInlineScript('
	document.addEventListener("DOMContentLoaded", () => {
        JlContentFieldsFilter.init({
                "autho_send" : ' . $autho_send . ',
                "form_identifier" : "mod-finder-searchform-' . $module->id . '",
                "ajax" : ' . $ajax . ',
                "ajax_selector" : "' . $ajax_selector . '",
                "ajax_loader" : "' . $ajax_loader . '",
                "ajax_loader_width" : ' . $ajax_loader_width . '
            });
    });	
');

if ($params->get('enable_css', 1))
{
	$wa->registerAndUseStyle('mod_jlcontentfieldsfilter.jlcontentfilter.style','mod_jlcontentfieldsfilter/jlcontentfilter.css', ['version' => 'auto']);
}

?>

<form id="mod-finder-searchform-<?php echo $module->id; ?>" action="<?php echo $action; ?>"
      method="<?php echo $form_method; ?>" class="form-search">
    <div class="jlcontentfieldsfilter<?php echo $moduleclass_sfx; ?>">

		<?php foreach ($fields as $v) : ?>
			<?php if ($v): ?>
                <div class="jlmf-section">
					<?php echo $v; ?>
                </div>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php if ($enableOrdering) : ?>
            <div class="jlmf-section">
				<?php echo $orderingSelect; ?>
            </div>
		<?php endif; ?>

        <div class="jlmf-section">
			<?php if (!$autho_send) : ?>
                <button type="submit"
                        class="jlmf-button"><?php echo Text::_('MOD_JLCONTENTFIELDSFILTER_SUBMIT'); ?></button>
			<?php endif; ?>
            <div>
                <button type="button" class="jlmf-link"
                        onclick="return JlContentFieldsFilter.clearForm(this);"><?php echo Text::_('MOD_JLCONTENTFIELDSFILTER_RESET'); ?></button>
            </div>
        </div>

    </div>
	<?php if ($option == 'com_tags') : ?>
        <input type="hidden" name="tag_category_id" value="<?php echo $catid; ?>">
	<?php endif; ?>
    <input type="hidden" name="jlcontentfieldsfilter[is_filter]" value="1">
</form>
