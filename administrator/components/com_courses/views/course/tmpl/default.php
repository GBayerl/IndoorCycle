<?php
defined('_JEXEC') or die;
?>

<form action="<?php echo JRoute::_('index.php?option=com_courses&id=' . (int) $this->item->course_id); ?>" method="post" name="adminForm" id=" adminForm ">
<fieldset class="adminform">
    <legend>
        <?php echo JText::_('COM_COURSES_DATA_SET'); ?>
    </legend>
    
    <ul class="adminformlist">