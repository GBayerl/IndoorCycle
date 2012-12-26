<?php
defined ('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');

$item = $this->item;
$params = $this->params;
$user = JFactory::getUser();
$courseid = $item->course_id;
?>


<h1><?php echo $item->coursename ?></h1>
<h3><?php echo $item->description; ?></h3>
<br />

Hallo Du <br /><br />

<form id="subscribe" method="post" 
      action="<?php echo JRoute::_('index.php?option=com_courses'); ?>">

    <label>Kontaktnummer: <input type="input" name="contact" value="" /></label>
    <button type="submit">
        <?php echo "Anmelden"; ?>
    </button>
    
    <input type="hidden" name="task" value="course.subscribe" />
    <input type="hidden" name="course" value="<?php echo$item->course_id ;?>"
        <?php echo JHtml::_('form.token'); ?>
</form>


<br /><br />

    
    <?php
// @var $subscriptionlink Integer 
//$subscriptionlink = JRoute::_('index.php?com_courses&task=course.edit&id=' . $courseid);
//$link = JRoute::_("index.php?com_courses&task=course.edit=" .$item->course_id);
//echo '<a href="' . $subscriptionlink . '">Für Kurs anmelden</a>';
//echo "<br />";

$memberslink = JRoute::_('index.php?com_courses&view=subscriber&id=' . $courseid);
//$link = JRoute::_("index.php?com_courses&view=members&id=" .$item->course_id);
echo '<a href="' . $memberslink . '">Teilnehmerliste</a>';
?>           