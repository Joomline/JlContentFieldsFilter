<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;



/**
 * Provides input for TOS
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       2.5.5
 */
class JFormFieldJlContentFieldsFilterFields extends \Joomla\CMS\Form\FormField
{
	protected $type = 'jlcontentfieldsfilterfields';

	public function getInput()
	{
		$dataType = (string)@$this->element['dataType'];
		$fieldId = (string)@$this->element['fieldId'];

		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('JNO'));

		if(!empty($fieldId)){
			switch ($dataType){
				case 'list':
				case 'radio':
				case 'checkboxes':
				case 'multiselect':
					$options[] = JHtml::_('select.option', 'radio', JText::_('PLG_JLCONTENTFIELDSFILTER_FILTER_RADIO'));
					$options[] = JHtml::_('select.option', 'list', JText::_('PLG_JLCONTENTFIELDSFILTER_FILTER_LIST'));
					$options[] = JHtml::_('select.option', 'checkboxes', JText::_('PLG_JLCONTENTFIELDSFILTER_FILTER_CHECKBOXES'));
					break;
				case 'text':
					$options[] = JHtml::_('select.option', 'text', JText::_('PLG_JLCONTENTFIELDSFILTER_FILTER_TEXT'));
					break;
				default:
					break;
			}
		}

		return JHtml::_('select.genericlist', $options, $this->name, '', 'value', 'text', $this->value, $this->id);
	}
}
