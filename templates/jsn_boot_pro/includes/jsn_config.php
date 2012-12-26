<?php
/**
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

	// No direct access
	defined('_JEXEC') or die('Restricted access');
	
	// Template attributes
	$jsn_template_attrs = array(
		'width' => array ('narrow', 'wide', 'float'),
		'textstyle' => array('business', 'personal', 'news'),
		'textsize' => array('small', 'medium', 'big'),
		'color' => array ('blue', 'red', 'green', 'christmas'),
		'direction' => array ('ltr', 'rtl'),
		'specialfont' => array ('yes', 'no'),
		'mobile' => array ('yes', 'no'),
		'promoleftwidth' => 'integer',
		'promorightwidth' => 'integer',
		'leftwidth' => 'integer',
		'rightwidth' => 'integer',
		'innerleftwidth' => 'integer',
		'innerrightwidth' => 'integer',
		'preset' => array('default')
	);
	
	$jsn_textstyles_config = array(
		'business' => array(
			'font-a' => 'Arial, Helvetica, sans-serif',
			'font-b' => 'Verdana, Geneva, Arial, Helvetica, sans-serif',
			'font-s' => 'Oswald',
			'font-sw' => 'regular,bold',
			'size-small' => '65%',
			'size-medium' => '75%',
			'size-big' => '85%'
		),
		'personal' => array(
			'font-a' => '"Trebuchet MS", Helvetica, sans-serif',
			'font-b' => 'Georgia, serif',
			'font-s' => 'Italiana',
			'font-sw' => '',
			'size-small' => '70%',
			'size-medium' => '80%',
			'size-big' => '90%'
		),
		'news' => array(
			'font-a' => '"Times New Roman", Times, serif',
			'font-b' => '"Palatino Linotype", Palatino, serif',
			'font-s' => 'Cinzel Decorative',
			'font-sw' => 'regular,bold',
			'size-small' => '75%',
			'size-medium' => '85%',
			'size-big' => '95%'
		)
	);
	
	$jsn_font_b_elements = array(
		'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
		'#jsn-menu a', '#jsn-menu span', '#jsn-gotoplink',
		'.componentheading', '.contentheading'
	);
?>