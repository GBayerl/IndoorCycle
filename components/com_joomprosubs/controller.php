<?php
/**
 * @version		$Id: controller.php 287 2011-11-11 23:13:33Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_joomprosubs
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Joomprosubs Component Controller
 *
 */
class JoomproSubsController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean       If true, the view output will be cached
	 * @param	array         An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController   This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Initialise variables.
		$cachable	= true;	
		$user		= JFactory::getUser();

		// Set the default view name and format from the Request.
		// Note we are using sub_id to avoid collisions with the router and the return page.
		$id	= JRequest::getInt('sub_id');
		$vName = JRequest::getCmd('view', 'category');
		JRequest::setVar('view', $vName);

		if ($user->get('id')) {
			$cachable = false;
		}

		$safeurlparams = array(
			'id'				=> 'INT',
			'limit'				=> 'INT',
			'limitstart'		=> 'INT',
			'filter_order'		=> 'CMD',
			'filter_order_Dir'	=> 'CMD',
			'lang'				=> 'CMD'
		);

		// Check for edit form.
		if ($vName == 'form' && !$this->checkEditId('com_joomprosubs.edit.subscription', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		return parent::display($cachable,$safeurlparams);
	}
}
