<?php
/**
 * JL Content Fields Filter
 *
 * @version          @version@
 * @author           Joomline
 * @copyright    (C) 2017-2019 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license          GNU General Public License version 2 or later; see    LICENSE.txt
 */

/**
 * $module - объект модуля. Оттуда Вы можете взять id модуля ($module->id), заголовок модуля, его позицию и т.д.
 * $app - объект приложения. Это значит, что Вам не нужно самостоятельно вызывать Joomla\CMS\Factory::getApplication(). Он уже есть для Вашего удобства.
 * $input - также в макете модуля теперь сразу доступен объект Input (через него мы получаем GET, POST параметры, SERVER и т.д.), который раньше приходилось вызывать самостоятельно.
 * $params - параметры модуля. Получаем их как раньше: $params->get('param_name' , 'default_value_if_value_is_empty'). Эти параметры мы собираем с помощью различных типов полей Joomla в xml-манифесте модуля.
 * $template - параметры настроек стиля текущего шаблона. У шаблонов Joomla есть templateDetails.xml, в которых можно задавать различные параметры шаблона: логотипы, шрифты, пользовательские скрипты в <head> и <body> и всё, что душе угодно. Теперь в модуле Вы имеете возможность без лишних шевелений получить доступ к этим параметрам. Однако, стоит помнить, что многие студийные шаблоны (JoomShaper Helix и иже с ними) не используют стандартное место хранение параметров, поэтому там может оказаться пусто.
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
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
	$wa->registerAndUseStyle('mod_jlcontentfieldsfilter.jlcontentfilter.style','mod_jlcontentfieldsfilter/jlcontentfilter.css', array('version' => 'auto'));
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
	<?php if ($option == 'com_tags') { ?>
        <input type="hidden" name="tag_category_id" value="<?php echo $catid; ?>">
	<?php } ?>
    <input type="hidden" name="jlcontentfieldsfilter[is_filter]" value="1">
</form>
