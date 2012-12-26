<?php
/**
 * @version		$Id: subscription.php 290 2011-11-12 03:43:07Z dextercowley $
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport('joomla.user.helper');

class JoomproSubsControllerSubscription extends JControllerForm
{

	protected $view_item = 'form';

	/**
	 * Method to edit an existing record.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	Boolean	True if access level check and checkout passes, false otherwise.
	 */
	public function edit($key = null, $urlVar = 'sub_id')
	{
		$result = false;
		$itemid = JRequest::getInt('Itemid');
		$catid = JRequest::getInt('catid');

                if (($catid) && ($this->allowEdit($catid))) {
			$result = parent::edit($key, $urlVar);
			
			// Check in the subscription, since it was checked out in the edit method
			$this->getModel()->checkIn(JRequest::getInt($urlVar));
		}		
		return $result;
	}

	/**
	 * Method to check if you can edit a subscription.
	 * We check the category level because this is the lowest ACL level.
	 *
	 * @param   integer	$catid	Category id
	 * @return  boolean	true if allowed to edit, false otherwise		
	 */
	protected function allowEdit($catid)
	{
		return JFactory::getUser()->authorise('core.edit', $this->option.'.category.'.$catid);
	}
	
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 * @param	array	$config	Configuration array for model. Optional.
	 *
	 * @return	object	The model.
	 */
	
        
        public function getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = null)
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$itemId	= JRequest::getInt('Itemid');
		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}
		return $append;
	}
	
	/**
	 * Subscribe to a subscription.
	 * 
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key
	 *
	 * @return	string	The return URL.
	 */
	public function subscribe ($key = null, $urlVar = 'sub_id')
	{
                // Check that user is authorized
		$user = JFactory::getUser();
		if (!$user->authorise('core.edit', 'com_joomprosubs.category.' . $this->category->id)) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		
		// Check that form data is valid
		if(!$this->validate()) {
			return false;
		}
		
		// Add user to group if not already a member
		$model = $this->getModel();
		$id = JRequest::getInt('sub_id');
		$subscription = $model->getItem($id);

     
                
		// Set redirect without id in case of an error
		//$this->setRedirect(JRoute::_('index.php?option=com_joomprosubs&view=form&layout=thankyou', false));
		if (!in_array($subscription->group_id, $user->groups)) {
			if (!JUserHelper::addUserToGroup($user->id, $subscription->group_id)) {
				$this->setMessage($model->getError(), 'error');
				return false;
			}
		}
		
		// Add or update row to mapping table

                if (!$result = $model->updateSubscriptionMapping($subscription, $user)) {
			$this->setMessage($model->getError(), 'error');
			return false;
		}
		
		// At this point, we have succeeded
		// Trigger the onAfterSubscribe event
		JDispatcher::getInstance()->trigger('onAfterSubscribe', array(&$subscription));
		
                           // Send notification mail
                
                $mail = $user->email;
                $sendok = $this->sendNotification(JText::_('COM_JOOMPRO_MAIL_SUB_BODY'), JText::_('COM_JOOMPRO_MAIL_SUB_SUBJECT'), $mail);

                
		// Include id in redirect for success message
		$this->setRedirect(JRoute::_('index.php?option=com_joomprosubs&view=form&layout=thankyou&sub_id='.$id, false));
		return true;				
	}
        
        public function unsubscribe ($key = null, $urlVar = 'sub_id') 
	{

                // Check that user is authorized
		$user = JFactory::getUser();
		if (!$user->authorise('core.edit', 'com_joomprosubs.category.' . $this->category->id)) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		
		// Check that form data is valid
		if(!$this->validate()) {
			return false;
		}

		
		// Add user to group if not already a member
		$model = $this->getModel();
		$id = JRequest::getInt('sub_id');
		$subscription = $model->getItem($id);
                
		// Set redirect without id in case of an error
		$this->setRedirect(JRoute::_('index.php?option=com_joomprosubs&view=form&layout=thankyou', false));
		if (!in_array($subscription->group_id, $user->groups)) {
			if (!JUserHelper::addUserToGroup($user->id, $subscription->group_id)) {
				$this->setMessage($model->getError(), 'error');
				return false;
			}
		}

		// delete row from mapping table
		if (!$result = $model->deleteMapRow($subscription->id, $user->id)) {
			$this->setMessage($model->getError(), 'error');
			return false;
		}
		
		// At this point, we have succeeded
		// Trigger the onAfterSubscribe event
		// JDispatcher::getInstance()->trigger('onAfterSubscribe', array(&$subscription));

                $mail = $user->email;
                $sendok = $this->sendNotification(JText::_('COM_JOOMPRO_MAIL_UNSUB_BODY'), JText::_('COM_JOOMPRO_MAIL_UNSUB_SUBJECT'), $mail);

		// Include id in redirect for success message
		$this->setRedirect(JRoute::_('index.php?option=com_joomprosubs&view=form&layout=thankyou&sub_id='.$id, false));

                return true;				
	}
	
	/**
	 * Validate the data
	 *
	 * @return	boolean	true if data is valid, false otherwise
	 */
	protected function validate() 
	{
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$form = $model->getForm($data, false);		
		$validData = $model->validate($form, $data);
		$recordId = JRequest::getInt('sub_id');
		
		// Check for validation errors.
		if ($validData === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			if (isset($data[0])) {
				$app->setUserState($context.'.data', $data);
			}

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, 'sub_id'), false));
			return false;
		}		
		return true;
	}
        

        
        public function sendNotification($body, $subject, $recipients)
        {
            echo "Body: $body <br/>";
            echo "Subject: $subject <br />";
            echo "Emfp: $receiptents <br />";
            
            $config =JFactory::getConfig();
            
            $sender = array(
                $config->getValue( 'config.mailfrom' ),
                $config->getValue( 'config.fromname' ));

            echo $config->getValue( 'config.fromname' ) . "<br />";
            
            $mailer = JFactory::getMailer();
            $mailer->setSender($sender);
     
            $mailer->setsubject($subject);
            $mailer->setbody($body);
            $mailer->addRecipient($recipients);
            
            $send = $mailer->Send();
            return $send;
            
        }
	
} // end of class
