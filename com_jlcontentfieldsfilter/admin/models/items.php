<?php

// No direct access
defined( '_JEXEC' ) or die;

/**
 * @author Joomline
 */
class JlcontentfieldsfilterModelItems extends JModelList
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
        $categoryOptions = JHtml::_(
            'select.options',
            JHtml::_('category.options', 'com_content'),
            'value',
            'text',
            0
        );
        return $categoryOptions;
    }
}