<?php

/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\View\Items;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Jlcontentfieldsfilter\Administrator\Helper\JlcontentfieldsfilterHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View to display a list of items
 * @author Joomline
 */
class HtmlView extends BaseHtmlView
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
	 * @var $state \Joomla\CMS\Object\CMSObject
	 */
	public $state;
	/**
	 * @var $user \Joomla\CMS\User\User
	 */
	public $user;
	/**
	 * @var $authors stdClass[]
	 */
	public $authors;
	public $categoryOptions;

	/**
	 * Method to display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  void
	 *
	 * @since   1.0.0
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
	 * Method to add toolbar buttons
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addToolbar()
	{
		ToolBarHelper::title( Text::_( 'COM_JLCONTENTFIELDSFILTER' ) );
		$canDo = JlcontentfieldsfilterHelper::getActions( 'item' );

		if ( $canDo->{'core.admin'} ) {
			ToolBarHelper::preferences( 'com_jlcontentfieldsfilter' );
			ToolBarHelper::divider();
		}
	}

	/**
	 * Get the sort fields for the list
	 *
	 * @return  array  Array of sort field options
	 *
	 * @since   1.0.0
	 */
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