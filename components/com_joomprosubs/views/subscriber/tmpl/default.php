<?php
defined ('_JEXEC') or die;
$nullDate = JFactory::getDbo()->getNullDate();

?>

<h1>Teilnehmerliste</h1>


<?php 
if ($this->items){
    echo "<h2>Kursname: " . $item->coursename . "</h2>";
    ?>
    <table align="center" cellspacint="0" border="1" cellpadding="5px">
    <tr>
        <th width="90px">User ID</th>
        <th width="200px"> Teilnehmer</th>

    </tr>


    <?php foreach ($this->items as $item) :?>

        <tr>
            <td><?php echo $item->userid; ?></td>
            <td><?php echo $item->username; ?></td>
            
        </tr>
    <?php endforeach; 
 
}?>

</table>