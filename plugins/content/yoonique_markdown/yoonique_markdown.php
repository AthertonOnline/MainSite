<?php
/*
 * @version   11-0 Thu Apr 5 06:18:44 2012 -0700
 * @package   yoonique markdown plugin
 * @author    yoonique[.]net
 * @copyright Copyright (C) yoonique[.]net and all rights reserved.
 * @license   GNU General Public License version 3
 */


defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgContentYoonique_markdown extends JPlugin {

	public function onContentPrepare($context, &$article) {

		require_once(dirname(__FILE__) . "/yoonique_markdown/markdown.php");

		$markdown = New MarkdownExtra_Parser;
		$article->text = $markdown->transform($article->text);

		if ($this->params->get('htmlawed', 'no') == 'yes') {
			require_once(dirname(__FILE__) . "/yoonique_markdown/htmLawed/htmLawed.php");
			$config = array(
				'safe' => 1,
				'deny_attribute' => ',style'
			);

			// $config['anti_link_spam'] = array('`.`', '');

			$article->text = htmLawed($article->text, $config);
		}

		$article->text = "<!-- Yoonique[.net] Markdown -->\n" . $article->text;

		return true;
	}

	public function onPrepareContent(&$article) {
		require_once(dirname(__FILE__) . "/yoonique_markdown/markdown.php");

		$markdown = New MarkdownExtra_Parser;
		$article->text = $markdown->transform($article->text);

		if ($this->params->get('htmlawed', 'no') === 'yes') {
			require_once(dirname(__FILE__) . "/yoonique_markdown/htmLawed/htmLawed.php");
			$config = array(
				'safe' => 1,
				'deny_attribute' => ',style'
			);

			// $config['anti_link_spam'] = array('`.`', '');

			$article->text = htmLawed($article->text, $config);
		}

		$article->text = "<!-- Yoonique[.net] Markdown -->\n" . $article->text;

		return true;
	}

}

