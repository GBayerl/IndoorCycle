<?php
/**
 * @version		$Id: view.html.php 284 2011-11-11 16:17:14Z dextercowley $
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit a contact.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_joomprosubs
 */
class JoomprosubsViewSubscription extends JView
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

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
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user = JFactory::getUser();
		$isNew = ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo = JoomprosubsHelper::getActions($this->state->get('filter.category_id'), $this->item->id);

		JToolBarHelper::title(JText::_('COM_JOOMPROSUBS_MANAGER_JOOMPROSUB'), 'newfeeds.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||(count($user->getAuthorisedCategories('com_joomprosubs', 'core.create')))))
		{
			JToolBarHelper::apply('subscription.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('subscription.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && (count($user->getAuthorisedCategories('com_joomprosubs', 'core.create')))){			
			JToolBarHelper::custom('subscription.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && (count($user->getAuthorisedCategories('com_joomprosubs', 'core.create')) > 0)) {
			JToolBarHelper::custom('subscription.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('subscription.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('subscription.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('', '', JText::_('COM_JOOMPROSUBS_SUBSCRIPTION_HELP_LINK'));
	}
}
