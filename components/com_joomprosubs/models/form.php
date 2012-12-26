<?php
/**
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JLoader::register('JoomproSubsModelSubscription', 
	JPATH_COMPONENT_ADMINISTRATOR.'/models/subscription.php');

/**
 * Joomprosubs model.
 *
 * @package		Joomla.Site
 * @subpackage	com_joomprosubs
 */
class JoomproSubsModelForm extends JoomproSubsModelSubscription
{

	
	/**
	 * 
	 * Method to add or update the subscription mapping table
	 * If the row already exists, update the start and end date.
	 * If the row doesn't exist, add a new row.
	 * 
	 * @param JObject $subscription	Subscription object 
	 * @param JUser $user User object
	 * @return	Boolean	true on success, false on failure
	 */
	public function updateSubscriptionMapping($subscription, $user)
	{
                $session = JFactory::getSession();
            
		// Check that we have valid inputs
		if (((int) $subscription->id) && ((int) $subscription->duration )
				&& ((int) $user->id)) {
			
			//$today = JFactory::getDate()->toMySQL();
			//$endDate = JFactory::getDate('+ ' . (int) $subscription->duration . ' days')->toMySQL();
			
			// Check whether the row exists 
			$mapRow = $this->getMapRow($subscription->id, $user->id);
                        
                        if ($mapRow === false) {
				// We have a database error
				return false;
			} else if ($mapRow) {
                            
                            $session->set('course_' . $subscription->id, 'true');

				// The row already exists, so return error
				 if (!$this->existsRow($subscription->id, $user->id)) {
					//echo "Bereits angemeldet <br />";
                                        return false;
				}
			} else {
				// The row doesn't exist, so add a new  map row                           
                            $session->clear('course_' . $subscription->id);
				if (!$this->addMapRow($subscription->id, $user->id, $today, $endDate)) {
					return false;
				}
			}
			
			// At this point, we have successfully updated the database
			return true;			
		}
	}	
	
	protected function addMapRow ($subID, $userID) 
	{
		$session = JFactory::getSession();
                $db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->insert($db->nameQuote('#__joompro_sub_mapping'));
		$query->set('subscription_id = ' . (int) $subID);
		$query->set('user_id = ' . (int) $userID);
		$db->setQuery($query);
		if ($db->query()) {
		$session->set('course_' . $subID, 'true');	
                return true;
		} else 
                {
                    $this->setError(JText::_('COM_JOOMPROSUBS_ADD_MAP_ROW_FAIL'));
                    return false;
		}
	}
	
	public function getMapRow($subID, $userID) 
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('subscription_id, user_id, start_date, end_date');
		$query->from($db->nameQuote('#__joompro_sub_mapping'));
		$query->where('subscription_id = ' . (int) $subID);
		$query->where('user_id = ' . (int) $userID);
		$db->setQuery($query);
		$data = $db->loadObject();
		if ($db->getErrorNum()) {
			$this->setError(JText::_('COM_JOOMPROSUBS_GET_MAP_ROW_FAIL'));
			return false;
		} else 
                {
                    return $data;
		}
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		// Load state from the request.
		$pk = JRequest::getInt('sub_id');
		$this->setState('joomprosub.sub_id', $pk);
		// Add compatibility variable for default naming conventions.
		$this->setState('form.id', $pk);

		$return = JRequest::getVar('return', null, 'default', 'base64');

		if (!JUri::isInternal(base64_decode($return))) {
			$return = null;
		}

		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);
		$this->setState('layout', JRequest::getCmd('layout'));
	}

        
        protected function existsRow($subID, $userID)
        {
            /**
           	$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('subscription_id, user_id, start_date, end_date');
		$query->from($db->nameQuote('#__joompro_sub_mapping'));
		$query->where('subscription_id = ' . (int) $subID);
		$query->where('user_id = ' . (int) $userID);
		$db->setQuery($query);
		$data = $db->loadObject();
               
            
            $this->setError(JText::_('COM_JOOMPROSUBS_ROW_ALREADY_EXISTS'));
            **/
            return true;
        }
        
        public function deleteMapRow($subID, $userID)
        {
            echo "deleteion of ". $subID. " and ". $userID;
            $session = JFactory::getSession();
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->delete();
            $query->from($db->nameQuote('#__joompro_sub_mapping'));
            $query->where('subscription_id = ' . (int) $subID);
            $query->where('user_id = ' . (int) $userID);
            echo $query->dump();
            $db->setQuery($query);
            
            
            if ($db->query())
            {
                $session->clear('course_' . $subID);
                return true;
            } else {
                $this->setError(JText::_('COM_JOOMPROSUBS_DELETE_ROW_FAIL'));
                return false;
            }
        }
}
