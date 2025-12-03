<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  System.jlcontentfieldsfilter
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\Jlcontentfieldsfilter\Fields;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use \Joomla\CMS\Form\FormField;
use Joomla\Database\DatabaseInterface;


class JlcontentfieldslayoutsField extends FormField
{
    protected $type = 'Jlcontentfieldslayouts';

    protected $layouts_path = '/modules/mod_jlcontentfieldsfilter/layouts/mod_jlcontentfieldsfilter';

    protected $layouts_overrided_path = '/mod_jlcontentfieldsfilter';

    protected function getFrontTemplate()
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $query
            ->select('template')
            ->from('#__template_styles')
            ->where('client_id = 0')
            ->where('home = 1');

        $db->setQuery($query);
        return $db->loadResult();
    }

    protected function getInput()
    {
        $client = ApplicationHelper::getClientInfo(0);

        $layouts_path = Path::clean($client->path . $this->layouts_path);

        $plugin_layouts = [];

        $groups = [];

        if (is_dir($layouts_path) && ($plugin_layouts = Folder::files($layouts_path, '^[^_]*\.php$'))) {
            $groups['_'] = [];
            $groups['_']['id'] = $this->id . '__';
            $groups['_']['text'] = Text::sprintf('JOPTION_FROM_MODULE');
            $groups['_']['items'] = [ HTMLHelper::_('select.option', '', Text::_('JNO')) ];

            foreach ($plugin_layouts as $file) {
                $value = basename($file, '.php');
                $groups['_']['items'][] = HTMLHelper::_('select.option', '_:' . $value, $value);
            }
        }


        $template = $this->getFrontTemplate();

        $template_style_id = '';
        if ($this->form instanceof Form && !empty($template_style_id = $this->form->getValue('template_style_id'))) {
            $template_style_id = preg_replace('#\W#', '', $template_style_id);
        }

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $query
            ->select('element, name')
            ->from('#__extensions as e')
            ->where('e.client_id = 0')
            ->where('e.type = ' . $db->quote('template'))
            ->where('e.enabled = 1');

        if ($template) {
            $query->where('e.element = ' . $db->quote($template));
        }

        if ($template_style_id) {
            $query
                ->join('LEFT', '#__template_styles as s on s.template=e.element')
                ->where('s.id=' . (int)$template_style_id);
        }

        $db->setQuery($query);
        $templates = $db->loadObjectList('element');
        $lang = Factory::getApplication()->getLanguage();
        if ($templates) {
            foreach ($templates as $template) {
                $lang->load('tpl_' . $template->element . '.sys', $client->path, null, false, true)
                || $lang->load('tpl_' . $template->element . '.sys', $client->path . '/templates/' . $template->element, null, false, true);

                $template_path = Path::clean($client->path . '/templates/' . $template->element . '/html/layouts' . $this->layouts_overrided_path);

                if (is_dir($template_path) && ($files = Folder::files($template_path, '^[^_]*\.php$'))) {

                    foreach ($files as $i => $file) {
                        if (in_array($file, $plugin_layouts)) {
                            unset($files[$i]);
                        }
                    }

                    if (count($files)) {
                        $groups[$template->element] = [];
                        $groups[$template->element]['id'] = $this->id . '_' . $template->element;
                        $groups[$template->element]['text'] = Text::sprintf('JOPTION_FROM_TEMPLATE', $template->name);
                        $groups[$template->element]['items'] = [];

                        foreach ($files as $file) {
                            $value = basename($file, '.php');
                            $groups[$template->element]['items'][] = HTMLHelper::_('select.option', $template->element . ':' . $value, $value);
                        }
                    }
                }
            }
        }
        $attr = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';
        $attr .= $this->element['class'] ? ' class="' . (string)$this->element['class'] . '"' : '';

        $html = [];

        $selected = [$this->value];

        $html[] = HTMLHelper::_('select.groupedlist', $groups, $this->name, ['id' => $this->id, 'group.id' => 'id', 'list.attr' => $attr, 'list.select' => $selected]);

        return implode($html);

    }
}
