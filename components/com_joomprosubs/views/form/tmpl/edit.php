<?php

 /**
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
$subid = JRequest::getInt('sub_id');
$itemid = JRequest::getInt('Itemid');
$session = JFactory::getsession();

$timetosubscribe = strtotime($this->item->begin. ' - 1 hour');
$timetounsubscribe = strtotime ($this->item->begin. ' - 36 hour');
$now = strtotime (now);
?>

<div class="edit<?php echo $this->pageclass_sfx; ?>">
	<form action="<?php echo JRoute::_('index.php'); ?>" 
			id="adminForm" name="adminForm" method="post" class="form-validate">
		<fieldset>
		<legend><?php echo JText::_('COM_JOOMPROSUBS_FORM_LABEL'); ?></legend>
			<dl>
				<dt><?php echo JText::_('COM_JOOMPROSUBS_GRID_TITLE'); ?></dt>
				<dd><?php echo $this->escape($this->item->get('title')); ?></dd>
				<dt><?php echo JText::_('COM_JOOMPROSUBS_GRID_DESC'); ?></dt>
				<dd><?php echo $this->escape(strip_tags($this->item->get('description'))); ?></dd>
				<dt><?php echo $this->form->getLabel('subscription_terms'); ?></dt>
				<dd><?php echo $this->form->getInput('subscription_terms'); ?></dd>
			</dl>
		</fieldset>
		<fieldset>
			
                        <?php if (!$session->has('course_' . $subid)) { ?>
                            <button class="button validate" type="submit"><?php echo JText::_('COM_JOOMPRPOSUBS_FORM_SUBMIT'); ?></button>
                            <input type="hidden" name="task" value="subscription.subscribe" />
                            
                        <?php } else if ($timetounsubscribe > $now) { ?>
                            
                            <button class="button validate" type="submit"><?php echo JText::_('COM_JOOMPRPOSUBS_FORM_UNSUBMIT'); ?></button>
                            <input type="hidden" name="task" value="subscription.unsubscribe" />
                        <?php } ?>
                        
                        <input type="hidden" name="option" value="com_joomprosubs" />
                            
                        <a href="<?php echo JRoute::_('index.php?option=com_joomprosubs&Itemid='. $itemid); ?>">                      
                        <?php echo JText::_('COM_JOOMPRPOSUBS_FORM_CANCEL'); ?></a>                        
                           
			<input type="hidden" name="sub_id" value="<?php echo $this->item->id; ?>" />
			<?php echo JHtml::_( 'form.token' ); ?>
		</fieldset>
	</form>
    
</div>