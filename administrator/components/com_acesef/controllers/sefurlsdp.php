<?php
/**
* @version		1.5.0
* @package		AceSEF
* @subpackage	AceSEF
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No permission
defined('_JEXEC') or die('Restricted Access');

// Controller Class
class AcesefControllerSefUrlsDp extends AcesefController {

	// Main constructer
	function __construct() 	{
		parent::__construct('sefurlsdp', 'urls');
	}
	
	function publish() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'published', 1, $this->_model);
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
    }
	
	function unpublish() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'published', 0, $this->_model);
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
	}
	
	// Use URL
	function used() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$ids = parent::_getIDs($this->_table, $this->_model);
		
		AcesefUtility::import('models.sefurls');
		$model = new AcesefModelSefUrls();
		
		foreach ($ids as $index => $id) {
			$model->used($id);
		}
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
	}
	
	function resetUsed() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		parent::updateField($this->_table, 'used', 0, $this->_model);
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
    }
	
	function lock() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'locked', 1, $this->_model);
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
    }
	
	function unlock() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'locked', 0, $this->_model);
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
	}
	
	function block() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'blocked', 1, $this->_model);
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
    }
	
	function unblock() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'blocked', 0, $this->_model);
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
	}
	
	function cache() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		$fields = "id, url_sef, url_real, used, meta, sitemap, tags, ilinks, bookmarks, params";
		parent::updateCache($this->_table, 'url_real', $fields, 1, $this->_model);
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
	}
	
	function uncache() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		$fields = "id, url_sef, url_real, used, meta, sitemap, tags, ilinks, bookmarks, params";
		parent::updateCache($this->_table, 'url_real', $fields, 0, $this->_model);
		
		// Redirect
		$id = JRequest::getInt('id');
		$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view&id='.$id.'&tmpl=component');
	}
}
?>