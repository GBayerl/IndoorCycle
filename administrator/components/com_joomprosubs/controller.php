<?php
/**
 * @version		$Id: controller.php 284 2011-11-11 16:17:14Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_joomprosubs
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 *Joomprosubs joomprosub Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_joomprosubs
 */
class JoomproSubsController extends JController
{
	/**
	* @var		string	The default view.
	* @since	1.6
	*/
	protected $default_view = 'submanager';
	
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 */	
	public function display($cachable = false, $urlparams = false)
	{
		JLoader::register('JoomproSubsHelper', JPATH_COMPONENT.'/helpers/joomprosubs.php');

		// Load the submenu.
		JoomproSubsHelper::addSubmenu(JRequest::getCmd('view', 'submanager'));

		$view = JRequest::getCmd('view', 'submanager');
		$layout	= JRequest::getCmd('layout', 'default');
		$id = JRequest::getInt('id');

		// Check for edit form.
		if ($view == 'subscription' && $layout == 'edit' && !$this->checkEditId('com_joomprosubs.edit.subscription', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_joomprosubs&view=submanager', false));

			return false;
		}

		parent::display();

		return $this;
	}
}