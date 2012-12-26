<?php
/**
 * @version		$Id: baseview.php 834 2012-11-13 17:58:50Z jeffchannell $
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

// include the path helper
JLoader::register('JCalProHelperPath', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/path.php');
// include the html helper here
jimport('joomla.html.html');
JHtml::addIncludePath(JCalProHelperPath::site().'/helpers/html');
// include core libs
jimport('joomla.error.profiler');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
// include other helpers
JLoader::register('JCalPro', JCalProHelperPath::helper('jcalpro'));
JLoader::register('JCalProHelperDate', JCalProHelperPath::helper('date'));
JLoader::register('JCalProHelperFilter', JCalProHelperPath::helper('filter'));
JLoader::register('JCalProHelperMail', JCalProHelperPath::helper('mail'));
JLoader::register('JCalProHelperTheme', JCalProHelperPath::helper('theme'));
JLoader::register('JCalProHelperUrl', JCalProHelperPath::helper('url'));
// we have to always load the language file for com_categories
JCalPro::language('com_categories', JPATH_ADMINISTRATOR);

// create an intermediary dummy class
if (jimport('joomla.application.component.view')) {
	class JCalProBaseView extends JView
	{
		
	}
}
else {
	jimport('legacy.view.legacy');
	class JCalProBaseView extends JViewLegacy
	{
		
	}
}


class JCalProView extends JCalProBaseView
{
	public static $option = 'com_jcalpro';
	
	protected $_filters;

	function display($tpl = null, $echo = true) {
		$profiler = JProfiler::getInstance('Application');
		$profiler->mark('onJCalProViewDisplayStart');
		
		$app = JFactory::getApplication();
		
		$this->viewClass = 'jcl_component';
		if (JCalPro::version()->isCompatible('3.0')) {
			$this->viewClass .= ' jcl_bootstrap';
		}
		
		// add actions to debugger
		if (defined('JDEBUG') && JDEBUG) {
			$user = JFactory::getUser();
			if (!$app->isAdmin() && $user->id) {
				// Permissions
				JCalPro::debugger('Component Permissions', array(
					'core.admin' => JCalPro::canDo('core.admin')
				,	'core.manage' => JCalPro::canDo('core.manage')
				,	'core.create' => JCalPro::canDo('core.create')
				,	'core.create.private' => JCalPro::canDo('core.create.private')
				,	'core.delete' => JCalPro::canDo('core.delete')
				,	'core.edit' => JCalPro::canDo('core.edit')
				,	'core.edit.state' => JCalPro::canDo('core.edit.state')
				,	'core.edit.own' => JCalPro::canDo('core.edit.own')
				,	'core.moderate' => JCalPro::canDo('core.moderate')
				));
				JCalPro::debugger('Can Email', JCalProHelperMail::canSendTo($user->email) ? 'true' : 'false');
			}
		}
		
		// add the common tmpl path so we can load our commonly shared files
		$this->addTemplatePath(($app->isAdmin() ? JCalProHelperPath::admin() : JCalProHelperPath::site()) . '/views/common/tmpl');
		
		// are we in component view?
		$this->tpl = 'component' == $app->input->get('tmpl', '', 'cmd');
		
		if (!$app->isAdmin()) {
			// Initialise variables
			$state		 = $this->get('State');
			$context   = $this->get('Context');
			// these are page params only... ?
			$params    = $state->params;
			// get the theme from the request (or from config) 
			$template  = JCalProHelperTheme::current();
			// are we in a raw view?
			$this->raw = ('raw' == $app->input->get('format', '', 'cmd'));
			// component params
			$cparams   = JComponentHelper::getParams(self::$option);
			// Escape strings for HTML output
			$this->pageclass_sfx = JCalProHelperFilter::escape($params->get('pageclass_sfx'));
			
			// assign variables to the view
			$this->template   = property_exists($this, 'template') && !empty($this->template) ? $this->template : $template;
			$this->cparams    = $cparams;
			$this->params     = $params;
			$this->state      = $state;
			$this->context    = $context;
			
			if (property_exists($this, 'extmode')) {
				try {
					// title limits
					$this->title_limit       = max(0, (int) JCalPro::config($this->extmode . "_title_limit", 0));
					// description & limits
					$this->show_description  = (bool) JCalPro::config($this->extmode . "_description", 1);
					$this->description_limit = max(0, (int) JCalPro::config($this->extmode . "_description_limit", 0));
					JCalPro::debugger('Limits', array(
						'title_limit' => $this->title_limit
					,	'description_limit' => $this->description_limit
					,	'show_description' => $this->show_description
					));
				}
				// dump errors, we don't need em
				catch (Exception $e) {
				}
			}
			
			// show heading?
			$this->show_page_heading = false;
			if (is_object($this->state) && method_exists($this->state, 'get')) {
				$menuparams = $this->state->get('parameters.menu');
				if (is_object($menuparams) && method_exists($menuparams, 'get')) {
					$this->show_page_heading = $this->state->get('parameters.menu')->get('show_page_heading');
				}
				else {
					$this->show_page_heading = $this->state->get('show_page_heading');
				}
			}
			
			// add debug info
			JCalPro::debugger('Context', $this->context);
			if (property_exists($this, 'extmode')) JCalPro::debugger('Extmode', $this->extmode);
			JCalPro::debugger('Template', $this->template);
			JCalPro::debugger('State', $this->state);
			JCalPro::debugger('Params', $this->params);
			
		}
		// prepare the document and display
		$this->_prepareDocument();
		
		$profiler->mark('onJCalProViewDisplayEnd');
		
		if ($echo) {
			parent::display($tpl);
		}
		else {
			return $this->loadTemplate($tpl);
		}
	}
	
	/**
	 * used to add administrator toolbar
	 */
	public function addToolBar() {
		
		// only fire in administrator
		if (!JFactory::getApplication()->isAdmin()) return;
		
		if (JFactory::getUser()->authorise('core.manage', self::$option)) {
			JToolBarHelper::preferences(self::$option);
		}
		
		JToolBarHelper::divider();
		// help!!!
		JToolBarHelper::help('COM_JCALPRO_HELP', false, JCalPro::config('jcalpro_help_url'));
		
	}
	
	public function addMenuBar() {
		
		$app = JFactory::getApplication();
		
		// only fire in administrator
		if (!$app->isAdmin()) return;
		
		$vName  = $app->input->get('view', '', 'cmd');
		$option = $app->input->get('option', '', 'cmd');
		// Dashboard
		JSubMenuHelper::addEntry(JText::_(strtoupper(self::$option)), JCalProHelperUrl::_(), $option == self::$option && in_array($vName, array('', 'dashboard')));
		// the rest
		$subMenuItems = array('events', 'locations', 'registrations', 'forms', 'fields', 'emails', 'about', 'help');
		foreach ($subMenuItems as $sub) {
			$label = JText::_(strtoupper(self::$option . "_$sub"));
			$href = JCalProHelperUrl::_(array('view' => $sub));
			$active = ($vName == $sub);
			JSubMenuHelper::addEntry($label, $href, $active);
			// we want categories AFTER events
			if ('events' == $sub) JSubMenuHelper::addEntry(JText::_('COM_CATEGORIES'), JCalProHelperUrl::_(array('option' => 'com_categories', 'extension' => self::$option)), 'com_categories' == $option);
		}
	}
	
	/**
	 * Load a template file -- first look in the templates folder for an override
	 * 
	 * @param   string   The name of the template source file ...
	 *                   automatically searches the template paths and compiles as needed.
	 * @return  string   The output of the the template script.
	 */
	public function loadTemplate($tpl = null) {
		// clear prior output
		$this->_output = null;
		
		$template = JFactory::getApplication()->getTemplate();
		$layout = $this->getLayout();
		$layoutTemplate = $this->getLayoutTemplate();
		$theme = property_exists($this, 'template') && !empty($this->template) ? $this->template : JCalProHelperTheme::current();
		
		// Create the template file name based on the layout
		$file = isset($tpl) ? $theme.'_'.$tpl : $theme;
		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl  = isset($tpl)? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;
		
		// Load the language file for the template
		$lang   = JFactory::getLanguage();
		$lang->load('tpl_'.$template, JPATH_BASE, null, false, false)
		|| $lang->load('tpl_'.$template, JPATH_THEMES."/$template", null, false, false)
		|| $lang->load('tpl_'.$template, JPATH_BASE, $lang->getDefault(), false, false)
		|| $lang->load('tpl_'.$template, JPATH_THEMES."/$template", $lang->getDefault(), false, false)
		;
		
		// Change the template folder if alternative layout is in different template
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template) {
			$this->_path['template'] = str_replace($template, $layoutTemplate, $this->_path['template']);
		}
		
		// Load the template script
		jimport('joomla.filesystem.path');
		$filetofind     = $this->_createFileName('template', array('name' => $file));
		$this->_template = JPath::find($this->_path['template'], $filetofind);
		
		// If alternate template layout can't be found, try the "theme" layout
		if ($this->_template == false) {
			$filetofind = $this->_createFileName('', array('name' => $theme . (isset($tpl) ? '_' . $tpl : $tpl)));
			$this->_template = JPath::find($this->_path['template'], $filetofind);
		}
		
		// If alternate template layout can't be found, try the requested layout
		if ($this->_template == false) {
			$filetofind = $this->_createFileName('', array('name' => $layout . (isset($tpl) ? '_' . $tpl : $tpl)));
			$this->_template = JPath::find($this->_path['template'], $filetofind);
		}
		
		// If alternate layout can't be found, fall back to default layout
		if ($this->_template == false) {
			$filetofind = $this->_createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
			$this->_template = JPath::find($this->_path['template'], $filetofind);
		}
		
		if ($this->_template != false) {
			// Unset so as not to introduce into template scope
			unset($tpl);
			unset($file);
			
			// Never allow a 'this' property
			if (isset($this->this)) {
				unset($this->this);
			}
			
			// Start capturing output into a buffer
			ob_start();
			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;
			
			// Done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();
			
			return $this->_output;
		}
		else {
			return JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file));
		}
	} 

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument() {
		
		$app = JFactory::getApplication();
		
		// load js framework
		JHtml::_('behavior.framework', true);
		
		// load the jcal framework helper
		$this->document->addScript(JCalProHelperUrl::media() . '/js/jcalpro.js');
		
		// we don't want to run this whole function in admin,
		// but there's still a bit we need - specifically, styles for header icons
		// if we're in admin, just load the stylesheet and bail
		if ($app->isAdmin()) {
			$this->document->addStyleSheet(JCalProHelperUrl::media() . '/css/admin.css');
			// grab the modal styles if necessary
			// EDIT: don't do this for location view
			if ($this->tpl && 'modal' == $app->input->get('layout', '', 'cmd') && !preg_match('/^location/', $app->input->get('view', '', 'cmd'))) {
				$this->document->addStyleSheet(JCalProHelperUrl::media() . '/css/modal.css');
			}
			return;
		}
		
		jimport('joomla.filesystem.file');
		
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;
		
		// load common css
		$this->document->addStyleSheet(JCalProHelperUrl::media() . '/css/common.css');
		// load default css (or print css, depending)
		$css = ($this->tpl ? ('modal' == $app->input->get('layout', '', 'cmd') ? 'modal' : 'print') : 'default');
		JCalProHelperTheme::addStyleSheet($css, 'css', $this->template);
		
		// load extmode-specific assets
		if (isset($this->extmode) && $this->extmode) {
			// check for general extmode scripts
			$script = '/js/' . $this->extmode . '.js';
			if (JFile::exists(JCalProHelperPath::media() . $script)) {
				$this->document->addScript(JCalProHelperUrl::media() . $script);
			}
		}
		
		// we only want the ajax script if ajax features are enabled!
		if (JCalPro::config('enable_ajax_features', true)) {
			$this->document->addScript(JCalProHelperUrl::media() . '/js/ajax.js');
		}

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else {
			$this->params->def('page_heading', JText::_('COM_JCALPRO_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);
		/*
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
		*/
	}
}
