<?php
/**
 * version $Id: view.html.php 289 2011-11-12 02:10:08Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_joomprosubs
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');


/**
 * HTML View class for the JoomproSubs component
 *
 */
class JoomprosubsViewForm extends JView
{
	protected $state;
	protected $item;
	protected $category;
        protected $substate;


	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$user = JFactory::getUser();
                $session = JFactory::getSession();
                
		// Get some data from the models
		$item = $this->get('Item');
		$this->form = $this->get('Form');
		$authorised = $user->authorise('core.edit', 'com_joomprosubs.category.' . $item->catid);
                
		if ($authorised !== true) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		$this->form->bind($item);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->params = $params;
		$this->user = $user;
		$this->item = $item;

                $this->_prepareDocument();
                $model = $this->getModel('form');
                $result = $model->getMapRow($item->id, $user->id);
                if ($result)
                {
                    $session->set('course_' . $item->id, 'true');
                }
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		$head = JText::_('COM_JOOMPROSUBS_FORM_SUBMIT_SUB');

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', $head);
		}

		$title = $this->params->def('page_title', $head);
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) 
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) 
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
} // end of class
