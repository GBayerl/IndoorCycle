<?php
/**
 * @version		$Id: theme.php 807 2012-10-02 18:53:43Z jeffchannell $
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

// register the path helper
JLoader::register('JCalProHelperPath', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/path.php');
// register the other helpers
JLoader::register('JCalPro', JCalProHelperPath::helper() . '/jcalpro.php');
JLoader::register('JCalProHelperFilter', JCalProHelperPath::helper().'/filter.php');
JLoader::register('JCalProHelperUrl', JCalProHelperPath::helper().'/url.php');

abstract class JCalProHelperTheme
{
	/**
	 * static method to get the current theme
	 * 
	 * @return string
	 */
	static public function current() {
		static $theme;
		if (!isset($theme)) {
			jimport('joomla.filesystem.folder');
			$app = JFactory::getApplication();
			$theme  = basename($app->getUserStateFromRequest(JCalPro::COM . '.theme', 'theme', JCalPro::config('default_theme', ''), 'string'));
			// check if the theme is actually available
			if (!JFolder::exists(JCalProHelperPath::theme() . '/' . $theme)) {
				$theme = '';
				$app->setUserState(JCalPro::COM . '.theme', '');
			}
		}
		return $theme;
	}
	
	/**
	 * static method to add a stylesheet to the document
	 * 
	 * @param string $file
	 * @param string $base
	 * @param string $template
	 */
	static public function addStyleSheet($file, $base = 'css', $template = '') {
		// get our document
		$doc = JFactory::getDocument();
		// if the document cannot have styles added, bail
		if (!method_exists($doc, 'addStyleSheet')) return;
		// sanitise the filename a little
		$css = self::getFilePath("$file.css", $base, $template);
		if ($css) {
			$doc->addStyleSheet($css);
		}
	}
	
	static public function addIEStyleSheet($file, $version = 0, $diff = '') {
		$document = JFactory::getDocument();
		if (!method_exists($document, 'addCustomTag')) return;
		$version = (int) $version;
		if (!in_array($diff, array('gt', 'gte', 'lt', 'lte'))) $diff = '';
		$tag   = array('');
		$tag[] = '<!--[if ' . (empty($diff) ? '' : "$diff ") . 'IE' . ($version ? " $version" : '') . ']>';
		$tag[] = '<link href="' . JCalProHelperFilter::escape($file) . '" rel="stylesheet" type="text/css" />';
		$tag[] = '<![endif]-->';
		$tag[] = '';
		$document->addCustomTag(implode("\n", $tag));
	}
	
	/**
	 * static method to get the url of a file
	 * 
	 * @param string $file
	 * @param string $base
	 * @param string $template
	 */
	static public function getFilePath($file, $base, $template = '') {
		jimport('joomla.filesystem.file');
		// sanitise the filename a little
		$file = basename($file);
		$base = trim($base, '/');
		// now load the css file for the theme, if available
		$theme = basename(empty($template) ? self::current() : $template);
		if (!empty($theme) && JFile::exists(JCalProHelperPath::media() . "/themes/$theme/$base/$file")) {
			return JCalProHelperUrl::media() . "/themes/$theme/$base/$file";
		}
		// no theme? load the default css, if it exists
		else if (JFile::exists(JCalProHelperPath::media() . "/$base/$file")) {
			return JCalProHelperUrl::media() . "/$base/$file";
		}
		return false;
	}
	
	/**
	 * static method to get a list of available themes
	 * 
	 * @return array
	 */
	static public function getList() {
		jimport('joomla.filesystem.folder');
		// JCalPro 3 uses the "file" type extension for its themes
		// convention will follow that we'll load our data from the #__extensions table
		// basing our search on enabled rows with names like FILES_JCALTHEME_%
		// then cross-reference the "element" column with folders in media/jcalpro/themes
		$db = JFactory::getDbo();
		// go ahead and build the query to load the themes
		$query = $db->getQuery(true)
			->select('element, name')
			->from('#__extensions')
			->where('LOWER(' . $db->quoteName('name') . ') LIKE "files_jcaltheme_%"')
			->where('enabled = 1')
			->order($db->quoteName('name'))
		;
		// load the enabled themes
		$db->setQuery((string) $query);
		$dbthemes = $db->loadObjectList();
		// get our xref array from the folders in media/jcalpro/themes
		$fsthemes = JFolder::folders(JPATH_ROOT . '/media/jcalpro/themes');
		// start building our select options
		$list = array();
		// go ahead and add the default theme - this is the standard images used (and is not installed like the others)
		$list[] = JHtml::_('select.option', '', JText::_('COM_JCALPRO_THEMES_DEFAULT'), '_id', '_name');
		// loop our enabled themes to ensure they're in both the database AND filesystem
		foreach ($dbthemes as $theme) {
			// we have to remove the "jcaltheme_" prefix before searching
			$themename = preg_replace('/^jcaltheme_/i', '', $theme->element);
			// don't bother if it's not in the filesystem
			if (!in_array($themename, $fsthemes)) continue;
			// load up the language file
			JCalPro::language(strtolower($theme->name . '.sys'), JPATH_ROOT);
			// add to the list
			$list[] = JHtml::_('select.option', $themename, JText::_($theme->name . '_NAME'), '_id', '_name');
		}
		return $list;
	}
}
