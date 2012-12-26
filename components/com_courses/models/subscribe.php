<?php
defined ('_JEXEC') or die;
jimport ('joomla.application.component.model');

class CoursesModelSubscribe extends JModel
{

    public function getItem($id=null)
    {       
        $userid         =   JFactory::getuser()->id;
        $username       =   JFactory::getuser()->name;
        $courseid       =   JRequest::getInt('course');
        $contactnumber  =   JRequest::getInt('contact');
        
        echo "<h1>Daten schreiben</h1>";
    
    
        echo "<h3>Benutzer: " . $userid . "<br />Kurs: " . $courseid . "<br />Kontaktnummer: " . $contactnumber . "</h3><br />";
        $result = true;

        if ($courseid > 0 AND !$userid = 0)
        {
            echo "Daten jetzt in die Datenbank schreiben! <br />";
            
            /*
             * $db = $this->getDbo();
             
             *$query = $db->getQuery(true);
             *$query->insert('#__courses_map');
             *$query->set('course_id=' . $courseid . ', user_id=' . $userid . ', contactnumber='. $contactnumber);
             *$db->setQuery($query);
             *$result =$db->loadObject();
             * 
             */
            
        }
        return $result; 
        $this->redirect();
    }
    
    public function updateSubscriptionMapping($subscription, $user)
    {
        //
    }
    
    
    
    protected function validate()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel();
        $data = JRequest::getVar('jform',array(), 'post', 'array');
        
        $form = $model->getForm($data, false);
        $reordId = JRequest::getInt('course');
        
        // Check for validation errors.
        
        if ($validDate === false)
        {
            // Get the validation messages
            $errors = $model->getErrors();
            
            //Push up to three validation messages out to the user
            
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if (JError::isError($errors[§i]))
                {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }
            
            // Save the data to the session
            if (isset($data[0]))
            {
                $app->setUserState($context.'.data', $data);
            }
            
            // Redirect back to the screen.
            
            $this->setRediret(JRoute::_('index.php?option='.$this->option.'&view'.$this->view_item. $this->getRedirectToItemAppend($recordID, 'course_id'), false));
            return false;
        }
        return true;
        
    } // end of classe
}