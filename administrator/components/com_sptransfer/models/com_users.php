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

// import the Joomla modellist library
jimport('joomla.application.component.model');

class SPTransferModelCom_Users extends JModel
{
    protected $jAp;   
    protected $tableLog;
    protected $destination_db;
    protected $destination_query;
    protected $source_db;
    protected $source_query;
    protected $user;
    protected $params;
    protected $task;
    
    public function init($source_db, $task) {
        $this->writeLog($message);           
        $this->jAp = & JFactory::getApplication(); 
        $this->tableLog = $this->getTable('Log', 'SPTransferTable');        
        $this->destination_db = $this->getDbo();
        $this->destination_query = $this->destination_db->getQuery(true);
        $this->source_db = $source_db;
        $this->source_query = $source_db->getQuery(true);     
        $this->user = JFactory::getUser(0);
        $this->params = JComponentHelper::getParams('com_sptransfer');
        $this->task = $task;
    }
    protected function writeLog($message, $mode = 'a')  {
        $fileName = JPATH_COMPONENT_ADMINISTRATOR.'/log.htm';
        $handle = fopen($fileName, $mode);
        if ($handle) {
            fwrite($handle,$message);
            fflush($handle);
            fclose($handle);
        }
        return true;
    }
    public function getTable($type = 'Tables', $prefix = 'JTable', $config = array())  {
            return JTable::getInstance($type, $prefix, $config);
    }
    public function usergroups($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_table = $this->getTable('UserGroup', 'JTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;
    
        $message= ('<h2>'.JText::_(COM_USERS).' - '.JText::_(COM_USERS_USERGROUPS).'</h2>');
        $this->writeLog($message); 

        // Load items
        $query = 'SELECT source_id
            FROM #__sptransfer_log
            WHERE tables_id = '. (int) $task->id.' AND state >= 2
            ORDER BY id ASC';        
        $destination_db->setQuery($query);
        $result = $destination_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $destination_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->$destination_db()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }        
        $temp = $destination_db->loadResultArray();
            
        $query = 'SELECT * 
            FROM #__usergroups
            WHERE parent_id > 0';        
        if (!is_null($temp[0])) $query .= ' AND id NOT IN ('. implode(',', $temp) .')';
        if (!is_null($ids[0])) $query .= ' AND id IN ('. implode(',', $ids) .')';
        $query .= ' ORDER BY id ASC';
        $source_db->setQuery($query);
        $result = $source_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }
        $items = $source_db->loadAssocList();
        
        //percentage
        $percTotal = count($items);
        if ( $percTotal < 100 ) $percKlasma = 0.1;
        if ( $percTotal > 100 && $percTotal < 2000 ) $percKlasma = 0.05;
        if ( $percTotal > 2000 ) $percKlasma = 0.01;
        $percTen = $percKlasma * $percTotal;
        $percCounter = 0;
        if ($percTotal == 0) {
            $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_TRANSFER).'</p>';
            $this->writeLog($message);
        }
                
        // Loop to save items
        foreach ($items as $i => $item) {
           
            //percentage
            $percCounter += 1;
            if (@($percCounter % $percTen) == 0) {
                $perc = round(( 100 * $percCounter ) / $percTotal);
                $message = $perc.'% '.JText::_('COM_SPTRANSFER_MSG_PROCESSED').'<br/>';  
                $this->writeLog($message);
            }
            
             //log            
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "source_id" => $item['id']));            
            $tableLog->created = null;
            $tableLog->note="";
            $tableLog->source_id = $item['id'];
            $tableLog->destination_id = $item['id'];
            $tableLog->state = 1;
            $tableLog->tables_id = $task->id;            
            
            // Create record
            $destination_db->setQuery(
                "INSERT INTO #__usergroups" .
                " (id)".
                " VALUES (".$destination_db->quote($item['id']).")"
                );
            if(!$destination_db->query()) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                        "INSERT INTO #__usergroups" .
                        " (title)".
                        " VALUES (".$destination_db->quote('sp_transfer').")"
                        );
                    if(!$destination_db->query()) {
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                        $this->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                    $destination_db->setQuery(
                        "SELECT id FROM #__usergroups" .
                        " WHERE title LIKE ".$destination_db->quote('sp_transfer')
                        );
                    $destination_db->query();
                    $tableLog->destination_id = $destination_db->loadResult();                    
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_NEW_IDS', $item['id'], $tableLog->destination_id ).'</p>';
                    $item['id'] = $tableLog->destination_id;
                    $this->writeLog($message);                    
                    $tableLog->note = $message;                    
                } elseif ($params->get("new_ids", 0) == 0) {
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }                
            }             
            
            // Reset
            $destination_table->reset();
            
            //Replace existing item
            if ($params->get("new_ids", 0) == 2) $destination_table->load($item['id']);
            
            // Bind
            if (!$destination_table->bind($item)) {
                // delete record
                $destination_db->setQuery(
                    "DELETE FROM #__usergroups" .
                    " WHERE id = ".$destination_db->quote($item['id'])
                    );
                if(!$destination_table->query()) {
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                    $this->writeLog($message);
                }
                $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_BIND', $item['id'], $destination_table->getError() ).'</p>';
                $this->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }
            
            // Store
            if (!$destination_table->store()) {                
                if ($params->get("duplicate_alias", 0)) {
                    $destination_table->title .= '-sp-'.rand();                    
                    if (!$destination_table->store()) {
                        // delete record
                        $destination_db->setQuery(
                            "DELETE FROM #__usergroups" .
                            " WHERE id = ".$destination_db->quote($item['id'])
                            );
                        if(!$destination_db->query()) {
                            $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                            $this->writeLog($message);
                        }
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_STORE', $item['id'], $destination_table->getError() ).'</p>';
                        $this->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_DUPLICATE_ALIAS', $item['id'], $destination_table->title ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                } else {
                    // delete record
                    $destination_db->setQuery(
                        "DELETE FROM #__usergroups" .
                        " WHERE id = ".$destination_db->quote($item['id'])
                        );
                    if(!$destination_db->query()) {
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                        $this->writeLog($message);
                    }
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_STORE', $item['id'], $destination_table->getError() ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }                
            }
            
            //Log
            $tableLog->state = 2;
            $tableLog->store();
        } //Main loop end
        
    }
    public function viewlevels($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_table = $this->getTable('ViewLevel', 'JTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;

        $message= ('<h2>'.JText::_(COM_USERS).' - '.JText::_(COM_USERS_VIEWLEVELS).'</h2>');
        $this->writeLog($message); 

        // Load items
        $query = 'SELECT source_id
            FROM #__sptransfer_log
            WHERE tables_id = '. (int) $task->id.' AND state >= 2
            ORDER BY id ASC';        
        $destination_db->setQuery($query);
        $result = $destination_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $destination_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->$destination_db()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }        
        $temp = $destination_db->loadResultArray();
             
        $query = 'SELECT * 
            FROM #__viewlevels
            WHERE id > 0';        
        if (!is_null($temp[0])) $query .= ' AND id NOT IN ('. implode(',', $temp) .')';
        if (!is_null($ids[0])) $query .= ' AND id IN ('. implode(',', $ids) .')';
        $query .= ' ORDER BY id ASC';
        $source_db->setQuery($query);
        $result = $source_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }
        $items = $source_db->loadAssocList();
        
        //percentage
        $percTotal = count($items);
        if ( $percTotal < 100 ) $percKlasma = 0.1;
        if ( $percTotal > 100 && $percTotal < 2000 ) $percKlasma = 0.05;
        if ( $percTotal > 2000 ) $percKlasma = 0.01;
        $percTen = $percKlasma * $percTotal;
        $percCounter = 0;
        if ($percTotal == 0) {
            $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_TRANSFER).'</p>';
            $this->writeLog($message);
        }
                
        // Loop to save items
        foreach ($items as $i => $item) {
           
            //percentage
            $percCounter += 1;
            if (@($percCounter % $percTen) == 0) {
                $perc = round(( 100 * $percCounter ) / $percTotal);
                $message = $perc.'% '.JText::_('COM_SPTRANSFER_MSG_PROCESSED').'<br/>';  
                $this->writeLog($message);
            }
            
             //log            
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "source_id" => $item['id']));            
            $tableLog->created = null;
            $tableLog->note="";
            $tableLog->source_id = $item['id'];
            $tableLog->destination_id = $item['id'];
            $tableLog->state = 1;
            $tableLog->tables_id = $task->id;            
            
            // Create record
            $destination_db->setQuery(
                "INSERT INTO #__viewlevels" .
                " (id)".
                " VALUES (".$destination_db->quote($item['id']).")"
                );
            if(!$destination_db->query()) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                        "INSERT INTO #__viewlevels" .
                        " (title)".
                        " VALUES (".$destination_db->quote('sp_transfer').")"
                        );
                    if(!$destination_db->query()) {
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                        $this->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                    $destination_db->setQuery(
                        "SELECT id FROM #__viewlevels" .
                        " WHERE title LIKE ".$destination_db->quote('sp_transfer')
                        );
                    $destination_db->query();
                    $tableLog->destination_id = $destination_db->loadResult();                    
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_NEW_IDS', $item['id'], $tableLog->destination_id ).'</p>';
                    $item['id'] = $tableLog->destination_id;
                    $this->writeLog($message);                    
                    $tableLog->note = $message;                    
                } elseif ($params->get("new_ids", 0) == 0) {
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }                
            } 
            
            // Reset
            $destination_table->reset();
            
            //Replace existing item
            if ($params->get("new_ids", 0) == 2) $destination_table->load($item['id']);
            
            // Bind
            if (!$destination_table->bind($item)) {
                // delete record
                $destination_db->setQuery(
                    "DELETE FROM #__viewlevels" .
                    " WHERE id = ".$destination_db->quote($item['id'])
                    );
                if(!$destination_table->query()) {
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                    $this->writeLog($message);
                }
                $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_BIND', $item['id'], $destination_table->getError() ).'</p>';
                $this->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }
            
            // Store
            if (!$destination_table->store()) {
                if ($params->get("duplicate_alias", 0)) {
                    $destination_table->title .= '-sp-'.rand();                    
                    if (!$destination_table->store()) {
                        // delete record
                        $destination_db->setQuery(
                            "DELETE FROM #__viewlevels" .
                            " WHERE id = ".$destination_db->quote($item['id'])
                            );
                        if(!$destination_db->query()) {
                            $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                            $this->writeLog($message);
                        }
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_STORE', $item['id'], $destination_table->getError() ).'</p>';
                        $this->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_DUPLICATE_ALIAS', $item['id'], $destination_table->title ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                } else {
                    // delete record
                    $destination_db->setQuery(
                        "DELETE FROM #__viewlevels" .
                        " WHERE id = ".$destination_db->quote($item['id'])
                        );
                    if(!$destination_db->query()) {
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                        $this->writeLog($message);
                    }
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_STORE', $item['id'], $destination_table->getError() ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }                
            }
            
            //Reorder
            $destination_table->reorder();
            
            //Log
            $tableLog->state = 2;
            $tableLog->store();
        } //Main loop end
        
    }
    public function users($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_table = $this->getTable('User', 'JTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;

        $message= ('<h2>'.JText::_(COM_USERS).' - '.JText::_(COM_USERS_USERS).'</h2>');
        $this->writeLog($message); 
        
        // Load items
        $query = 'SELECT source_id
            FROM #__sptransfer_log
            WHERE tables_id = '. (int) $task->id.' AND state >= 2
            ORDER BY id ASC';        
        $destination_db->setQuery($query);
        $result = $destination_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $destination_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->$destination_db()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }        
        $temp = $destination_db->loadResultArray();
              
        $query = 'SELECT * 
            FROM #__users
            WHERE id > 0';        
        if (!is_null($temp[0])) $query .= ' AND id NOT IN ('. implode(',', $temp) .')';
        if (!is_null($ids[0])) $query .= ' AND id IN ('. implode(',', $ids) .')';
        $query .= ' ORDER BY id ASC';
        $source_db->setQuery($query);
        $result = $source_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }
        $items = $source_db->loadAssocList();

        //percentage
        $percTotal = count($items);
        if ( $percTotal < 100 ) $percKlasma = 0.1;
        if ( $percTotal > 100 && $percTotal < 2000 ) $percKlasma = 0.05;
        if ( $percTotal > 2000 ) $percKlasma = 0.01;
        $percTen = $percKlasma * $percTotal;
        $percCounter = 0;
        if ($percTotal == 0) {
            $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_TRANSFER).'</p>';
            $this->writeLog($message);
        }
                
        // Loop to save items
        foreach ($items as $i => $item) {
           
            //percentage
            $percCounter += 1;
            if (@($percCounter % $percTen) == 0) {
                $perc = round(( 100 * $percCounter ) / $percTotal);
                $message = $perc.'% '.JText::_('COM_SPTRANSFER_MSG_PROCESSED').'<br/>';  
                $this->writeLog($message);
            }
            
             //log            
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "source_id" => $item['id']));
            $tableLog->created = null;
            $tableLog->note="";
            $tableLog->source_id = $item['id'];
            $tableLog->destination_id = $item['id'];
            $tableLog->state = 1;
            $tableLog->tables_id = $task->id;            
            
            // Create record
            $query = "INSERT INTO #__users";
            $query .= " (";
            foreach ($item as $key => $value) {
                $query .= $destination_db->nameQuote($key).",";                
            }
            $query = chop($query,",");
            $query .=")";
            $query .= " VALUES (";
            foreach ($item as $key => $value) {
                $query .= $destination_db->quote($value).",";
            }
            $query = chop($query,",");
            $query .=")";
            $destination_db->setQuery($query);
            if(!$destination_db->query()) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                        "INSERT INTO #__users" .
                        " (email)".
                        " VALUES (".$destination_db->quote('sp_transfer').")"
                        );
                    if(!$destination_db->query()) {
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                        $this->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                    $destination_db->setQuery(
                        "SELECT id FROM #__users" .
                        " WHERE email LIKE ".$destination_db->quote('sp_transfer')
                        );
                    $destination_db->query();
                    $tableLog->destination_id = $destination_db->loadResult();                    
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_NEW_IDS', $item['id'], $tableLog->destination_id ).'</p>';
                    $item['id'] = $tableLog->destination_id;
                    $this->writeLog($message);                    
                    $tableLog->note = $message;
                    $query = "UPDATE #__users";
                    $query .= " SET ";
                    foreach ($item as $key => $value) {
                        $query .= $destination_db->nameQuote($key)."=".$destination_db->quote($value).",";
                    }
                    $query = chop($query,",");
                    $query .= " WHERE `id` =".(int) $item['id'];            
                    $destination_db->setQuery($query);
                    if(!$destination_db->query()) {
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                        $this->writeLog($message);
                        $tableLog->note = $message;
                        $tableLog->store();
                        continue;
                    }
                } elseif ($params->get("new_ids", 0) == 0) {
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }                
            } 

            // check for existing username
            $query = 'SELECT id'
                    . ' FROM #__users '
                    . ' WHERE username = ' . $destination_db->Quote($item['username'])
                    . ' AND id != '. (int) $item['id'];            
            $destination_db->setQuery($query);
            $xid = intval($destination_db->loadResult());
            if ($xid && $xid != intval($item['id'])) {
                $item['username'] .= '-sp-'.rand();  
                $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_DUPLICATE_USERNAME', $item['id'], $item['username'] ).'</p>';
                $this->writeLog($message);                    
                $tableLog->note = $message;
                $query = "UPDATE #__users";
                $query .= " SET ";
                foreach ($item as $key => $value) {
                    $query .= $destination_db->nameQuote($key)."=".$destination_db->quote($value).",";
                }
                $query = chop($query,",");
                $query .= " WHERE `id` =".(int) $item['id'];            
                $destination_db->setQuery($query);
                if(!$destination_db->query()) {
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                    // delete record
                    $destination_db->setQuery(
                        "DELETE FROM #__users" .
                        " WHERE id = ".$destination_db->quote($item['id'])
                        );
                    if(!$destination_db->query()) {
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                        $this->writeLog($message);
                        continue;
                    }                    
                }
            }

            // check for existing email
            $query = 'SELECT id'
                    . ' FROM #__users '
                    . ' WHERE email = '. $destination_db->Quote($item['email'])
                    . ' AND id != '. (int) $item['id']
            ;
            $destination_db->setQuery($query);
            $xid = intval($destination_db->loadResult());
            if ($xid && $xid != intval($this->id)) {
                $item['email'] .= '-sp-'.rand();  
                $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_DUPLICATE_EMAIL', $item['id'], $item['email'] ).'</p>';
                $this->writeLog($message);                    
                $tableLog->note = $message;
                $query = "UPDATE #__users";
                $query .= " SET ";
                foreach ($item as $key => $value) {
                    $query .= $destination_db->nameQuote($key)."=".$destination_db->quote($value).",";
                }
                $query = chop($query,",");
                $query .= " WHERE `id` =".(int) $item['id'];            
                $destination_db->setQuery($query);
                if(!$destination_db->query()) {
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                    // delete record
                    $destination_db->setQuery(
                        "DELETE FROM #__users" .
                        " WHERE id = ".$destination_db->quote($item['id'])
                        );
                    if(!$destination_db->query()) {
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_DELETE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                        $this->writeLog($message);
                        continue;
                    }                    
                }
            }
            
            // User Usergroup Map
            $query = 'SELECT group_id'
                    . ' FROM #__user_usergroup_map '
                    . ' WHERE user_id = '. (int) $tableLog->source_id
            ;
            $source_db->setQuery($query);
            $source_db->query();
            $group_id = $source_db->loadResult();
            if (!is_null($group_id)) {                
                $query = "INSERT INTO #__user_usergroup_map" .
                    " (user_id,group_id)".
                    " VALUES (".$destination_db->quote($item['id']).','.$destination_db->quote($group_id).")";            
                $destination_db->setQuery($query);
                if(!$destination_db->query()) {
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                    $tableLog->store();
                    continue;
                }
            }

            //Log
            $tableLog->state = 2;
            $tableLog->store();
        } //Main loop end
    }
    public function usergroups_fix($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_table = $this->getTable('UserGroup', 'JTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;
    
        $message= ('<h2>'.JText::_(COM_USERS).' - '.JText::_(COM_USERS_USERGROUPS).'</h2>');
        $this->writeLog($message); 

        // Load items
        $query = 'SELECT destination_id
            FROM #__sptransfer_log
            WHERE tables_id = '. (int) $task->id.' AND ( state = 2 OR state = 3 )';
            if (!is_null($ids[0])) $query .= ' AND source_id IN ('. implode(',', $ids) .')';
            $query .= ' ORDER BY id ASC';        
        $destination_db->setQuery($query);
        $result = $destination_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }        
        $temp = $destination_db->loadResultArray();
        
        // Return if empty
        if (is_null($temp[0])) {
            $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_FIX).'</p>';
            $this->writeLog($message);
            return;
        }            
                
        $query = 'SELECT * 
            FROM #__usergroups
            WHERE parent_id > 0';        
        $query .= ' AND id IN ('. implode(',', $temp) .')';        
        $query .= ' ORDER BY id ASC';
        $destination_db->setQuery($query);
        $result = $destination_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $destination_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $destination_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }
        $items = $destination_db->loadAssocList();       
        
        //percentage
        $percTotal = count($items);
        if ( $percTotal < 100 ) $percKlasma = 0.1;
        if ( $percTotal > 100 && $percTotal < 2000 ) $percKlasma = 0.05;
        if ( $percTotal > 2000 ) $percKlasma = 0.01;
        $percTen = $percKlasma * $percTotal;
        $percCounter = 0;
        if ($percTotal == 0) {
            $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_TRANSFER).'</p>';
            $this->writeLog($message);
        }
                
        // Loop to save items
        foreach ($items as $i => $item) {
            //percentage
            $percCounter += 1;
            if (@($percCounter % $percTen) == 0) {
                $perc = round(( 100 * $percCounter ) / $percTotal);
                $message = $perc.'% '.JText::_('COM_SPTRANSFER_MSG_PROCESSED').'<br/>';  
                $this->writeLog($message);
            }
            
            // Set destination_id
            if ($item['parent_id'] > 1) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => $task->id, "source_id" => $item['parent_id']));
                if ($tableLog->source_id == $tableLog->destination_id) {
                    $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));            
                    $tableLog->state = 4;
                    $tableLog->store();
                    continue;
                }
                $item['parent_id']=$tableLog->destination_id;
            } else {
                $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));            
                $tableLog->state = 4;
                $tableLog->store();
                continue;
            }           
            
            //log            
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));            
            $tableLog->created = null;
            $tableLog->state = 3;
            $tableLog->tables_id = $task->id;            
            
            // Reset
            $destination_table->reset();
            
            // Bind
            if (!$destination_table->bind($item)) {
                $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_BIND', $item['id'], $destination_table->getError() ).'</p>';
                $this->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }

            // Store
            if (!$destination_table->store()) {
                $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_STORE', $item['id'], $destination_table->getError() ).'</p>';
                $this->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }                
            
            //Log
            $tableLog->state = 4;
            $tableLog->store();
        } //Main loop end        
    }
    public function viewlevels_fix($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_table = $this->getTable('ViewLevel', 'JTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;
    
        $message= ('<h2>'.JText::_(COM_USERS).' - '.JText::_(COM_USERS_VIEWLEVELS).'</h2>');
        $this->writeLog($message); 

        // Load items
        $query = 'SELECT destination_id
            FROM #__sptransfer_log
            WHERE tables_id = '. (int) $task->id.' AND ( state = 2 OR state = 3 )';
            if (!is_null($ids[0])) $query .= ' AND source_id IN ('. implode(',', $ids) .')';
            $query .= ' ORDER BY id ASC';        
        $destination_db->setQuery($query);
        $result = $destination_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }        
        $temp = $destination_db->loadResultArray();
        
        // Return if empty
        if (is_null($temp[0])) {
            $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_FIX).'</p>';
            $this->writeLog($message);
            return;
        }            
                
        $query = 'SELECT * 
            FROM #__viewlevels
            WHERE id > 0';        
        $query .= ' AND id IN ('. implode(',', $temp) .')';        
        $query .= ' ORDER BY id ASC';
        $destination_db->setQuery($query);
        $result = $destination_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $destination_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $destination_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }
        $items = $destination_db->loadAssocList();       
        
        //percentage
        $percTotal = count($items);
        if ( $percTotal < 100 ) $percKlasma = 0.1;
        if ( $percTotal > 100 && $percTotal < 2000 ) $percKlasma = 0.05;
        if ( $percTotal > 2000 ) $percKlasma = 0.01;
        $percTen = $percKlasma * $percTotal;
        $percCounter = 0;
        if ($percTotal == 0) {
            $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_TRANSFER).'</p>';
            $this->writeLog($message);
        }
                
        // Loop to save items
        foreach ($items as $i => $item) {
            //percentage
            $percCounter += 1;
            if (@($percCounter % $percTen) == 0) {
                $perc = round(( 100 * $percCounter ) / $percTotal);
                $message = $perc.'% '.JText::_('COM_SPTRANSFER_MSG_PROCESSED').'<br/>';  
                $this->writeLog($message);
            }

            // Set destination_id
            $rules = json_decode($item['rules']);
            foreach ($rules as $k => $rule) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 1, "source_id" => $rule));
                $rules2[$k] = (int) $tableLog->destination_id;
                if ($rules2[$k] == 0) $rules2[$k] = 1;
            }
            $item['rules'] = json_encode($rules2);
            
            //log            
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));            
            $tableLog->created = null;
            $tableLog->state = 3;
            $tableLog->tables_id = $task->id;            
            
            // Reset
            $destination_table->reset();
            
            // Bind
            if (!$destination_table->bind($item)) {
                $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_BIND', $item['id'], $destination_table->getError() ).'</p>';
                $this->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }

            // Store
            if (!$destination_table->store()) {
                $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_STORE', $item['id'], $destination_table->getError() ).'</p>';
                $this->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            }                
            
            //Log
            $tableLog->state = 4;
            $tableLog->store();
        } //Main loop end        
    }
    public function users_fix($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;
    
        $message= ('<h2>'.JText::_(COM_USERS).' - '.JText::_(COM_USERS_USERS).'</h2>');
        $this->writeLog($message); 

        // Load items
        $query = 'SELECT destination_id
            FROM #__sptransfer_log
            WHERE tables_id = '. (int) $task->id.' AND ( state = 2 OR state = 3 )';
            if (!is_null($ids[0])) $query .= ' AND source_id IN ('. implode(',', $ids) .')';
            $query .= ' ORDER BY id ASC';        
        $destination_db->setQuery($query);
        $result = $destination_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $source_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }        
        $temp = $destination_db->loadResultArray();
        
        // Return if empty
        if (is_null($temp[0])) {
            $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_FIX).'</p>';
            $this->writeLog($message);
            return;
        }            
                
        $query = 'SELECT * 
            FROM #__user_usergroup_map
            WHERE user_id > 0';        
        $query .= ' AND user_id IN ('. implode(',', $temp) .')';        
        $query .= ' ORDER BY user_id ASC';
        $destination_db->setQuery($query);
        $result = $destination_db->query();
        if (!$result) {
            $jAp->enqueueMessage(JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $destination_db->getErrorMsg()), 'error');
            $message = '<p><b><font color="red">'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_QUERY', $destination_db->getErrorMsg()).'</font></b></p>';
            $this->writeLog($message);
            return false;
        }
        $items = $destination_db->loadAssocList();       
        
        //percentage
        $percTotal = count($items);
        if ( $percTotal < 100 ) $percKlasma = 0.1;
        if ( $percTotal > 100 && $percTotal < 2000 ) $percKlasma = 0.05;
        if ( $percTotal > 2000 ) $percKlasma = 0.01;
        $percTen = $percKlasma * $percTotal;
        $percCounter = 0;
        if ($percTotal == 0) {
            $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_TRANSFER).'</p>';
            $this->writeLog($message);
        }
                
        // Loop to save items
        foreach ($items as $i => $item) {
            //percentage
            $percCounter += 1;
            if (@($percCounter % $percTen) == 0) {
                $perc = round(( 100 * $percCounter ) / $percTotal);
                $message = $perc.'% '.JText::_('COM_SPTRANSFER_MSG_PROCESSED').'<br/>';  
                $this->writeLog($message);
            }
            
            // Set destination_id
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => 1, "source_id" => $item['group_id']));
            $item['group_id']=$tableLog->destination_id;
            $group_id = $tableLog->source_id;
            if ($tableLog->source_id == $tableLog->destination_id) {
                $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['user_id']));            
                $tableLog->state = 4;
                $tableLog->store();
                continue;
            }

            //log            
            $tableLog->reset();
            $tableLog->id = null;
            $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['user_id']));            
            $tableLog->created = null;
            $tableLog->state = 3;
            $tableLog->tables_id = $task->id;            
            
            // User Usergroup Map
            $query = 'UPDATE #__user_usergroup_map '
                    . ' SET group_id = '. (int) $item['group_id']
                    . ' WHERE user_id = '. (int) $tableLog->destination_id                    
                    . ' AND group_id = '. (int) $group_id                    
            ;
            $destination_db->setQuery($query);
            $destination_db->query();
            $result = $source_db->loadResult();
            if(!$destination_db->query()) {
                $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_UPDATE', $item['user_id'], $destination_db->getErrorMsg() ).'</p>';
                $this->writeLog($message);
                $tableLog->note = $message;
                $tableLog->store();
                continue;
            } 
            
            //Log
            $tableLog->state = 4;
            $tableLog->store();
        } //Main loop end        
    }
}
