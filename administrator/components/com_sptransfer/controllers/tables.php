<?php

/**
 * @package		SP Paypal
 * @subpackage	Components
 * @copyright	SP CYEND - All rights reserved.
 * @author		SP CYEND
 * @link		http://www.cyend.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * SPTransfers Controller
 */
class SPTransferControllerTables extends JControllerAdmin
{
    public function getModel($name = 'Tables', $prefix = 'SPTransferModel') {
            $model = parent::getModel($name, $prefix, array('ignore_request' => true));
            return $model;
    }
    function fix() {
        // Check for request forgeries
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        //Validate Input IDs
        $input_ids = JRequest::getVar('input_ids', array(), '', 'array');       
        $input_ids = $this->validateInputIDs($input_ids);
        if(!$input_ids) {
            $this->setRedirect('index.php?option=com_sptransfer',  JText::_('COM_SPTRANSFER_MSG_ERROR_INVALID_IDS'),'error');
            return false;
        }  

        //Initial tasks
        //Disable warnings
        error_reporting(E_ERROR | E_PARSE);
        set_time_limit(0);
        
        //monitor log
        //$message= '<META HTTP-EQUIV="REFRESH" CONTENT="15">';
        $message= '';
        $this->writeLog($message, 'w'); // create monitor log
        $message= ('<h1>'.JText::_(COM_SPTRANSFER_START).'</h1>');
        $this->writeLog($message); 
        
        //check for MySQLi
        //if (!$this->checkDbType(JPATH_SITE.'/configuration.php')) return false;

        // Connect to source db
        $params = JComponentHelper::getParams('com_sptransfer');        
        if (!($source_db = $this->connect($params))) return false;
        
        // Main Loop within extensions
        //Get ids
        $ids	= JRequest::getVar('cid', array(), '', 'array');        
        
        // Get the model.
        $model = $this->getModel();

        //Main Loop
        foreach ($ids as $i => $id)
        {
            if (!($item = $model->getItem($id))) JError::raiseWarning(500, $model->getError());            
            $modelContent = $this->getModel($item->extension_name);
            $modelContent->init($source_db, $item);
            $modelContent->{$item->name.'_fix'}($input_ids[$id-1]);                
        }

        // Finish
        //enable warnings
        error_reporting(E_ALL);
        $source_db->close();
        set_time_limit(30);
        $message= ('<h1>'.JText::_("COM_SPTRANSFER_COMPLETED").'</h1>');
        $this->writeLog($message); 
        $this->setRedirect('index.php?option=com_sptransfer',  JText::_("COM_SPTRANSFER_COMPLETED"));
    }
    function transfer() {
        // Check for request forgeries
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        //Validate Input IDs
        $input_ids = JRequest::getVar('input_ids', array(), '', 'array');       
        $input_ids = $this->validateInputIDs($input_ids);
        if(!$input_ids) {
            $this->setRedirect('index.php?option=com_sptransfer',  JText::_('COM_SPTRANSFER_MSG_ERROR_INVALID_IDS'),'error');
            return false;
        }  

        //Initial tasks
        //Disable warnings
        error_reporting(E_ERROR | E_PARSE);
        set_time_limit(0);
        
        //monitor log
        //$message= '<META HTTP-EQUIV="REFRESH" CONTENT="15">';
        $message= '';
        $this->writeLog($message, 'w'); // create monitor log
        $message= ('<h1>'.JText::_(COM_SPTRANSFER_START).'</h1>');
        $this->writeLog($message); 
        
        // Open monitor log
        /*
        echo '<script type="text/javascript">'
            , "var myDomain = location.protocol + '//' +
                location.hostname +
                location.pathname.substring(0, location.pathname.lastIndexOf('/')) +
                '/components/com_sptransfer/log.htm';                    
                  window.open(myDomain,'SP Upgrade','width=640,height=480, scrollbars=1');
                  return true; "
           , '</script>';
        */
        
        //check for MySQLi
        //if (!$this->checkDbType(JPATH_SITE.'/configuration.php')) return false;

        // Connect to source db
        $params = JComponentHelper::getParams('com_sptransfer');        
        if (!($source_db = $this->connect($params))) return false;
        
        // Main Loop within extensions
        //Get ids
        $ids	= JRequest::getVar('cid', array(), '', 'array');        
        
        // Get the model.
        $model = $this->getModel();

        //Main Loop
        foreach ($ids as $i => $id)
        {
            if (!($item = $model->getItem($id))) JError::raiseWarning(500, $model->getError());            
            $modelContent = $this->getModel($item->extension_name);
            $modelContent->init($source_db, $item);
            echo $modelContent->{$item->name}($input_ids[$id-1]);                
        }

        // Finish
        //enable warnings
        error_reporting(E_ALL);
        $source_db->close();
        set_time_limit(30);
        $message= ('<h1>'.JText::_(COM_SPTRANSFER_COMPLETED).'</h1>');
        $this->writeLog($message); 
        $this->setRedirect('index.php?option=com_sptransfer',  JText::_("COM_SPTRANSFER_COMPLETED"));
    }
    function connect($params) {
        $option = array(); //prevent problems 
        $option['driver']   = $params->get("driver", 'mysqli');            // Database driver name
        $option['host']     = $params->get("host", 'localhost');    // Database host name
        $option['user']     = $params->get("source_user_name", '');       // User for database authentication
        $option['password'] = $params->get("source_password", '');   // Password for database authentication
        $option['database'] = $params->get("source_database_name", '');      // Database name
        $option['prefix']   = $this->modPrefix($params->get("source_db_prefix", ''));             // Database prefix (may be empty)

        $source_db = & JDatabase::getInstance( $option );

        $jAp = & JFactory::getApplication();
        
        // Test connection
        $query = "SELECT id from #__categories WHERE id = 0";
        $source_db->setQuery($query);
        $result = $source_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_DB', $source_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_DB', $source_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            $this->setRedirect('index.php?option=com_sptransfer');
            return false;
        }
        

        //change character set
        $query    = "SHOW VARIABLES LIKE 'character_set_database'";
        $source_db->setQuery($query);
        $result = $source_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_DB', $source_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_DB', $source_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            $this->setRedirect('index.php?option=com_sptransfer');
            return false;
        }
        $row = mysqli_fetch_assoc($result);
        //mysqli_set_charset($row['Value'], $source_db);
        mysqli_set_charset($source_db, 'utf8');
        
        return $source_db;
    }
    function writeLog($message, $mode = 'a')  {
        $fileName = JPATH_COMPONENT_ADMINISTRATOR.'/log.htm';
        $handle = fopen($fileName, $mode);
        if ($handle) {
            fwrite($handle,$message);
            fflush($handle);
            fclose($handle);
        }
        return true;
    }
    function checkDbType($fileConfiguration) {
        $jAp = & JFactory::getApplication();
        $pathValidation = TRUE;
        if (!file_exists($fileConfiguration)) {
            $pathValidation = FALSE;
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_INVALIDPATH3', $fileConfiguration), 'error');
            $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_INVALIDPATH3', $fileConfiguration).'</p>';
            $this->writeLog($message);         
            return false;
        }

        if ($pathValidation) {
            $mBool = FALSE;
            $handleConfiguration = @fopen($fileConfiguration, "r");
            while (($buffer = fgets($handleConfiguration, 4096)) !== false) {
                if (strpos($buffer, "mysqli") > 0 ) $mBool = TRUE;
            }
            fclose($handleConfiguration);

            if (!$mBool) {
                $jAp->enqueueMessage(JText::_('COM_SPTRANSFER_MSG_MYSQLI'), 'error');
                $message = '<p><b><font color="red">'.JText::_('COM_SPTRANSFER_MSG_MYSQLI').'</font></b></p>';
                $this->writeLog($message);
                return false;
            }            
        }
        
        return true;
    }       
    function validateInputIDs($input_ids) {
        $return = Array();
        foreach ($input_ids as $i => $ids) {
            if ($ids!="") {
                $ranges = explode(",", $ids);
                foreach ($ranges as $j => $range) {
                    if (preg_match("/^[0-9]*$/", $range))  {
                        $return[$i][] = $range;
                    } else {
                        if (preg_match("/^[0-9]*-[0-9]*$/", $range)) {
                            $nums = explode("-", $range);
                            if ($nums[0] >= $nums[1]) return false;
                            for ($k = $nums[0]; $k <= $nums[1]; $k++) {
                                $return[$i][]=$k;
                            }
                        } else {
                            return false;
                        }
                    }
                }                
            }
        }    
        if (count($return) == 0) {
            return true;
        } else {
            return $return;
        }        
    }
    function modPrefix($prefix) { //Add underscore if not their
            if (!strpos($prefix, '_')) $prefix = $prefix.'_';
            return $prefix;
    }
}
