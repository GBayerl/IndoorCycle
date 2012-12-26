<?php
/**
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Joomprosubs Component Category Tree
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_joomprosubs
 */
class JoomproSubsCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__joompro_subscriptions';
		$options['extension'] = 'com_joomprosubs';
		$options['statefield'] = 'published';
		parent::__construct($options);
	}
} // end of class
