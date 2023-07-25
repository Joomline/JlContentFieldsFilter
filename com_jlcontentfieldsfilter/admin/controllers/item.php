<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright	(C) 2017-2019 Arkadiy Sedelnikov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

// No direct access
use Joomla\CMS\Factory;

defined( '_JEXEC' ) or die;

/**
 * Controller for edit current element
 * @author Joomline
 */
class JlcontentfieldsfilterControllerItem extends JControllerForm
{

	/**
	 * Class constructor
	 * @param array $config
	 */
	function __construct( $config = array() )
	{
		$this->view_list = 'items';
		parent::__construct( $config );
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param    array    $data    An array of input data.
	 * @param    string    $key    The name of the key for the primary key.
	 *
	 * @return    boolean
	 * @since    1.6
	 */
	protected function allowEdit( $data = array(), $key = 'id' )
	{
		// Initialise variables.
		$recordId = ( int )isset( $data[$key] ) ? $data[$key] : 0;
		$user = Factory::getApplication()->getIdentity();
		$userId = $user->get( 'id' );
		// Check general edit permission first.
		if ( $user->authorise( 'core.edit', 'com_jlcontentfieldsfilter.item.' . $recordId ) ) {
			return true;
		}
		// Fallback on edit.own.
		// First test if the permission is available.
		if ( $user->authorise( 'core.edit.own', 'com_jlcontentfieldsfilter.item.' . $recordId ) ) {
			// Now test the owner is the user.
			$ownerId = ( int )isset( $data['created_by'] ) ? $data['created_by'] : 0;
			if ( empty( $ownerId ) && $recordId ) {
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem( $recordId );

				if ( empty( $record ) ) {
					return false;
				}

				$ownerId = $record->created_by;
			}
			// If the owner matches 'me' then do the test.
			if ( $ownerId == $userId ) {
				return true;
			}
		}
		// Since there is no asset tracking, revert to the component permissions.
		return false;
	}
}