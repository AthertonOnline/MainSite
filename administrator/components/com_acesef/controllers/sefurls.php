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
class AcesefControllerSefUrls extends AcesefController {

	// Main constructer
	function __construct() 	{
		parent::__construct('sefurls', 'urls');
	}

	// Modal
	function url() {		
		$view = $this->getView(ucfirst($this->_context), 'html');
		$view->setModel($this->_model, true);
		$view->view('url');
	}
	
	function apply() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Save
		$this->_model->apply();
		
		// Return
		parent::route(JText::_('ACESEF_URL_SEF_SAVED'));
	}

	function generate() {		
		$view = $this->getView(ucfirst($this->_context), 'generate');
		$view->generate('generate');
	}

	function generateURLs() {
		$this->_model->generateURLs();

		?>
		<script language="javascript">
			SqueezeBox.close();
		</script>
		<?php
	}
	
	function delete() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');

		// Action
		$where = parent::_getWhere($this->_model);
		if (!$this->_model->delete($where)) {
			$msg = JText::_('ACESEF_COMMON_RECORDS_DELETED_NOT');
		} else {
			$msg = JText::_('ACESEF_COMMON_RECORDS_DELETED');
		}
		
		// Return
		parent::route($msg);
	}
	
	function publish() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'published', 1, $this->_model);
		
		// Return
		parent::route();
    }
	
	function unpublish() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'published', 0, $this->_model);
		
		// Return
		parent::route();
	}
	
	// Use URL
	function used() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get ids
		$ids = parent::_getIDs($this->_table, $this->_model);
		
		// Action
		foreach ($ids as $index => $id) {
			$this->_model->used($id);
		}
		// Return
		parent::route();
	}
	
	function resetUsed() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateField($this->_table, 'used', 0, $this->_model);
		
		// Return
		parent::route();
    }
	
	function lock() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'locked', 1, $this->_model);
		
		// Return
		parent::route();
    }
	
	function unlock() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'locked', 0, $this->_model);
		
		// Return
		parent::route();
	}
	
	function block() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'blocked', 1, $this->_model);
		
		// Return
		parent::route();
    }
	
	function unblock() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'blocked', 0, $this->_model);
		
		// Return
		parent::route();
	}
	
	function trash() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'trashed', 1, $this->_model);
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'published', 0, $this->_model);
		
		// Return
		parent::route();
    }
	
	function restore() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'trashed', 0, $this->_model);
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'published', 1, $this->_model);
		
		// Return
		parent::route();
	}
	
	function cache() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateCache($this->_table, 'url_real', '*', 1, $this->_model);
		
		// Return
		parent::route();
	}
	
	function uncache() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateCache($this->_table, 'url_real', '*', 0, $this->_model);
		
		// Return
		parent::route();
	}
	
	function setTags() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$tags = JRequest::getCmd('newtags', null, 'post');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'tags', $tags, $this->_model);
		
		// Return
		parent::route();
    }
	
	function setIlinks() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$ilinks = JRequest::getCmd('newilinks', null, 'post');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'ilinks', $ilinks, $this->_model);
		
		// Return
		parent::route();
    }
	
	function setBookmarks() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$bookmarks = JRequest::getCmd('newbookmarks', null, 'post');
	
		// Action
		parent::updateParam($this->_table, 'AcesefSefUrls', 'params', 'bookmarks', $bookmarks, $this->_model);
		
		// Return
		parent::route();
    }
	
	// Unpublish
	function setUnpublishTag() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		$tag = trim(JRequest::getCmd('newtag', null, 'post'));
		$this->_model->unpublishTag($tag);
		
		// Return
		parent::route();
	}
	
	function copyToMetadata() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		if (!$this->_model->copyTo('metadata')) {
			$msg = JText::_('ACESEF_URL_SEF_RECORDS_COPIED');
		} else {
			$msg = JText::_('ACESEF_URL_SEF_RECORDS_COPIED_NOT');
		}
		
		// Return
		parent::route($msg);
    }
	
	function copyToSitemap() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		if (!$this->_model->copyTo('sitemap')) {
			$msg = JText::_('ACESEF_URL_SEF_RECORDS_COPIED');
		} else {
			$msg = JText::_('ACESEF_URL_SEF_RECORDS_COPIED_NOT');
		}
		
		// Return
		parent::route();
    }
	
	//
	// Edit methods
	//
	
	// Save changes
	function editSave() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get post
		$post = JRequest::get('post');
		$post2 = $this->_model->_modifyPostData($post);
		
		// Save record
		if (!parent::saveRecord($post, 'AcesefSefUrls', $post['id'])) {
			return JError::raiseWarning(500, JText::_('ACESEF_COMMON_RECORD_SAVED_NOT'));
		} else {
			// Save Aliases
			$this->_model->_saveAliases($post2['aliases']);
			
			// Save Metadata
			$this->_model->_saveMetadata($post2['metadata']);
			
			// Save Sitemap
			$this->_model->_saveSitemap($post2['sitemap']);
			
			// Cache
			$value = 0;
			if (isset($post['url_cached'])) {
				$value = 1;
			}
			$where = " WHERE url_real = '{$post['url_real']}'";
			parent::updateCache($this->_table, 'url_real', '*', $value, $this->_model, $where);
			
			if ($post['modal'] == '1') {
				// Display message
				JFactory::getApplication()->enqueueMessage(JText::_('ACESEF_COMMON_RECORD_SAVED'));
			} else {
				// Return
				parent::route(JText::_('ACESEF_COMMON_RECORD_SAVED'));
			}
		}
	}
	
	// Apply changes
	function editApply() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get post
		$post = JRequest::get('post');
		$post2 = $this->_model->_modifyPostData($post);
		
		// Save record
		if (!parent::saveRecord($post, 'AcesefSefUrls', $post['id'])) {
			return JError::raiseWarning(500, JText::_('ACESEF_COMMON_RECORD_SAVED_NOT'));
		} else {
			// Save Aliases
			$this->_model->_saveAliases($post2['aliases']);
			
			// Save Metadata
			$this->_model->_saveMetadata($post2['metadata']);
			
			// Save Sitemap
			$this->_model->_saveSitemap($post2['sitemap']);
			
			if ($post['modal'] == '1') {
				// Return
				$this->setRedirect('index.php?option=com_acesef&controller=sefurls&task=edit&cid[]='.$post['id'].'&tmpl=component', JTEXT::_('ACESEF_COMMON_RECORD_SAVED'));
			} else {
				// Return
				$this->setRedirect('index.php?option=com_acesef&controller=sefurls&task=edit&cid[]='.$post['id'], JText::_('ACESEF_COMMON_RECORD_SAVED'));
			}
		}
	}
	
	// Save & Moved
	function editSaveMoved() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get post
		$post = JRequest::get('post');
		$post2 = $this->_model->_modifyPostData($post);
		
		// Save record
		if (!parent::saveRecord($post, 'AcesefSefUrls', $post['id'])) {
			return JError::raiseWarning(500, JText::_('ACESEF_COMMON_RECORD_SAVED_NOT'));
		} elseif ($this->_model->editSaveMoved($post['id'])) {
			// Save Aliases
			$this->_model->_saveAliases($post2['aliases']);
			
			// Save Metadata
			$this->_model->_saveMetadata($post2['metadata']);
			
			// Save Sitemap
			$this->_model->_saveSitemap($post2['sitemap']);
			
			if ($post['modal'] == '1') {
				// Display message
				JFactory::getApplication()->enqueueMessage(JText::_('ACESEF_URL_EDIT_SAVED_MOVED'));
			} else {
				// Return
				parent::route(JText::_('ACESEF_URL_EDIT_SAVED_MOVED'));
			}
		}
	}
}
?>