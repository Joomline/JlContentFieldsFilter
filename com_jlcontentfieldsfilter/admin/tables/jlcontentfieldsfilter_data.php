<?php

// No direct access
defined( '_JEXEC' ) or die;

/**
 * Object Class Table
 * @author Joomline
 */
class TableJlcontentfieldsfilter_Data extends JTable
{

	/**
	 * Class constructor
	 * @param Object $db (database link object)
	 */
	function __construct( &$db )
	{
		parent::__construct( '#__jlcontentfieldsfilter_data', 'id', $db );
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
			$user = JFactory::getUser();
			$array['created_by'] = $user->id;
		}
		if ( empty( $array['created'] ) ) {
			$array['created'] = date( 'Y-m-d H:i:s' );
		}
		if ( isset( $array['rules'] ) && is_array( $array['rules'] ) ) {
			$rules = new JAccessRules( $array['rules'] );
			$this->setRules( $rules );
		}
		$array['alias'] = JApplication::stringURLSafe( $array['alias'] );
		if ( trim( str_replace( '-', '', $array['alias'] ) ) == '' ) {
			$array['alias'] = JApplication::stringURLSafe( $array['title'] );
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
			$registry = new JRegistry;
			$registry->loadArray( $array['params'] );
			$array['params'] = (string) $registry;
		}

		return parent::bind( $array, $ignore );
	}

}