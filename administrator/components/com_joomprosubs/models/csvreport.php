<?php
/**
 * @version		$Id: csvreport.php 293 2011-11-12 18:34:35Z dextercowley $
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
JLoader::register('JoomproSubsModelSubManager', 
	JPATH_COMPONENT.'/models/submanager.php');

/**
 * Methods supporting a list of joomprosub records.
 *
 */
class JoomproSubsModelCSVReport extends JoomproSubsModelSubManager
{
	/**
	 * Method to set the state using the values from the submanager view
	 */
	public function setModelState()
	{
		$this->context = 'com_joomprosubs.submanager';
		parent::populateState();
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.id AS subsciption_id, 
			a.title AS subscription_title, 
			g.title AS group_title, c.title AS category_title, 
			a.alias AS subscription_alias, 
			a.description AS subscription_description, a.duration, 
			a.published AS subscription_published, 
			a.access AS subscription_access, 
			uc.name AS subscriber_name');
		$query->from($db->quoteName('#__joompro_subscriptions').' AS a');

		// Join over the mapping table to get subscribers
		$query->select('m.user_id as subscriber_id, m.start_date, m.end_date');
		$query->join('LEFT', $db->quoteName('#__joompro_sub_mapping').' AS m ON m.subscription_id = a.id');

		// Join over the users for the subscribed user.
		$query->join('LEFT', $db->quoteName('#__users').' AS uc ON uc.id = m.user_id');
		
		// Join over the user groups to get the group name
		$query->join('LEFT', $db->quoteName('#__usergroups').' AS g ON a.group_id = g.id');

		// Join over the categories.
		$query->join('LEFT', $db->quoteName('#__categories').' AS c ON c.id = a.catid');
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('a.catid = '.(int) $categoryId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		return $query;
	}
} // end of class