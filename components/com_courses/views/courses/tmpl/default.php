<?php
defined ('_JEXEC') or die;
$nullDate = JFactory::getDbo()->getNullDate();
//echo $this->freebikes;
?>

<h1>Momentan werden folgende Kurse angeboten</h1>
                    <?php if ($this->items){ ?>
    <table align="center" cellspacint="0" border="1" cellpadding="5px">
    <tr>
        <th width="90px">Datum</th>
        <th width="70px">Uhrzeit</th>
        <th width="120px">Kurs</th>
        <th width="150px">Schwierigkeit</th>
        <th width="80px">Trainer</th>
        <th width="90px">Dauer</th>
        <th width="70px">Freie Plätze</th>
    </tr>


    <?php foreach ($this->items as $item) :?>

        <tr>
            <td><?php echo JHtml::_('date', $item->coursedate, 'd.m.Y'); ?></td>
            <td><?php echo $item->coursetime; ?></td>
            <td><?php 
                        if (!$item->free == 0){
                                //$link = JRoute::_("index.php?option=com_courses&view=course&id=" .$item->course_id);
                                $link = JRoute::_("index.php?com_courses&view=course&id=" .$item->course_id);
                            echo '<a href="' . $link . '">' . $item->coursename . '</a>';
                        } else {
                                echo $item->coursename;
                        }
                 ?>
            </td>
            <td><?php echo $item->levelname; ?></td>
            <td><?php echo $item->trainer; ?></td>
            <td><?php echo $item->duration; ?> </td>
            <td><?php echo $item->free; ?> </td>
        </tr>
    <?php endforeach; 
}?>

</table>
<?php

?>
