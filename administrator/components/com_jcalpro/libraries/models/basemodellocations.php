<?php
/**
 * @version		$Id: basemodellocations.php 803 2012-09-14 23:14:38Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro

**********************************************
JCal Pro
Copyright (c) 2006-2012 Anything-Digital.com
**********************************************
JCalPro is a native Joomla! calendar component for Joomla!

JCal Pro was once a fork of the existing Extcalendar component for Joomla!
(com_extcal_0_9_2_RC4.zip from mamboguru.com).
Extcal (http://sourceforge.net/projects/extcal) was renamed
and adapted to become a Mambo/Joomla! component by
Matthew Friedman, and further modified by David McKinnis
(mamboguru.com) to repair some security holes.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This header must not be removed. Additional contributions/changes
may be added to this header as long as no information is deleted.
**********************************************
Get the latest version of JCal Pro at:
http://anything-digital.com/
**********************************************

 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JCalProListModel', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/models/basemodellist.php');

/**
 * This model supports retrieving lists of Locations.
 *
 * @package		JCalPro
 * @subpackage	com_jcalpro
 */
class JCalProListLocationsModel extends JCalProListModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jcalpro.locations';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_jcalpro';

	private $_parent = null;

	private $_items = null;
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
		
		$app = JFactory::getApplication();
		$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$limit = $value;
		$this->setState('list.limit', $limit);
		
		$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
		
		$value = $app->getUserStateFromRequest($this->context.'.list.ordering', 'filter_order', '');
		$this->setState('list.ordering', $value);
		
		$value = $app->getUserStateFromRequest($this->context.'.list.direction', 'filter_order_Dir', '');
		$this->setState('list.direction', $value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $value);
	}

	protected function getListQuery() {
		
		$db = $this->getDbo();
	
		// main query
		$query = $db->getQuery(true)
			// Select the required fields from the table.
			->select($this->getState('list.select', 'Location.*'))
			->from('#__jcalpro_locations AS Location')
		;
		// add author to query
		$this->appendAuthorToQuery($query, 'Location');
		
		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('Location.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%', false);
				$query->where('(Location.title LIKE '.$search.' OR Location.address LIKE '.$search.')');
			}
		}
		
		// if we're in a modal (because we're selecting a location for an event)
		// or we're not in admin, filter out the ones that are trashed/unpublished
		$app = JFactory::getApplication();
		if (!$app->isAdmin() || 'modal' == $app->input->get('layout', '')) {
			$query->where('Location.published = 1');
		}
		else {
			// Filter by published state
			$published = $this->getState('filter.published');
			if (is_numeric($published)) {
				$query->where('Location.published = ' . (int) $published);
			}
			else if ($published == '') {
				$query->where('(Location.published = 0 OR Location.published = 1)');
			}
		}

		// Group by filter
		$query->group('Location.id');
		return $query;
	}
}
