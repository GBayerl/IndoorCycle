<?php
/**
 *
 * @package Joomla.Administrator
 * @subpackage com_courses
 * @copyright (c) 2012, Guenther Bayerl. All rights reserved
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * 
 * Methods supporting a list of courses recrods
 * 
 */

class CoursesModelCourses extends JModelList
{
    /**
     * 
     * @param array An optional associative array of configuration settings.
     * @see JController
     * @since 2.5
     */
    
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'course_id', 'a.course_id',
                'coursename', 'a.coursename',
                'description', 'a.description',
                'catid', 'a.catid',
                'trainer', 'a.trainer',
                'coursedate', 'a.coursedate',
                'coursetime', 'a.coursetime',
                'duration', 'a.duration',
                'bikes', 'a.bikes',
                'created_by', 'a.createdbyid',
                'lastmodified', 'a.lastmodified',
                'checked_out' , 'a.checked_out',
                'group_id', 'a.group_id'
                );
        }
        
        parent::__construct($config);
    }
    
    /**
     * 
     * Method to auto-populate the model state.
     * 
     * Note: Calling getState in this method will result in recursion.
     * 
     */
    
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables
        
        $app = JFactory::getApplication('administrator');
        
        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        
        $accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
        $this->setState('filter.access', $accessId);
        
        $published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        
        $categoryId = $this->getUserStateFromRequest($this->context.'filter.category_id', 'filter_category_id', '');
        $this->setState('filter.category_id', $categoryId);
        
        //Load parameters
        $params = JComponentHelper::getParams('com_courses');
        $this->setState('params', $params);
        
        // List state information
        parent::populateState('a.coursename', 'asc');
    }
    
    /**
     * 
     * Method to get a store id base on model configuration state.
     * 
     * This is necessary because the model is used by the component and
     * ordering requirements.
     * 
     * @param string $id A prefix for the store id.
     * @return string A store id.
     * 
     */
    
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id.=':' . $this->getState('filter.search');
        $id.=':' . $this->getState('filter.access');
        $id.=':' . $this->getState('filter.state');
        $id.=":" . $this->getState('filter.category_id');
        
        return parent::getStoreId($id);
    }
    
    /**
     * Bild a SQL quer to load the list data.
     * 
     * @return JDatabaseQuery
     * 
     */
    
    protected function getListQuery()
    {
        echo "in the model";
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);
        
        // Select the requred fields from the table.
        $query->select('a.*');
        $query->from($db->quoteName('#__courses').' AS a');
        
        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', $db->quoteName('#__users').' AS uc ON uc.id=a.checked_out');
        
        // Join over the user groups to get the group name
        $query->select('g.title as group_title');
        $query->join('LEFT', $db->quoteName('#__usergroups').' AS g ON a.group_id = g.id');
        
        // Join over the categories'.
        $query->select('c.title AS category_title');
        $query->join('LEFT', $db->quoteName('#__categories').' AS c ON c.id = a.catid');
        
        // Filter by access level
        if ($access = $this->getState('filter.access'))
        {
            $query->where('a.access='.(int) $access);
        }
        
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published))
        {
            $query->where('a.published = '.(int) $published);
        } else if ($published ==='')
        {
            $query->where('a.published IN (0, 1)');
        }
        
        // Filter by category.
        $categoryId = $this->getState('filter.category_id');
        if (is_numeric($categoryId))
        {
            $query->where('a.catid = '.(int) $categoryId);
        }
        
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('a.course_id = '.(int) substr($search, 3));
            } else
            {
                $search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                $query->where('(a.coursename LIKE '.$search.' OR a.alias LIKE '.$search.')');
            }
        }
        
        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        $query->order($db->getEscaped($orderCol.' '.$orderDirn));
                
        return $query;
    }
}