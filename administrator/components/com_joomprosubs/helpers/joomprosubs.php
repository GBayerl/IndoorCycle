<?php
/**
 * @version		$Id: joomprosubs.php 286 2011-11-11 19:34:35Z dextercowley $
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 *Joomprosubs helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_joomprosubs
 */
class JoomproSubsHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName = 'submanager')
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_JOOMPROSUBS_SUBMENU_JOOMPROSUBS'),
			'index.php?option=com_joomprosubs&view=submanager',
			$vName == 'submanager'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JOOMPROSUBS_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_joomprosubs',
			$vName == 'categories'
		);
		if ($vName=='categories') {
			JToolBarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE',JText::_('com_joomprosubs')),
				'joomprosubs-categories');
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @return	JObject
	 */
	public static function getActions($categoryId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($categoryId)) {
			$assetName = 'com_joomprosubs';
		} else {
			$assetName = 'com_joomprosubs.category.'.(int) $categoryId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
