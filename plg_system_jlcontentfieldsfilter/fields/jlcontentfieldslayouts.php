<?php
defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.filesystem.folder');

class JFormFieldJlContentFieldsLayouts extends JFormField
{
    protected $type = 'jlcontentfieldslayouts';

    protected $layouts_path = '/modules/mod_jlcontentfieldsfilter/layouts/mod_jlcontentfieldsfilter';

    protected $layouts_overrided_path = '/mod_jlcontentfieldsfilter';

    protected function getFrontTemplate()
    {
        $db = JFactory::getDbo();
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
        $client = JApplicationHelper::getClientInfo(0);
        $client_admin = JApplicationHelper::getClientInfo(1);

        $plugin = 'jlcontentfieldsfilter';
        $folder = 'system';


        $lang = JFactory::getLanguage();
        $lang->load($plugin . '.sys', $client_admin->path, null, false, true)
        || $lang->load($plugin . '.sys', $client_admin->path . '/plugins/system/jlcontentfieldsfilter', null, false, true);

        $layouts_path = JPath::clean($client->path . $this->layouts_path);

        $plugin_layouts = [];

        $groups = [];

        if (is_dir($layouts_path) && ($plugin_layouts = JFolder::files($layouts_path, '^[^_]*\.php$'))) {
            $groups['_'] = [];
            $groups['_']['id'] = $this->id . '__';
            $groups['_']['text'] = JText::sprintf('JOPTION_FROM_MODULE');
            $groups['_']['items'] = [ JHtml::_('select.option', '', Text::_('JNO')) ];

            foreach ($plugin_layouts as $file) {
                $value = basename($file, '.php');
                $groups['_']['items'][] = JHtml::_('select.option', '_:' . $value, $value);
            }
        }


        $template = $this->getFrontTemplate();

        $template_style_id = '';
        if ($this->form instanceof JForm) {
            $template_style_id = $this->form->getValue('template_style_id');
            $template_style_id = preg_replace('#\W#', '', $template_style_id);
        }

        $db = JFactory::getDbo();
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

        if ($templates) {
            foreach ($templates as $template) {
                $lang->load('tpl_' . $template->element . '.sys', $client->path, null, false, true)
                || $lang->load('tpl_' . $template->element . '.sys', $client->path . '/templates/' . $template->element, null, false, true);

                $template_path = JPath::clean($client->path . '/templates/' . $template->element . '/html/layouts' . $this->layouts_overrided_path);

                if (is_dir($template_path) && ($files = JFolder::files($template_path, '^[^_]*\.php$'))) {

                    foreach ($files as $i => $file) {
                        if (in_array($file, $plugin_layouts)) {
                            unset($files[$i]);
                        }
                    }

                    if (count($files)) {
                        $groups[$template->element] = [];
                        $groups[$template->element]['id'] = $this->id . '_' . $template->element;
                        $groups[$template->element]['text'] = JText::sprintf('JOPTION_FROM_TEMPLATE', $template->name);
                        $groups[$template->element]['items'] = [];

                        foreach ($files as $file) {
                            $value = basename($file, '.php');
                            $groups[$template->element]['items'][] = JHtml::_('select.option', $template->element . ':' . $value, $value);
                        }
                    }
                }
            }
        }
        $attr = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';
        $attr .= $this->element['class'] ? ' class="' . (string)$this->element['class'] . '"' : '';

        $html = [];

        $selected = [$this->value];

        $html[] = JHtml::_('select.groupedlist', $groups, $this->name, ['id' => $this->id, 'group.id' => 'id', 'list.attr' => $attr, 'list.select' => $selected]);

        return implode($html);

    }
}
