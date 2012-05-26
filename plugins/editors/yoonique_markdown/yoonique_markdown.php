<?php
/*
 * @version   11-15 Thu Apr 5 06:18:35 2012 -0700
 * @package   yoonique markdown editor
 * @author    yoonique[.]net
 * @copyright Copyright (C) yoonique[.]net and all rights reserved.
 */


defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgEditorYoonique_markdown extends JPlugin {

	protected $_width = '';

	public function onInit() {
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$path = JURI::root() . 'plugins/editors/yoonique_markdown/yoonique_markdown/';
		} else {
			$path = JURI::root() . 'plugins/editors/yoonique_markdown/';
		}

		$doc = JFactory::getDocument();

		$this->_width = $this->params->get('width', '');

		if ($this->params->get('jquery', 'yes') == 'yes' && JFactory::getApplication()->get('jquery') <> 'true') {
			JFactory::getApplication()->set('jquery', true);
			$doc->addScript('http://code.jquery.com/jquery-1.6.1.min.js');
		}

		$doc->addScript    ($path . 'jquery.markitup.js');
		$doc->addScript    ($path . 'sets/markdown/set.js');
		$doc->addStylesheet($path . 'sets/markdown/style.css');
		$doc->addStylesheet($path . 'skins/simple/style.css');

		$txt =	"<script type=\"text/javascript\">
					function insertAtCursor(myField, myValue) {
						if (document.selection) {
							// IE support
							myField.focus();
							sel = document.selection.createRange();
							sel.text = myValue;
						} else if (myField.selectionStart || myField.selectionStart == '0') {
							// MOZILLA/NETSCAPE support
							var startPos = myField.selectionStart;
							var endPos = myField.selectionEnd;
							myField.value = myField.value.substring(0, startPos)
								+ myValue
								+ myField.value.substring(endPos, myField.value.length);
						} else {
							myField.value += myValue;
						}
					}
				</script>";
		return $txt;
	}

	public function onSave($editor) {
		return;
	}

	public function onGetContent($editor) {
		return "document.getElementById( '$editor' ).value;\n";
	}

	public function onSetContent($editor, $html) {
		return "document.getElementById( '$editor' ).value = $html;\n";
	}

	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true) {

		if ($this->_width <> '') {
			$width = $this->_width;
		}

		if (is_numeric($width)) {
			$width .= 'px';
		}
		if (is_numeric($height)) {
			$height .= 'px';
		}

		$doc = JFactory::getDocument();

		$id = preg_replace('/([[|\]])/', '\\\\\\\\\1', $name);

		$token = JUtility::getToken();
		$admin = JFactory::getApplication()->isAdmin();
		$uri =& JFactory::getURI();
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$path = $uri->root() . 'plugins/editors/yoonique_markdown/yoonique_markdown';
		} else {
			$path = $uri->root() . 'plugins/editors/yoonique_markdown';
		}
		if ($uri->isSSL()) {
			$path = str_replace('http://', 'https://', $path);
		}

		$script = <<<EOF
jQuery.noConflict(); jQuery(document).ready(function() {mySettings.previewParserPath = '$path/convert.php?$token=1&admin=$admin';  jQuery('textarea#$id').markItUp(mySettings); });
EOF;
		$doc->addScriptDeclaration($script);

		$buttons = $this->_displayButtons($name, $buttons);
		$editor = "<textarea name=\"$name\" id=\"$name\" cols=\"$col\" rows=\"$row\" style=\"width: $width; height: $height;\">$content</textarea>" . $buttons;

		return $editor;
	}

	public function onGetInsertMethod($name) {
		$doc = & JFactory::getDocument();

		$js = "\tfunction jInsertEditorText( text, editor ) {
			insertAtCursor( document.getElementById(editor), text );
		}";
		$doc->addScriptDeclaration($js);

		return true;
	}

	protected function _displayButtons($name, $buttons) {
		JHTML::_('behavior.modal', 'a.modal-button');

		$args = array( 'name' => $name, 'event' => 'onGetInsertMethod');

		$return = '';

		$results[] = $this->update($args);
		foreach ($results as $result) {
			if (is_string($result) && trim($result)) {
				$return .= $result;
			}
		}

		if (!empty($buttons)) {
			$results = $this->_subject->getButtons($name, $buttons);
			$return .= "\n<div id=\"editor-xtd-buttons\">\n";

			foreach ($results as $button) {
				if ($button->get('name')) {
					$modal = ($button->get('modal')) ? 'class="modal-button"' : null;
					$href = ($button->get('link')) ? 'href="' . $button->get('link') . '"' : null;
					$onclick = ($button->get('onclick')) ? 'onclick="' . $button->get('onclick') . '"' : null;
					$return .= "<div class=\"button2-left\"><div class=\"" . $button->get('name') . "\"><a " . $modal . " title=\"" . $button->get('text') . "\" " . $href . " " . $onclick . " rel=\"" . $button->get('options') . "\">" . $button->get('text') . "</a></div></div>\n";
				}
			}
			$return .= "</div>\n";
		}

		return $return;
	}

}
