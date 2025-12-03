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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

FormHelper::loadFieldClass('list');

/**
 * Form Field class for jlcontentfieldsfilter fields
 *
 * @since  1.0.0
 */
class JlcontentfieldsfilterfieldsField extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'Jlcontentfieldsfilterfields';

	/**
	 * The current extra field type
	 *
	 * @var    string
	 */
	protected $dataType = null;


	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   \SimpleXMLElement $element   The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed            $value     The form field value to validate.
	 * @param   string           $group     The field name group control value. This acts as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		if ($return = parent::setup($element, $value, $group))
		{
			$this->dataType = (!empty($this->element['dataType'])) ? (string) $this->element['dataType'] : '';
		}


		return $return;
	}


	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 */
	protected function getOptions()
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('JNO'));

		switch ($this->dataType)
		{
			case 'list':
			case 'radio':
			case 'checkboxes':
				$options[] = HTMLHelper::_('select.option', 'radio', Text::_('PLG_JLCONTENTFIELDSFILTER_FILTER_RADIO'));
				$options[] = HTMLHelper::_('select.option', 'list', Text::_('PLG_JLCONTENTFIELDSFILTER_FILTER_LIST'));
				$options[] = HTMLHelper::_('select.option', 'checkboxes', Text::_('PLG_JLCONTENTFIELDSFILTER_FILTER_CHECKBOXES'));
				break;
			case 'text':
				$options[] = HTMLHelper::_('select.option', 'text', Text::_('PLG_JLCONTENTFIELDSFILTER_FILTER_TEXT'));
				$options[] = HTMLHelper::_('select.option', 'range', Text::_('PLG_JLCONTENTFIELDSFILTER_FILTER_RANGE'));
				break;
			default:
				break;
		}

		return $options;
	}
}
