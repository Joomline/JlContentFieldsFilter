<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jlcontentfieldsfilter
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Model;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\ListModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Items model for jlcontentfieldsfilter component
 *
 * @since  1.0.0
 */
class ItemsModel extends ListModel
{
	/**
	 * Составление запроса для получения списка записей
	 * @return JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$query = $this->getDbo()->getQuery( true );
		$query->select( '*' );
		$query->from( '#__jlcontentfieldsfilter_data' );
		return $query;
	}


	public function getCategoryOptions()
    {
        $categoryOptions = HTMLHelper::_(
            'select.options',
            HTMLHelper::_('category.options', 'com_content'),
            'value',
            'text',
			['class'=>'form-select'],
            0
        );
        return $categoryOptions;
    }
}