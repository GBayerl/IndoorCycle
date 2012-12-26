<?php
defined ('_JEXEC') or die;
jimport ('joomla.application.component.modellist');

class JoomprosubsModelSubscriber extends JModelList
{
    public function getListQuery($id=null)
    {
        //echo "start here";
        $app = JFactory::getApplication();
        //$requested_id = $app->get('input')->get('id', 0, 'int');
        $requested_id = JRequest::getInt('sub_id');

        if ($requested_id > 0)
        {
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->from('#__joompro_sub_mapping as b');
            $query->select('b.subscription_id as courseid, a.id as userid, a.name as username, c.title as coursename');
            $query->leftjoin('#__users as a ON a.id = b.user_id');
            $query->leftjoin('#__joompro_subscriptions as c ON c.id = b.subscription_id');
            $query->where('b.subscription_id = \'' . $requested_id . '\'');
            return $query;
            
         }

        
    }
}