<?php
/**
 * @version		$Id: baseview.php 806 2012-09-20 22:39:57Z jeffchannell $
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

jimport('joomla.application.component.view');
JLoader::register('JCalProHelperFilter', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/filter.php');
JLoader::register('JCalProView', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/views/baseview.php');

/**
 * JCalPro events view.
 *
 * @package		JCalPro
 * @subpackage	com_jcalpro
 */
class JCalProViewEvents extends JCalProView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;

	/**
	 * Display the view
	 *
	 * @return	mixed	False on error, null otherwise.
	 */
	function display($tpl = null, $echo = true) {
		
		$app = JFactory::getApplication();
		
		$profiler = JProfiler::getInstance('Application');
		$profiler->mark('onJCalProEventsViewDisplayStart');
		// we need to set the format in the model's state in case of ical
		$format = $app->input->get('format', 'html', 'cmd');
		$this->getModel()->setState('filter.format', $format);
		// get the items from the model
		$profiler->mark('onJCalProEventsViewDisplayBeforeGetItems');
		$items      = $this->get('Items');
		$profiler->mark('onJCalProEventsViewDisplayBeforeGetCategories');
		$categories = $this->get('Categories');
		$profiler->mark('onJCalProEventsViewDisplayBeforeGetLinkData');
		$linkdata   = $this->get('LinkData');
		$profiler->mark('onJCalProEventsViewDisplayBeforeGetPending');
		$pending    = $this->get('Pending');
	
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		$this->dates      = $this->get('AllTheDates'); // challenge accepted
		$this->items      = $items;
		$this->categories = $categories;
		$this->linkdata   = $linkdata;
		$this->extmode    = $this->getRealLayout();
		$this->pending    = $pending;
		
		JCalPro::debugger('Dates', $this->dates);
		JCalPro::debugger('Items', $this->items);
		JCalPro::debugger('Categories', $this->categories);
		JCalPro::debugger('Linkdata', $this->linkdata);
		JCalPro::debugger('Extmode', $this->extmode);
		JCalPro::debugger('Pending', $this->pending);
		
		// alter the data based on layout
		switch ($this->extmode) {
			case 'month':
			case 'flat':
			case 'week':
			case 'day':
				break;
			case 'admin':
				/*
				if (!JCalPro::canModerateEvents() || !JCalPro::canDeleteEvents()) {
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
					return false;
				}
				*/
				// start building filters
				$filters = array();
				// filter values
				$values = array(
					'ALL'         => JCalProHelperUrl::events('', 'admin', true, array('filter_published'=>'', 'filter_approved'=>'', 'filter_date_range'=>''))
				,	'UNPUBLISHED' => JCalProHelperUrl::events('', 'admin', true, array('filter_published'=>'0', 'filter_approved'=>'', 'filter_date_range'=>''))
				,	'UNAPPROVED'  => JCalProHelperUrl::events('', 'admin', true, array('filter_published'=>'', 'filter_approved'=>'0', 'filter_date_range'=>''))
				,	'UNALL'       => JCalProHelperUrl::events('', 'admin', true, array('filter_published'=>'0', 'filter_approved'=>'0', 'filter_date_range'=>''))
				,	'UPCOMING'    => JCalProHelperUrl::events('', 'admin', true, array('filter_published'=>'1', 'filter_approved'=>'', 'filter_date_range'=>'2'))
				,	'PAST'        => JCalProHelperUrl::events('', 'admin', true, array('filter_published'=>'1', 'filter_approved'=>'', 'filter_date_range'=>'1'))
				);
				
				if (!JCalPro::canModerateEvents()) unset($values['UNAPPROVED']);
				
				foreach ($values as $key => $value) {
					$filters[] = JHtml::_('select.option', $value, JText::_('COM_JCALPRO_ADMIN_FILTER_'.$key.'_EVENTS'));
				}
				// figure out the default value
				$filter_published  = $app->input->get('filter_published', '', 'cmd');
				$filter_approved   = $app->input->get('filter_approved', '', 'cmd');
				$filter_date_range = $app->input->get('filter_date_range', '', 'cmd');
				$default = 'ALL';
				if ('' == $filter_published && '0' == $filter_approved && '' == $filter_date_range) {
					$default = 'UNAPPROVED';
				}
				else if ('0' == $filter_published && '' == $filter_approved && '' == $filter_date_range) {
					$default = 'UNPUBLISHED';
				}
				else if ('0' == $filter_published && '0' == $filter_approved && '' == $filter_date_range) {
					$default = 'UNALL';
				}
				else if ('1' == $filter_published && '' == $filter_approved && '2' == $filter_date_range) {
					$default = 'UPCOMING';
				}
				else if ('1' == $filter_published && '' == $filter_approved && '1' == $filter_date_range) {
					$default = 'PAST';
				}
				// build select
				$this->admin_filter = JHtml::_('select.genericlist', $filters, 'admin_filter', '', 'value', 'text', @$values[$default]);
				JFactory::getDocument()->addScriptDeclaration('window.addEvent(\'load\', function(){$(\'admin_filter\').addEvent(\'change\', function(el){document.location.href=this.getElement(\':selected\').value;});});');
				break;
			default: break;
		}
		
		// switch the different view modes depending on format
		switch ($format) {
			// ical format, register the ical helper, process the ical and exit
			case 'ical':
				JLoader::register('JCalProHelperIcal', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/ical.php');
				echo JCalProHelperIcal::toIcal($this->items);
				jexit();
				break;
			// rss feeds
			case 'feed':
				$doc = JFactory::getDocument();
				// create the url from the request
				$doc->link = JCalProHelperUrl::page(array(), array('format', 'type'));
				// loop our items and start constructing the feed
				if (!empty($items)) {
					// add the items to the feed
					foreach ($items as $item) {
						// create a new feed item
						$fitem = new JFeedItem();
						// set the title
						$title = JCalProHelperFilter::escape($item->title);
						$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
						$fitem->title = $title;
						// set the url
						$fitem->link = $item->href;
						// set the description
						$fitem->description = strip_tags($item->description);
						// set the date
						$fitem->date = $item->user_datetime->format(DateTime::ATOM);
						// author
						$fitem->author = $item->author_name;
						
						// add the item into the document
						$doc->addItem($fitem);
					}
				}
				break;
			// all other formats we just display normally
			default:
				// display
				parent::display($tpl);
		}
		$profiler->mark('onJCalProEventsViewDisplayEnd');
	}
	
	
	protected function _prepareDocument() {
		parent::_prepareDocument();
		// Add feed links
		if (!$this->raw && JCalPro::config('enable_feeds', 1) && method_exists($this->document, 'addHeadLink')) {
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}
	}
	
	/**
	 * overriding this so we can trick Joomla! into only loading default view :)
	 */
	public function getLayout() {
		// TODO: check templates for alternate layouts
		return 'default';
	}
	
	public function getRealLayout() {
		return $this->_layout;
	}
}
