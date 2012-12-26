<?php
/**
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class JoomprosubsTableSubscription extends JTable
{
    public $asset_id;
	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joompro_subscriptions', 'id', $db);
	}
        
        public function bind($array, $ignore = '')
        {
            if (isset($array['rules']) && is_array($array['rules']))
            {
                $rules = new JAccessRules($array['rules']);
                $this->setRules($rules);
            }
            return parent::bind($array, $ignore);
        }
        
        protected function _getAssetName()
        {
            $k = $this->_tbl_key;
            return 'com_joomprosubs.subscription.'.(int) $this->$k;
        }
        
        protected function _getAssetTitle()
        {
            return $this->title;
        }
        
        protected function _getAssetParentId($table = null, $id = null)
        {
            $asset = JTable::getInstance('asset');
            $asset->loadByName('com_joomprosubs');
            return $asset->id;
        }

	/**
	 * Overload the store method for the Subscriptions table.
	 *
	 * @param	boolean	Toggle whether null values should be updated.
	 * @return	boolean	True on success, false on failure.
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->id) {
			// Existing item
			$this->modified	= $date->toMySQL();
			$this->modified_by	= $user->get('id');
		} else {
			// New subscription. Created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->created)) {
				$this->created = $date->toMySQL();
			}
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('subscription', 'joomprosubsTable');
		if ($table->load(array('alias'=>$this->alias,'catid'=>$this->catid)) && ($table->id != $this->id || $this->id==0)) {
			$this->setError(JText::_('COM_JOOMPROSUBS_ERROR_UNIQUE_ALIAS'));
			return false;
		}
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}

	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return	boolean	True on success.
	 */
	public function check()
	{
		// check for existing name
		$db = $this->_db;
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__joompro_subscriptions'));
		$query->where('title = ' . $db->quote($this->title) . 
			' AND catid = ' . (int) $this->catid);
		$db->setQuery($query);

		$xid = intval($db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::_('COM_JOOMPROSUBS_ERR_TABLES_NAME'));
			return false;
		}

		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '') {
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		// Check the publish down date is not earlier than publish up.
		if (intval($this->publish_down) > 0 && $this->publish_down < $this->publish_up) {
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}

		return true;
	}
}
