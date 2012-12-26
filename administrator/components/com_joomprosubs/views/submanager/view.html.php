<?php
/**
 * @version		$Id: view.html.php 283 2011-11-10 23:31:14Z dextercowley $
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list ofJoomprosubs.
 *
 */
class JoomproSubsViewSubManager extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JLoader::register('JoomproSubsHelper', JPATH_COMPONENT.'/helpers/joomprosubs.php');

		$state	= $this->get('State');
		$canDo	= JoomprosubsHelper::getActions($state->get('filter.category_id'));
		$user	= JFactory::getUser();
		
		JToolBarHelper::title(JText::_('COM_JOOMPROSUBS_MANAGER_JOOMPROSUBS'), 'newsfeeds.png');
		if (count($user->getAuthorisedCategories('com_joomprosubs', 'core.create')) > 0) {
			JToolBarHelper::addNew('subscription.add','JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('subscription.edit','JTOOLBAR_EDIT');
		}

		// Add export toolbar
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'export', 'COM_JOOMPROSUBS_TOOLBAR_CSVREPORT', 
			'index.php?option=com_joomprosubs&task=submanager.csvreport');

		if ($canDo->get('core.edit.state')) {

			JToolBarHelper::divider();
			JToolBarHelper::publish('submanager.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('submanager.unpublish', 'JTOOLBAR_UNPUBLISH', true);

			JToolBarHelper::divider();
			JToolBarHelper::archiveList('submanager.archive');
			JToolBarHelper::checkin('submanager.checkin');
		}
		if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'submanager.delete','JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('submanager.trash','JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_joomprosubs');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('', '', JText::_('COM_JOOMPROSUBS_SUBMANAGER_HELP_LINK'));
	}
}
