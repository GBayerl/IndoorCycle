<?php
/**
 *
 * @package Joomla.Administrator
 * @subpackage com_courses
 * @copyright (c) 2012, Guenther Bayerl. All rights reserved
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludPath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('script', 'system/multiselect.js', false, true);

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
echo "in the default template";
?>

<div class=""courses-manager">
     <form action="<?php echo JRoute::_('index.php?option=com_courses&view=courses'); ?>" method="post" name="adminForm" id="adminForm">
         <fieldset id ="filter-bar">
             <div class="filter-search fltlft">
                 <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
                 <input type="text" name="filter_search" id="filter_search" value=""<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_COURSES_SEARCH_IN_TITLE'); ?>" />
                 <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
                 <button type="button" onclick="document.id('filter_search').value=''; this.form.submit();"> <?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
             </div>
             <div class="filter-select fltrt">
                 
                 <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                     <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                     <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
                 </select>
                 
                 <select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
                     <option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
                     <?php echo JHtml::_('select.options', JHtml::_('categoryOptions', 'com_courses'), 'value', 'text', $this->state->get('filter.category_id'));?>
                 </select>

                 <select name="filter_access" class="inputbox" onchange="this.form.submit()">
                     <option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
                     <?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
                 </select>
             </div>
         </fieldset>
         <div class="clr"></div>
         
         <table class="adminlist">
             <thead>
                 <tr>
                     <th style="width: 1%;">
                         <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
                     </th>
                     <th class="title">
                         <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.coursename', $listDirn, $listOrder); ?>
                     </th>
                     <th style="width: 5%;">
                         <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                     </th>
                     <th style="width: 20%;">
                         <?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
                     </th>
                     <th style="width: 20%;">
                         <?php echo JHtml::_('grid.sort', 'COM_COURSES_FIELD_USERGROUP_LABEL', 'g.title', $listDirn, $listOrder); ?>
                     </th>
                     <th style="width: 10%;">
                         <?php echo JHtml::_('grid.sort', 'COM_COURSES_FIELD_COURSEDATE_LABEL', 'a.coursedate', $listDirn, $listOrder); ?>)
                     </th>
                    <th style="width: 10%;">
                         <?php echo JHtml::_('grid.sort', 'COM_COURSES_FIELD_TRAINER_LABEL', 'a.trainer', $listDirn, $listOrder); ?>)
                    </th>
                     <th style="width: 5%;">
                         <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>)
                     </th>
                     <th style="width: 5%;" class="nowrap">
                         <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.courseid', $listDirn, $listOrder); ?>)
                     </th>
                 </tr>
             </thead>
             <tfoot>
                 <tr>
                     <td colspan="10">
                         <?php echo $this->pagination->getListFooter(); ?>
                     </td>
                 </tr>
             </tfoot>
             <tbody>
             <?php foreach ($this->items as $i => $item) :
               $ordering = ($listOrder == 'a.ordering');
               $item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_courses&task=edit&type=other&cid[]='. $item->catid);
               $canCreate = $user->authorise('core.create', 'com_courses.category.'.$item->catid);
               $canEdit = $user->authorise('core.edit', 'com_courses.category.'.$item->catid);
               $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
               $canChange = $user->authorise('core.edit.state', 'com_courses.category.'.$item->catid) && $canCheckin;
             ?>
                 <tr class="row<?php echo $i % 2; ?>"
                     <td class="center">
                         <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                     </td>
                     <td>
                         <?php if ($item->checked_out) : ?>
                         <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'courses.', $canCheckin); ?>
                         <?php endif; ?>
                         <?php if ($canEdit) : ?>
                            <a href=""<?php echo JRoute::_('index.php?option=com_courses&task=course.edit&id?'.(int) $item->id); ?>">
                            <?php echo $this->escape($item->coursename); ?></a>
                         <?php else : ?>
                            <?php echo $this->escape($item->coursename); ?>
                         <?php endif; ?>
                         <p class="smallsub">
                         <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
                     </td>
                     <td class="center">
                         <?php echo JHtml::_('jgrid.published', $item->published, $i, 'courses.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                     </td>
                     <td class="center">
                         <?php echo $this->escape($item->category_title); ?>
                     </td>
                     <td class="center">
                         <?php echo $this->escape($item->group_title); ?>
                     </td>
                     <td class="center">
                         <?php echo $this->escape($item->duration); ?>
                     </td>
                     <td class="center">
                         <?php echo $this->escape($item->access); ?>
                     </td>
                     <td class="center">
                         <?php echo (int) $item->id; ?>
                     </td>
                 </tr>
                 
                 <?php endforeach; ?>
             </tbody>
         </table>
         <div>
             <input type="hidden" name="task" value="" />
             <input type="hidden" name="boxchecked" value="0" />
             <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
             <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
             <?php echo JHtml::_('form.token'); ?>
         </div>
    </form>
</div>