<?php

/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

// No direct access
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

\defined( '_JEXEC' ) or die;

/**
 * View to display a list of items
 * @author Joomline
 */
class JlcontentfieldsfilterViewItems extends JViewLegacy
{
	/**
	 * @var $items stdClass[]
	 */
	public $items;
	/**
	 * @var $pagination JPagination
	 */
	public $pagination;
	/**
	 * @var $state JObject
	 */
	public $state;
	/**
	 * @var $user JUser
	 */
	public $user;
	/**
	 * @var $authors stdClass[]
	 */
	public $authors;
	public $categoryOptions;

	/**
	 * Method to display the current pattern
	 * @param string $tpl
	 */
	public function display( $tpl = null )
	{
        $this->categoryOptions = $this->get( 'CategoryOptions' );
		$this->pagination = $this->get( 'Pagination' );
		$this->state = $this->get( 'State' );
		$this->user = Factory::getApplication()->getIdentity();
		$this->loadHelper( 'jlcontentfieldsfilter' );
		$this->addToolbar();
		$this->sidebar = Sidebar::render();

		parent::display( $tpl );
	}

	/**
	 * Method to display the toolbar
	 */
	protected function addToolbar()
	{
		ToolBarHelper::title( Text::_( 'COM_JLCONTENTFIELDSFILTER' ) );
		$canDo = jlcontentfieldsfilterHelper::getActions( 'item' );

		if ( $canDo->get( 'core.admin' ) ) {
			ToolBarHelper::preferences( 'com_jlcontentfieldsfilter' );
			ToolBarHelper::divider();
		}
	}

	protected function getSortFields()
	{
		return [
			'ordering' => Text::_( 'JGRID_HEADING_ORDERING' ),
			'published' => Text::_( 'JSTATUS' ),
			'title' => Text::_( 'JGLOBAL_TITLE' ),
			'created_by' => Text::_( 'JAUTHOR' ),
			'created' => Text::_( 'JDATE' ),
			'id' => Text::_( 'JGRID_HEADING_ID' )
		];
	}
}