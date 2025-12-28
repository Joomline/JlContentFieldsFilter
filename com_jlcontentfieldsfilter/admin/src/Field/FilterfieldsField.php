<?php

/**
 * JL Content Fields Filter.
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2025 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\Module\Jlcontentfieldsfilter\Site\Helper\JlcontentfieldsfilterHelper;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Form Field class for displaying filter fields in admin.
 *
 * This field dynamically loads and renders filter fields based on the selected
 * category and configured options. It integrates with the module helper to
 * display the actual filter interface in the admin component.
 *
 * @since  1.0.0
 */
class FilterfieldsField extends ListField
{
    /**
     * The form field type.
     *
     * @var string
     * @since   1.0.0
     */
    protected $type = 'Filterfields';

    /**
     * Method to get the field input markup.
     *
     * Renders the complete filter interface by loading the module helper
     * and displaying all configured filter fields for the selected category.
     *
     * @return string The field HTML markup
     *
     * @since   1.0.0
     */
    protected function getInput()
    {
        $app = Factory::getApplication();
        $doc = $app->getDocument();

        // Get form data
        $formData = $this->form->getData();
        $catid    = $formData->get('catid', 0);

        if (!$catid) {
            return '<div class="alert alert-info">' . Text::_('COM_JLCONTENTFIELDSFILTER_SELECT_CATEGORY_FIRST') . '</div>';
        }

        // Load module assets
        HTMLHelper::_('jquery.framework');
        $doc->getWebAssetManager()
            ->useStyle('mod_jlcontentfieldsfilter.jlcontentfilter')
            ->useScript('mod_jlcontentfieldsfilter.jlcontentfilter');

        // Get filter data from form
        $filterData = [];
        if (isset($formData->jlcontentfieldsfilter) && \is_array($formData->jlcontentfieldsfilter)) {
            $filterData = $formData->jlcontentfieldsfilter;
        }

        // Get component option
        $option = 'com_content';

        // Create module params object
        $params = new Registry();
        $params->set('fields', []);
        $params->set('ordering', 'ordering');
        $params->set('show_title', 1);

        // Get available fields for this category
        $context = 'com_content.article';
        $item    = new \stdClass();
        $item->language = $app->getLanguage()->getTag();
        $item->catid    = $catid;

        $fields = FieldsHelper::getFields($context, $item);

        if (!\count($fields)) {
            return '<div class="alert alert-warning">' . Text::_('COM_JLCONTENTFIELDSFILTER_NO_FIELDS_FOUND') . '</div>';
        }

        // Build params for each field
        $fieldsConfig = [];
        foreach ($fields as $field) {
            $fieldsConfig[$field->id] = [
                'filter'  => $this->getFilterTypeForField($field),
                'layout'  => $this->getLayoutForField($field),
                'title'   => $field->title,
                'show'    => 1,
            ];
        }
        $params->set('fields', $fieldsConfig);

        // Load helper and render fields
        $helper     = new JlcontentfieldsfilterHelper();
        $moduleId   = 0; // Admin context
        $filterFields = $helper->getFields($params, $catid, $filterData, $moduleId, $option);

        // Render output
        $html = '<div class="jlcontentfieldsfilter-admin-preview">';
        $html .= '<div class="alert alert-info">' . Text::_('COM_JLCONTENTFIELDSFILTER_PREVIEW_FILTERS') . '</div>';

        if (\count($filterFields)) {
            $html .= '<div class="jlmf-wrapper">';
            foreach ($filterFields as $field) {
                $html .= '<div class="jlmf-field">';
                $html .= '<label class="jlmf-field-title">' . htmlspecialchars($field->title, ENT_QUOTES, 'UTF-8') . '</label>';
                
                // Render field based on type
                switch ($field->type) {
                    case 'list':
                    case 'radio':
                    case 'checkboxes':
                        $html .= $this->renderOptionsField($field, $filterData);
                        break;
                    case 'text':
                        $html .= $this->renderTextField($field, $filterData);
                        break;
                    case 'range':
                        $html .= $this->renderRangeField($field, $filterData);
                        break;
                    default:
                        $html .= '<div class="text-muted">' . Text::_('COM_JLCONTENTFIELDSFILTER_UNSUPPORTED_FIELD_TYPE') . '</div>';
                }
                
                $html .= '</div>';
            }
            $html .= '</div>';
        } else {
            $html .= '<div class="alert alert-warning">' . Text::_('COM_JLCONTENTFIELDSFILTER_NO_FILTERABLE_FIELDS') . '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Get filter type for a field based on its configuration.
     *
     * @param object $field The field object
     *
     * @return string The filter type (list, radio, checkboxes, text, range)
     *
     * @since   1.0.0
     */
    protected function getFilterTypeForField($field)
    {
        $fieldType = $field->type;

        switch ($fieldType) {
            case 'list':
            case 'radio':
                return 'list';
            case 'checkboxes':
                return 'checkboxes';
            case 'text':
            case 'textarea':
            case 'editor':
                return 'text';
            case 'integer':
                return 'range';
            default:
                return 'list';
        }
    }

    /**
     * Get layout name for a field.
     *
     * @param object $field The field object
     *
     * @return string The layout name
     *
     * @since   1.0.0
     */
    protected function getLayoutForField($field)
    {
        return $this->getFilterTypeForField($field);
    }

    /**
     * Render an options-based field (list, radio, checkboxes).
     *
     * @param object $field Field object
     * @param array $filterData Current filter data
     *
     * @return string HTML markup
     *
     * @since   1.0.0
     */
    protected function renderOptionsField($field, $filterData)
    {
        $html = '<select class="form-select" disabled>';
        $html .= '<option value="">' . Text::_('JSELECT') . '</option>';
        
        if (isset($field->fieldparams['options']) && \is_array($field->fieldparams['options'])) {
            foreach ($field->fieldparams['options'] as $option) {
                $value = $option['value'] ?? '';
                $text  = $option['name'] ?? $value;
                $html .= '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '">';
                $html .= htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                $html .= '</option>';
            }
        }
        
        $html .= '</select>';
        $html .= '<div class="form-text">' . Text::_('COM_JLCONTENTFIELDSFILTER_FRONTEND_INTERACTIVE') . '</div>';

        return $html;
    }

    /**
     * Render a text field.
     *
     * @param object $field Field object
     * @param array $filterData Current filter data
     *
     * @return string HTML markup
     *
     * @since   1.0.0
     */
    protected function renderTextField($field, $filterData)
    {
        $html  = '<input type="text" class="form-control" disabled ';
        $html .= 'placeholder="' . htmlspecialchars($field->title, ENT_QUOTES, 'UTF-8') . '">';
        $html .= '<div class="form-text">' . Text::_('COM_JLCONTENTFIELDSFILTER_FRONTEND_INTERACTIVE') . '</div>';

        return $html;
    }

    /**
     * Render a range field.
     *
     * @param object $field Field object
     * @param array $filterData Current filter data
     *
     * @return string HTML markup
     *
     * @since   1.0.0
     */
    protected function renderRangeField($field, $filterData)
    {
        $html  = '<div class="row g-2">';
        $html .= '<div class="col-6">';
        $html .= '<input type="number" class="form-control" disabled placeholder="' . Text::_('COM_JLCONTENTFIELDSFILTER_FROM') . '">';
        $html .= '</div>';
        $html .= '<div class="col-6">';
        $html .= '<input type="number" class="form-control" disabled placeholder="' . Text::_('COM_JLCONTENTFIELDSFILTER_TO') . '">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="form-text">' . Text::_('COM_JLCONTENTFIELDSFILTER_FRONTEND_INTERACTIVE') . '</div>';

        return $html;
    }
}
