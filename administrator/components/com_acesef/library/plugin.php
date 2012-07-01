<?php
/**
* @version		1.5.0
* @package		AceSEF Library
* @subpackage	Plugin
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Plugin class
class AcesefPlugin {
	
	function __construct() {
		// Get config object
		$this->AcesefConfig = AcesefFactory::getConfig();
	}

	function onAfterInitialise() {
		$mainframe =& JFactory::getApplication();
		
        JFactory::getConfig()->set('sef_suffix', 0);

		// Store J! router for later use
		$router =& $mainframe->getRouter();
		$mainframe->set('acesef.global.jrouter', $router);
		
		// Activate AceSEF router
		$acesef_router = new JRouterAcesef();
		$router->attachParseRule(array($acesef_router, 'parse'));
        $router->attachBuildRule(array($acesef_router, 'build'));
		
		// Multilanguage fix
		AcesefLanguage::multilangFix();
		
		// Instantiate JoomFishManager before JFDatabase loading to prevent infinity loop...
		if (AcesefUtility::JoomFishInstalled() && class_exists('JoomFishManager')) {
			JoomFishManager::getInstance();
		}
		
		// Post varsa yönlendirme
		$post = JRequest::get('post');
		if (is_array($post) && !empty($post)) {
			return;
		}

        // Ajax durumunda yönlendirme yapma
        $tmpl = JRequest::getCmd('tmpl');
        $format = JRequest::getCmd('format');
        if ($tmpl == 'component' || $format == 'raw') {
            return;
        }
		
		// www settings
		$uri  =& JFactory::getURI();
		$host =& $uri->getHost();
		
		$redirect = false;
		
		$wwwredirect = $this->AcesefConfig->redirect_to_www;
		if ($wwwredirect != 0) {
			if ($wwwredirect == 1 && strpos($host, 'www') !== 0) {
				$redirect = true;
				$uri->setHost('www.'.$host);
			}
			elseif ($wwwredirect == 2 && strpos($host, 'www') === 0) {
				$redirect = true;
				$uri->setHost(substr($host, 4, strlen($host)));
			}
		}
		
		if ($redirect === true) {
			$mainframe->redirect($uri->toString());
            $mainframe->close();
		}
	}

	function onAfterRoute() {
		$mainframe =& JFactory::getApplication();
		
		// SSL settings
		$uri =& JFactory::getURI();
		
		$redirect = false;
		
		// SSL
		$Itemid = JRequest::getInt('Itemid');
		$menu_items = $this->AcesefConfig->force_ssl;
		
		if (!empty($menu_items) && is_array($menu_items)) {
			if ($uri->isSSL() == false) {
				if (in_array($Itemid, $menu_items)) {
					$redirect = true;
					$uri->setScheme('https');
				}
			}
			else {
				if (!in_array($Itemid, $menu_items)) {
					$redirect = true;
					$uri->setScheme('http');
				}
			}
		}
		
		if ($redirect === true) {
			$mainframe->redirect($uri->toString());
            $mainframe->close();
		}
	}

    function onAfterDispatch() {
		$document =& JFactory::getDocument();
		
		// Metadata
		AcesefMetadata::plugin($document);
		
		// Set base href
		if ($this->AcesefConfig->base_href == 2) {
			$document->setBase(JURI::current());
		} elseif ($this->AcesefConfig->base_href == 3) {
			$document->setBase(JURI::base());
		} elseif ($this->AcesefConfig->base_href == 4) {
			$document->setBase('');
		}
		
		// Internal Links, Bookmarks, Tags
		if (($this->AcesefConfig->ilinks_mode == '1') || ($this->AcesefConfig->bookmarks_mode == '1') || ($this->AcesefConfig->tags_mode == '1')) {
			$component = JRequest::getCmd('option');
			$ext_params	= AcesefCache::getExtensionParams($component);
			
			if ($ext_params) {
				$buffer =& $document->getBuffer('component');
				
				if (ACESEF_PACK == 'pro' && $this->AcesefConfig->tags_mode == 1 && class_exists('AcesefTags')) {
					AcesefTags::plugin($buffer, $ext_params, 'component', $component);
				}
				
				if (ACESEF_PACK == 'pro' && $this->AcesefConfig->ilinks_mode == 1 && class_exists('AcesefIlinks')) {
					AcesefIlinks::plugin($buffer, $ext_params, 'component', $component);
				}
				
				if (ACESEF_PACK == 'pro' && $this->AcesefConfig->bookmarks_mode == 1 && class_exists('AcesefBookmarks')) {
					AcesefBookmarks::plugin($buffer, $ext_params, 'component', $component);
				}
				
				$document->setBuffer($buffer, 'component');
			}
		}
    }

	function onContentPrepare(&$text) {
		// Tags, Internal Links, Bookmarks
		if (($this->AcesefConfig->ilinks_mode == '1') || ($this->AcesefConfig->bookmarks_mode == '1') || ($this->AcesefConfig->tags_mode == '1')) {
			$component = JRequest::getCmd('option');
			$ext_params	= AcesefCache::getExtensionParams($component);
			
			if ($ext_params) {
				if (ACESEF_PACK == 'pro' && $this->AcesefConfig->tags_mode == 1 && class_exists('AcesefTags')) {
					AcesefTags::plugin($text, $ext_params, 'content', $component);
				}	
				
				if (ACESEF_PACK == 'pro' && $this->AcesefConfig->ilinks_mode == 1 && class_exists('AcesefIlinks')) {
					AcesefIlinks::plugin($text, $ext_params, 'content', $component);
				}
				
				if (ACESEF_PACK == 'pro' && $this->AcesefConfig->bookmarks_mode == 1 && class_exists('AcesefBookmarks')) {
					AcesefBookmarks::plugin($text, $ext_params, 'content', $component);
				}
			}
		}
		
		// No follow
		if (JString::strpos($text, 'href="') === false) {
			return;
		}
		
		if ($this->AcesefConfig->seo_nofollow == 1) {
			$regex = '/<a (.*?)href=[\"\'](.*?)\/\/(.*?)[\"\'](.*?)>(.*?)<\/a>/i';
			$text = preg_replace_callback($regex, array('AcesefUtility', 'noFollow'), $text);
		}
	}
	
	function onAfterRender() {
		$sef_plugin = JPATH_SITE.DS.'plugins'.DS.'system'.DS.'sef.php';
		if (!JPluginHelper::isEnabled('system', 'sef') && file_exists($sef_plugin)) {
			require_once($sef_plugin);
			if (class_exists('plgSystemSEF')) {
				plgSystemSEF::onAfterRender();
			}
		}
		
		// Generator
		if ($this->AcesefConfig->meta_generator_rem == 1) {
			$body = JResponse::getBody();
			$body = preg_replace('/<meta\s*name="generator"\s*content=".*\/>/isU', '', $body);
			JResponse::setBody($body);
		}
		
		if (ACESEF_PACK == 'pro' && $this->AcesefConfig->sm_auto_cron_mode == 1 && $this->AcesefConfig->sm_auto_cron_freq != 1 && class_exists('AcesefSitemap')) {
			if ($this->AcesefConfig->sm_auto_cron_last == "") {
				$this->AcesefConfig->sm_auto_cron_last = time();
			}
			
			if ($this->AcesefConfig->sm_auto_cron_last <  (time() - ($this->AcesefConfig->sm_auto_cron_freq * 3600))){
				$ext_params = new JParameter("");
				AcesefSitemap::cron($ext_params);
				
				$this->AcesefConfig->sm_auto_cron_last = time();
				
				// Store the new cron time
				AcesefUtility::storeConfig($this->AcesefConfig);
			}
		}
	}

	function onAcesefTags(&$text) {
		// Tags
		if (ACESEF_PACK == 'pro' && $this->AcesefConfig->tags_mode == '1') {
			$component = JRequest::getCmd('option');
			$ext_params	= AcesefCache::getExtensionParams($component);
			
			if ($ext_params && class_exists('AcesefTags')) {
				AcesefTags::plugin($text, $ext_params, 'trigger', $component);
			}
		}
	}

	function onAcesefIlinks(&$text) {
		// Tags, Internal Links, Bookmarks
		if (ACESEF_PACK == 'pro' && $this->AcesefConfig->ilinks_mode == '1') {
			$component = JRequest::getCmd('option');
			$ext_params	= AcesefCache::getExtensionParams($component);
			
			if ($ext_params && class_exists('AcesefIlinks')) {
				AcesefIlinks::plugin($text, $ext_params, 'trigger', $component);
			}
		}
	}

	function onAcesefBookmarks(&$text) {
		// Tags, Internal Links, Bookmarks
		if (ACESEF_PACK == 'pro' && $this->AcesefConfig->bookmarks_mode == '1') {
			$component = JRequest::getCmd('option');
			$ext_params	= AcesefCache::getExtensionParams($component);
			
			if ($ext_params && class_exists('AcesefBookmarks')) {
				AcesefBookmarks::plugin($text, $ext_params, 'trigger', $component);
			}
		}
	}

    // ----------------------------------------------------------

    public function onUserBeforeSave($user, $isnew, $new)
    {
		if (key_exists('params', $user))
		{
			$registry = new JRegistry();
			$registry->loadString($user['params']);
			self::_languageFilterData('_user_lang_code', $registry->get('language'));
            $_user_lang_code = self::_languageFilterData('_user_lang_code');
			if (empty($_user_lang_code)) {
				self::_languageFilterData('_user_lang_code', self::$default_lang);
			}
		}
	}

	public function onUserAfterSave($user, $isnew, $success, $msg)
    {
		if (key_exists('params', $user) && $success)
		{
			$registry = new JRegistry();
			$registry->loadString($user['params']);
			$lang_code = $registry->get('language');
			if (empty($lang_code)) {
				$lang_code = self::_languageFilterData('default_lang');
			}
			$app = JFactory::getApplication();
            $lang_codes = self::_languageFilterData('lang_codes');
			if ($lang_code == self::_languageFilterData('_user_lang_code') || !isset($lang_codes[$lang_code]))
			{
				if ($app->isSite())
				{
					$app->setUserState('com_users.edit.profile.redirect',null);
				}
			}
			else
			{
				if ($app->isSite())
				{
					$app->setUserState('com_users.edit.profile.redirect','index.php?Itemid='.$app->getMenu()->getDefault($lang_code)->id.'&lang='.$lang_codes[$lang_code]->sef);
					self::_languageFilterData('tag', $lang_code);
					// Create a cookie
					$conf = JFactory::getConfig();
					$cookie_domain 	= $conf->get('config.cookie_domain', '');
					$cookie_path 	= $conf->get('config.cookie_path', '/');
					setcookie(JUtility::getHash('language'), $lang_code, time() + 365 * 86400, $cookie_path, $cookie_domain);
				}
			}
		}
	}

	public function onUserLogin($user, $options = array())
	{
		$app = JFactory::getApplication();
		if ($app->isSite())
		{
            $lang_code = $user['language'];
            if (empty($lang_code)) {
                $lang_code = self::_languageFilterData('default_lang');
            }
            self::_languageFilterData('tag', $lang_code);
            // Create a cookie
            $conf = JFactory::getConfig();
            $cookie_domain 	= $conf->get('config.cookie_domain', '');
            $cookie_path 	= $conf->get('config.cookie_path', '/');
            setcookie(JUtility::getHash('language'), $lang_code, time() + 365 * 86400, $cookie_path, $cookie_domain);
		}
	}

    private function _languageFilterData($key, $value = null) {
        static $data = array();

        if (empty($data)) {
            $data['mode_sef'] 	        = true;
            $data['_user_lang_code']    = null;
            $data['sefs'] 		        = JLanguageHelper::getLanguages('sef');
            $data['lang_codes'] 	    = JLanguageHelper::getLanguages('lang_code');
            $data['default_lang']       = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
            $data['default_sef'] 	    = $data['lang_codes'][$data['default_lang']]->sef;
            $data['tag'] 			    = JFactory::getLanguage()->getTag();
        }

        if (!empty($value)) {
            $data[$key] = $value;
        }

        return $data[$key];
    }
}