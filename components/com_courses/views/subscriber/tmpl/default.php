<?php
defined ('_JEXEC') or die;
$nullDate = JFactory::getDbo()->getNullDate();

?>

<h1>Teilnehmerliste für den Kurs: </h1>
<h2>Folgende Teilehmer nehmen an oben genanntent Kurs teil</h2><br />

<?php 
if ($this->items){
    echo JHTML::date(now,'h:m:s');
    ?>
    <table align="center" cellspacint="0" border="1" cellpadding="5px">
    <tr>
        <th width="90px">User ID</th>
        <th width="200px"> Teilnehmer</th>
        <th width="200px">Anmeldezeit</th>
        <th width="200px">Kontaktnummer</th>
    </tr>


    <?php foreach ($this->items as $item) :?>

        <tr>
            <td><?php echo $item->userid; ?></td>
            <td><?php echo $item->username; ?></td>
            <td><?php echo JHTML::date($item->subscriptiontime, 'd.m.Y h:m'); ?></td>
            <td><?php echo $item->contactnumber; ?></td>
            
        </tr>
    <?php endforeach; 
 
}?>

</table>