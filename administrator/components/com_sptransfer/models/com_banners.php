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

class SPTransferModelCom_Banners extends JModel
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
    public function categories($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_table = $this->getTable('Category', 'JTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;
    
        $message= ('<h2>'.JText::_(COM_BANNERS).' - '.JText::_(COM_BANNERS_CATEGORIES).'</h2>');
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
            FROM #__categories
            WHERE extension LIKE "com_banners"';        
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
                "INSERT INTO #__categories" .
                " (id)".
                " VALUES (".$destination_db->quote($item['id']).")"
                );
            if(!$destination_db->query()) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                        "INSERT INTO #__categories" .
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
                        "SELECT id FROM #__categories" .
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
                    "DELETE FROM #__categories" .
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
                    $destination_table->alias .= '-sp-'.rand(100,999);                    
                    if (!$destination_table->store()) {
                        // delete record
                        $destination_db->setQuery(
                            "DELETE FROM #__categories" .
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
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_DUPLICATE_ALIAS', $item['id'], $destination_table->alias ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                } else {
                    // delete record
                    $destination_db->setQuery(
                        "DELETE FROM #__categories" .
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
        
        // Rebuild the hierarchy.
        if (!$destination_table->rebuild()) {
            $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_REBUILD', $destination_table->getError() ).'</p>';
            $this->writeLog($message);
            return false;
        }
                
        // Clear the component's cache
        $cache = JFactory::getCache('com_categories');
        $cache->clean();
    }
    public function banners($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_banners'.DS.'tables');
        $destination_table = $this->getTable('Banner', 'BannersTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;
        
        $message= ('<h2>'.JText::_(COM_BANNERS).' - '.JText::_(COM_BANNERS_BANNERS).'</h2>');
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
            FROM #__banners
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
                "INSERT INTO #__banners" .
                " (id)".
                " VALUES (".$destination_db->quote($item['id']).")"
                );
            if(!$destination_db->query()) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                        "INSERT INTO #__banners" .
                        " (name)".
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
                        "SELECT id FROM #__banners" .
                        " WHERE name LIKE ".$destination_db->quote('sp_transfer')
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
            
            // Check alias
            /*
            if ($params->get("duplicate_alias", 0)) {
                if ($destination_table->load(array('alias'=>$item['alias'],'catid'=>$item['catid'])) && ($destination_table->id != $item['id'] || $item['id']==0)) {
                    $item['alias'] .= '-sp-'.rand(100,999);
                }
            }
             * 
             */
            

            // Reset
            $destination_table->reset();
            
            //Replace existing item
            if ($params->get("new_ids", 0) == 2) $destination_table->load($item['id']);
            
            // Bind
            if (!$destination_table->bind($item)) {
                // delete record
                $destination_db->setQuery(
                    "DELETE FROM #__banners" .
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
            //$result = $destination_table->store();
            //if (!is_null($destination_table->getError())) {
            if (!$destination_table->store()) {
                if ($params->get("duplicate_alias", 0)) {
                    $destination_table->alias .= '-sp-'.rand(100,999);                    
                    if (!$destination_table->store()) {
                        // delete record
                        $destination_db->setQuery(
                            "DELETE FROM #__banners" .
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
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_DUPLICATE_ALIAS', $item['id'], $destination_table->alias ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                } else {
                    // delete record
                    $destination_db->setQuery(
                        "DELETE FROM #__banners" .
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

            //banner tracks
            $query = 'SELECT *
                FROM #__banner_tracks
                WHERE banner_id = '. (int) $tableLog->source_id;
            $source_db->setQuery($query);
            $source_db->query();            
            $result = $source_db->loadAssoc();
            if (!is_null($result)) { 
                $query= "INSERT INTO #__banner_tracks
                        (track_date, track_type, banner_id, count)
                        VALUES (".$destination_db->quote($result['track_date'])." , ".
                        $destination_db->quote($result['track_type'])." , ".
                        $destination_db->quote($tableLog->destination_id)." , ".
                        $destination_db->quote($result['count']).
                        ")";
                $destination_db->setQuery($query);
                if(!$destination_db->query()) {
                        $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_CREATE', $item['id'], $destination_db->getErrorMsg() ).'</p>';
                        $this->writeLog($message);
                    }
            }

            //Log
            $tableLog->state = 2;
            $tableLog->store();
        } //Main loop end
    }
    public function banner_clients($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_banners'.DS.'tables');
        $destination_table = $this->getTable('Client', 'BannersTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;
        
        $message= ('<h2>'.JText::_(COM_BANNERS).' - '.JText::_(COM_BANNERS_BANNER_CLIENTS).'</h2>');
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
            FROM #__banner_clients
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
                "INSERT INTO #__banner_clients" .
                " (id)".
                " VALUES (".$destination_db->quote($item['id']).")"
                );
            if(!$destination_db->query()) {
                if ($params->get("new_ids", 0) == 1) {
                    $destination_db->setQuery(
                        "INSERT INTO #__banner_clients" .
                        " (name)".
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
                        "SELECT id FROM #__banner_clients" .
                        " WHERE name LIKE ".$destination_db->quote('sp_transfer')
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
                    "DELETE FROM #__banner_clients" .
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
                    $destination_table->alias .= '-sp-'.rand(100,999);                    
                    if (!$destination_table->store()) {
                        // delete record
                        $destination_db->setQuery(
                            "DELETE FROM #__banner_clients" .
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
                    $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_DUPLICATE_ALIAS', $item['id'], $destination_table->alias ).'</p>';
                    $this->writeLog($message);
                    $tableLog->note = $message;
                } else {
                    // delete record
                    $destination_db->setQuery(
                        "DELETE FROM #__banner_clients" .
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
            $tableLog->state = 4;
            $tableLog->store();
        } //Main loop end
    }
    public function categories_fix($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        $destination_table = $this->getTable('Category', 'JTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;
    
        $message= ('<h2>'.JText::_(COM_BANNERS).' - '.JText::_(COM_BANNERS_CATEGORIES).'</h2>');
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
            FROM #__categories
            WHERE extension LIKE "com_banners"
            AND parent_id > 0';        
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
            //created_user_id
            if ($item['created_user_id'] > 0) {
                $tableLog->load(array("tables_id" => 3, "source_id" => $item['created_user_id']));
                if ($tableLog->source_id != $tableLog->destination_id) {
                    $item['created_user_id'] = $tableLog->destination_id;
                }
            }
            //modified_user_id
            /*
            if ($item['modified_user_id'] > 0) {
                $tableLog->load(array("tables_id" => 3, "source_id" => $item['modified_user_id']));
                if ($tableLog->source_id != $tableLog->destination_id) {
                    $item['modified_user_id'] = $tableLog->destination_id;
                }
            }
             * 
             */

            //parent_id
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
                $item['parent_id'] = $tableLog->destination_id;
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
        
        // Rebuild the hierarchy.
        if (!$destination_table->rebuild()) {
            $message = '<p>'.JText::sprintf('COM_SPTRANSFER_MSG_ERROR_REBUILD', $destination_table->getError() ).'</p>';
            $this->writeLog($message);
            return false;
        }
                
        // Clear the component's cache
        $cache = JFactory::getCache('com_categories');
        $cache->clean();
    }
    public function banner_clients_fix($ids = null)    {
        $message= ('<h2>'.JText::_(COM_BANNERS).' - '.JText::_(COM_BANNERS_BANNER_CLIENTS).'</h2>');
        $this->writeLog($message);
        $message = '<p>'.JText::_(COM_SPTRANSFER_NOTHING_TO_FIX).'</p>';
        $this->writeLog($message);
        return;
    }
    public function banners_fix($ids = null)    {
        // Initialize
        $jAp = $this->jAp;
        $tableLog = $this->tableLog;
        $destination_db = $this->destination_db;
        $destination_query = $this->destination_query;
        $source_db = $this->source_db;
        $source_query = $this->source_query;
        JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_banners'.DS.'tables');
        $destination_table = $this->getTable('Banner', 'BannersTable');
        $user = $this->user;
        $params = $this->params;
        $task = $this->task;
    
        $message= ('<h2>'.JText::_(COM_BANNERS).' - '.JText::_(COM_BANNERS_BANNERS).'</h2>');
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
            FROM #__banners
            WHERE catid > 0';        
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
            //cid
            if ($item['cid'] > 0) {
                $tableLog->load(array("tables_id" => 13, "source_id" => $item['cid']));
                if ($tableLog->source_id != $tableLog->destination_id) {
                    $item['cid'] = $tableLog->destination_id;
                }
            }
            //catid
            if ($item['catid'] > 1) {
                $tableLog->reset();
                $tableLog->id = null;
                $tableLog->load(array("tables_id" => 12, "source_id" => $item['catid']));
                if ($tableLog->source_id == $tableLog->destination_id) {
                    $tableLog->load(array("tables_id" => $task->id, "destination_id" => $item['id']));            
                    $tableLog->state = 4;
                    $tableLog->store();
                    continue;
                }
                $item['catid'] = $tableLog->destination_id;
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
}
