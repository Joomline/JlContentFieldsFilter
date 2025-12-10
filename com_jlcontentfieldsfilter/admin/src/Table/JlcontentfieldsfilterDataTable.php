<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Table;

use Joomla\CMS\Access\Rules;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined( '_JEXEC' ) or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Table class for jlcontentfieldsfilter data
 *
 * @since  1.0.0
 */
class JlcontentfieldsfilterDataTable extends Table
{

	/**
	 * Class constructor
	 * @param DatabaseInterface $db Database driver
	 */
	public function __construct(DatabaseInterface $db)
	{
		parent::__construct('#__jlcontentfieldsfilter_data', 'id', $db);
	}

	/**
	 * Method for loading data into the object field
	 * @param Array $array (Featured in the field of data)
	 * @param String $ignore
	 * @return Boolean result
	 */
	public function bind( $array, $ignore = '' )
	{
		if ( empty( $array['created_by'] ) ) {
			$user = Factory::getApplication()->getIdentity();
			$array['created_by'] = $user->id;
		}
		if ( empty( $array['created'] ) ) {
			$array['created'] = date( 'Y-m-d H:i:s' );
		}
		if ( isset( $array['rules'] ) && is_array( $array['rules'] ) ) {
			$rules = new Rules( $array['rules'] );
			$this->setRules( $rules );
		}
		$array['alias'] = ApplicationHelper::stringURLSafe( $array['alias'] );
		if ( trim( str_replace( '-', '', $array['alias'] ) ) == '' ) {
			$array['alias'] = ApplicationHelper::stringURLSafe( $array['title'] );
		}

		if ( isset( $array['text'] ) )
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match($pattern, $array['text'] );

			if ($tagPos == 0)
			{
				$this->introtext = $array['text'];
				$this->fulltext = '';
			}
			else
			{
				list ($this->introtext, $this->fulltext) = preg_split( $pattern, $array['text'], 2 );
			}
		}

		if ( isset( $array['params'] ) && is_array( $array['params'] ) )
		{
			$registry = new Registry;
			$registry->loadArray( $array['params'] );
			$array['params'] = (string) $registry;
		}

		return parent::bind( $array, $ignore );
	}

}