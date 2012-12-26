<?php
/**
 * @subpackage	com_joomprosubs
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// Code to support edit links for joomaprosubs
// Create a shortcut for params.

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::core();

// Get the user object.
$user = JFactory::getUser();

// Check if user is allowed to add/edit based on joomprosubs permissions.
$canEdit    = $user->authorise('core.edit', 'com_joomprosubs.category.' . $this->category->id, 'com_joomprosubs.subscription.' . $item->id);
$isTrainer  = $user->authorise('subscription.listsubs');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$listFilter = $this->state->get('list.filter');
?>

<?php if (empty($this->items) && ($listFilter == '')) : ?>
	<p> <?php echo JText::_('COM_JOOMPROSUBS_NO_JOOMPROSUBS'); ?></p>
<?php else : ?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" 
	method="post" name="adminForm" id="adminForm">
	<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
	<div class="filter-search">
		<label class="filter-search-lbl" for="filter-search"
			><?php echo JText::_('COM_JOOMPROSUBS_FILTER_LABEL').'&#160;'; ?></label>
		<input type="text" name="filter-search" id="filter-search" 
			value="<?php echo $this->escape($this->state->get('list.filter')); ?>" 
			class="inputbox" onchange="document.adminForm.submit();" 
			title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
	</div>
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	</fieldset>

	<table class="category">
		<thead><tr>
			
			<th class="begin" width="20%">
				<?php echo JHtml::_('grid.sort', 'COM_JOOMPROSUBS_GRID_BEGIN', 
					'a.begin', $listDirn, $listOrder); ?>
			</th>
			<th class="title" width="40%">
				<?php echo JHtml::_('grid.sort', 'COM_JOOMPROSUBS_GRID_TITLE', 
					'a.title', $listDirn, $listOrder); ?>
                        <th class="trainer" width="25%">
                            <?php echo JHtml::_('grid.sort',  'COM_JOOMPROSUBS_GRID_LEVEL', 
                                    'level', $listDirn, $listOrder); ?>
			</th>
                        <th class="trainer" width="25%">
				<?php echo JHtml::_('grid.sort',  'COM_JOOMPROSUBS_GRID_TRAINER', 
					'a.trainer', $listDirn, $listOrder); ?>
			</th>
                        <th class="bikes" width="10%">
				<?php echo JHtml::_('grid.sort',  'COM_JOOMPROSUBS_GRID_BIKES', 
					'a.free', $listDirn, $listOrder); ?>
			</th>
		</tr></thead>
	<tbody>
	<?php foreach ($this->items as $i => $item) : ?>
            <?php 
            $timetoopen = strtotime($item->begin. ' - 1 hour');
            $now = strtotime (now);
            ?>
		<tr class="cat-list-row<?php echo $i % 2; ?>" >
		
                <td class="item-begin">
			<?php echo $item->begin; ?>
		</td>	
                <td class="title">
			<?php if ($canEdit AND $timetoopen > $now)  : ?>
				<a href="<?php echo JRoute::_('index.php?option=com_joomprosubs&task=subscription.edit&sub_id='.$item->id.'&catid='.$item->catid); ?>">
				<?php echo $item->title; ?></a>
			<?php else: ?>
				<?php echo $item->title;?>
			<?php endif; ?>
			<?php if ($this->params->get('show_description')) : ?>
				<?php echo nl2br($item->description); ?>
			<?php endif; ?>
		</td>
                <td class="item-level">
			<?php echo $item->level; ?>
		</td>
		
		<td class="item-trainer">
			<?php echo $item->trainer; ?>
		</td>
                <td class="item-bikes">
			<?php if ($isTrainer) : ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_joomprosubs&view=subscriber&sub_id='.$item->id); ?>">
			    <?php echo $item->free; ?></a>
                        <?php else: ?>
                            <?php echo $item->free; ?>
                        <?php endif; ?>
                    
		</td>	
		</tr>
	<?php endforeach; ?>
</tbody>
</table>
<div class="pagination">
	<p class="counter">
	<?php echo $this->pagination->getPagesCounter(); ?>
	</p>
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<div>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</div>
</form>
<?php endif; ?>