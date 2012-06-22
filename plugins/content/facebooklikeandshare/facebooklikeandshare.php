<?php
/**
 * @version  3.5
 * @Project  Facebook Like And Share Button
 * @author   Compago TLC
 * @package  
 * @copyright Copyright (C) 2011 Compago TLC. All rights reserved.
 * @license  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgContentfacebooklikeandshare extends JPlugin {

  function plgContentfacebooklikeandshare( &$subject,$params ) {
    parent::__construct( $subject,$params ); 
  }

function onContentPrepare($context, &$article, &$params, $page=0){ 
    $ignore_pagination = $this->params->get( 'ignore_pagination'); 
    $view = JRequest::getCmd('view');
    if (($view == 'article')&&($ignore_pagination==1)) {
      $this->InjectCode($article, $params ,0,$view);
    }
}
function onContentBeforeDisplay($context,&$article,&$params,$page=0){ 
    $ignore_pagination = $this->params->get( 'ignore_pagination'); 
    $view = JRequest::getCmd('view'); 
    if (($view != 'article')||(($view == 'article')&&($ignore_pagination==0))) { 
      $this->InjectCode($article, $params ,1,$view);
    }
}
function onBeforeCompileHead(){
    $this->InjectHeadCode();
}



private function InjectHeadCode(){
    $document                 = & JFactory::getDocument();
    $enable_like              = $this->params->get( 'enable_like');
    $view                     = JRequest::getCmd('view');
    if (($enable_like==1)&&($view == 'article')) {
        $config                   =& JFactory::getConfig();
        $site_name                =$config->getValue('config.sitename');
        $type                     = $this->params->get('type');
        $description              = $this->params->get('description');
        $enable_admin             = $this->params->get('enable_admin');
        $enable_app               = $this->params->get('enable_app');
        $admin_id                 = $this->params->get('admin_id');
        $app_id                   = $this->params->get('app_id');
        $meta                     = "";
        $head                     = join(',',$document->getHeadData());
        if (($description==0)&&(preg_match('/<meta property="og:description"/i',$head)==0)){
          $description = $document->getMetaData("description");
          $meta .= "<meta property=\"og:description\" content=\"$description\"/>".PHP_EOL;
        }
        if ($enable_admin==0) { $admin_id=""; } 
        if ($enable_app==0) { $app_id=""; }
        
        if (preg_match('/<meta property="og:language"/i',$head)==0){
          $meta .= "<meta property=\"og:language\" content=\"".$document->getMetaData("language")."\"/>".PHP_EOL;
        }
        if (preg_match('/<meta property="og:type"/i',$head)==0){
          $meta .= "<meta property=\"og:type\" content=\"$type\"/>".PHP_EOL;
        }
        if (preg_match('/<meta property="og:site_name"/i',$head)==0){
          $meta .= "<meta property=\"og:site_name\" content=\"$site_name\"/>".PHP_EOL;
        }
        if (preg_match('/<meta property="fb:admins"/i',$head)==0){
          $meta .= "<meta property=\"fb:admins\" content=\"$admin_id\"/>".PHP_EOL;
        }
        if (preg_match('/<meta property="fb:app_id"/i',$head)==0){
          $meta .= "<meta property=\"fb:app_id\" content=\"$app_id\"/>".PHP_EOL;
        }
        $document->addCustomTag( $meta );
    }
}

private function InjectCode(&$article, &$params, $mode,$view){
    $document                 = & JFactory::getDocument();
    $position                 = $this->params->get( 'position',  '' );
    $enable_like              = $this->params->get( 'enable_like');
    $enable_share             = $this->params->get( 'enable_share');
    $enable_comments          = $this->params->get( 'enable_comments');
    $language                 = $this->params->get( 'language');
    $view_article_buttons     = $this->params->get( 'view_article_buttons');
    $view_frontpage_buttons   = $this->params->get( 'view_frontpage_buttons');
    $view_category_buttons    = $this->params->get( 'view_category_buttons');
    $view_article_comments    = $this->params->get( 'view_article_comments');
    $view_frontpage_comments  = $this->params->get( 'view_frontpage_comments');
    $view_category_comments   = $this->params->get( 'view_category_comments');
    $title = $title= htmlentities( $article->title, ENT_QUOTES, "UTF-8");     
    $enable_view_comments     = 0;
    $enable_view_buttons      = 0;
    $meta                     = "";
    if (($view == 'article')&&($view_article_buttons)||
        ($view == 'featured')&&($view_frontpage_buttons)||
        ($view == 'category')&&($view_category_buttons)) { 
      $enable_view_buttons = 1; 
    } 
    if (($view == 'article')&&($view_article_comments)||
        ($view == 'featured')&&($view_frontpage_comments)||
        ($view == 'category')&&($view_category_comments)) {
      $enable_view_comments = 1; 
    }
    if (($enable_view_buttons != 1)&&($enable_view_comments != 1)){
      return; 
    }
    $url              = $this->getPageUrl($article);
    if (($enable_like==1)&&($view == 'article')) {  
    	$head         = join(',',$document->getHeadData());  
        $description  = $this->params->get('description');
        $defaultimage = $this->params->get('defaultimage');
        if (($description==1)&&(preg_match('/<meta property="og:description"/i',$head)==0)){   
          $content = html_entity_decode(strip_tags($article->text));
          $pos = strpos($content, '.');
          if($pos === false) {
            $description = $content;
          } else {
            $description = substr($content, 0, $pos+1);
          }
          $meta .= "<meta  content=\"$description\"/>".PHP_EOL;
        }
        if (preg_match('/<meta property="og:image"/i',$head)==0){
          if (preg_match('%<img.*?src=(?:"(.*?)"|\'(.*?)\').*?>%i', $article->text, $regs)) {
			  if (preg_match('/^http/i',$regs[1])) {
				$image = $regs[1];
			  } else {
                $image ='http://'.$_SERVER['SERVER_NAME'].'/'.$regs[1];
			  }
          } else {
              if ($defaultimage=="") {
                $image = 'http://'.$_SERVER['SERVER_NAME'].'/plugins/content/facebooklikeandshare/link.png';
              } else {
                $image = $defaultimage;
              }
          } 
          if ($image != "") {
            $meta .= "<meta property=\"og:image\" content=\"$image\"/>".PHP_EOL;
          }
        }
        if (preg_match('/<meta property="og:url"/i',$head)==0){
          $meta .= "<meta property=\"og:url\" content=\"$url\"/>".PHP_EOL;
        }
        if (preg_match('/<meta property="og:title"/i',$head)==0){
          $meta .= "<meta property=\"og:title\" content=\"$title\"/>".PHP_EOL;
        }
        if (preg_match('/<meta property="og:author"/i',$head)==0){
          	$meta .= "<meta property=\"og:author\" content=\"$article->author\"/>".PHP_EOL;
        }
        
        $document->addCustomTag( $meta );
    }

    
    if (($enable_like==1)||($enable_share==1)||($enable_comments==1)) {
      $document->addScript("http://connect.facebook.net/$language/all.js#xfbml=1");
    }

    if ($view!='article'){ 
      $tmp = $article->introtext;
    } else {
      $tmp = $article->text;
    }
    
    if ((($enable_like==1)||($enable_share==1))&&($enable_view_buttons==1)) {
      $htmlcode=$this->getPlugInButtonsHTML($params, $article, $url, $title);
      if ($position == '1'){
        $tmp = $htmlcode . $tmp;  
      }
      if ($position == '2'){
        $tmp = $tmp . $htmlcode;
      }
      if ($position == '3'){
        $tmp = $htmlcode . $tmp . $htmlcode;
      }  
    }
  
    if (($enable_comments==1)&&($enable_view_comments==1)) {
      $tmp = $tmp . $this->getPlugInCommentsHTML($params, $article, $url, $title);
    }

    if ($view!='article'){ 
      $article->introtext=$tmp;
    } else {
      $article->text=$tmp;
    }

  }

  private function  getPlugInCommentsHTML($params, $article, $url, $title) {
    $document                    = & JFactory::getDocument();
    $category_tobe_excluded      = $this->params->get('category_tobe_excluded_comments', '' );
    $content_tobe_excluded       = $this->params->get('content_tobe_excluded_comments', '' );
    $excludedCatList             = @explode ( ",", $category_tobe_excluded ); 
    $excludedContentList         = @explode ( ",", $content_tobe_excluded );  
    if ($article->id!=null) {
      if ( in_array ( $article->id, $excludedContentList ) || in_array ( $article->catid, $excludedCatList ) ) { 
        return;
      } 
    } else {
      if (in_array ( JRequest::getCmd('id'), $excludedCatList )) return;
    }
    $htmlCode                    = "";
    $number_comments             = $this->params->get('number_comments');
    $width                       = $this->params->get('width_comments');
    $container_comments          = $this->params->get('container_comments','1');
    $css_comments                = $this->params->get('css_comments','border-top-style:solid;border-top-width:1px;padding:10px;text-align:center;');
    if ($css_comments!="") { $css_comments="style=\"$css_comments\""; }
    $enable_comments_count       = $this->params->get('enable_comments_count');
    $container_comments_count    = $this->params->get('container_comments_count','1');
    $css_comments_count          = $this->params->get('css_comments_count');
    if ($css_comments_count!="") { $css_comments_count="style=\"$css_comments_count\""; }
 
    if ($container_comments==1){
      $htmlCode .="<div $css_comments>";
    } elseif ($container_comments==2) {
      $htmlCode .="<p $css_comments>";
    }
    if ($enable_comments_count==1){
      if ($container_comments_count==1){
        $htmlCode .="<div $css_comments_count>";
      } elseif ($container_comments_count==2) {
        $htmlCode .="<p $css_comments_count>";
      }
      $htmlCode .= "<fb:comments-count href=\"$url\"></fb:comments-count> comments";
      if ($container_comments==1){
        $htmlCode .="</div>";
      } elseif ($container_comments==2) {
        $htmlCode .="</p>";
      }
    }
    $tmp = "<fb:comments href=\"$url\" num_posts=\"$number_comments\" width=\"$width\"></fb:comments>";
    $enable_admin                = $this->params->get('enable_admin');
    $admin_id                    = $this->params->get('admin_id');
    if ($enable_admin==1) {
      $document->setMetaData("fb:admins","$admin_id");
    }
    $enable_app                = $this->params->get('enable_app');
    $app_id                    = $this->params->get('app_id');
    if ($enable_app==1) {
      $document->setMetaData("fb:app_id","$app_id");
    }
    $htmlCode .= $tmp;
    if ($container_comments==1){
      $htmlCode .="</div>";
    } elseif ($container_comments==2) {
      $htmlCode .="</p>";
    }
    return $htmlCode;
  }
  private function  getPlugInButtonsHTML($params, $article, $url, $title) {
    $category_tobe_excluded      = $this->params->get('category_tobe_excluded_buttons', '' );
    $content_tobe_excluded       = $this->params->get('content_tobe_excluded_buttons', '' );
    $excludedCatList             = @explode ( ",", $category_tobe_excluded ); 
    $excludedContentList         = @explode ( ",", $content_tobe_excluded );  
    if ($article->id!=null) {
      if ( in_array ( $article->id, $excludedContentList ) || in_array ( $article->catid, $excludedCatList ) ) { 
        return;
      } 
    } else {
      if (in_array ( JRequest::getCmd('id'), $excludedCatList )) return;
    }
    $layout_style                = $this->params->get('layout_style','button_count');
    $show_faces                  = $this->params->get('show_faces');  
    if ($show_faces == 1) {
      $show_faces = "true";
    } else {
      $show_faces = "false";
    }
    $width_like                  = $this->params->get('width_like');
    $css_buttons                 = $this->params->get('css_buttons','height:40px;');
    if ($css_buttons!="") { $css_buttons="style=\"$css_buttons\""; }
    $css_like                    = $this->params->get('css_like','float:left;margin:10px;');
    if ($css_like!="") { $css_like="style=\"$css_like\""; }
    $css_share                   = $this->params->get('css_share','float:right;margin:10px;');
    if ($css_share!="") { $css_share="style=\"$css_share\""; }
    $container_buttons           = $this->params->get('container_buttons','1');
    $container_like              = $this->params->get('container_like','1');
    $container_share             = $this->params->get('container_share','1');
    $enable_like                 = $this->params->get('enable_like','1');
    $enable_share                = $this->params->get('enable_share','1');
    $send                        = $this->params->get('send','1');
    $htmlCode                    = "";
    if ($send == 2) {
      $standalone=1;
    } else {
      $standalone=0;
      if ($send == 1) {
        $send  = "true";
      } else {
        $send = "false";
      }
    }
 
    $verb_to_display             = $this->params->get('verb_to_display','1');
    $share_button_style          = $this->params->get('share_button_style','button_count');
    if ($verb_to_display == 1) {
      $verb_to_display  = "like";
    } else {
      $verb_to_display = "recommend";
    }
    $font                        = $this->params->get( 'font');  
    $color_scheme                = $this->params->get( 'color_scheme','light');
    if ($container_buttons==1){
      $htmlCode ="<div $css_buttons>";
    } elseif ($container_buttons==2) {
      $htmlCode ="<p $css_buttons>";
    }
    $htmlCode .= "<div id=\"fb-root\"></div>";
    if ($standalone==1){
      $tmp = "<fb:send href=\"$url\" font=\"$font\" colorscheme=\"$color_scheme\"></fb:send>";
      if ($container_like==1){
        $htmlCode .="<div $css_like>$tmp</div>";
      } elseif ($container_like==2) {
        $htmlCode .="<p $css_like>$tmp</p>";
      } else {
        $htmlCode .=$tmp; 

      }
    }
    $tmp = "<fb:like href=\"$url\" layout=\"$layout_style\" show_faces=\"$show_faces\" send=\"$send\" width=\"$width_like\" action=\"$verb_to_display\" font=\"$font\" colorscheme=\"$color_scheme\"></fb:like> \n";
    if ($enable_like==1){                 
      if ($container_like==1){
        $htmlCode .="<div $css_like>$tmp</div>";
      } elseif ($container_like==2) {
        $htmlCode .="<p $css_like>$tmp</p>";
      } else {
        $htmlCode .=$tmp; 
      }
    }
    switch ($share_button_style) {
        case "icontext":
          $tmp = "<script>function fbs_click() {u=$url;t=$title;window.open('http://www.facebook.com/sharer.php?u=$url&t=$title','sharer','toolbar=0,status=0,width=626,height=436');return false;}</script><style> html .fb_share_link { padding:2px 0 0 20px; height:16px; background:url(http://static.ak.facebook.com/images/share/facebook_share_icon.gif?6:26981) no-repeat top left; }</style><a rel=\"nofollow\" href=\"http://www.facebook.com/share.php?u=$url\" onclick=\"return fbs_click()\" share_url=\"$url\" target=\"_blank\" class=\"fb_share_link\">Share on Facebook</a>"; 
		  break;
		case "button_count":
          $tmp = "<a name=\"fb_share\" type=\"button_count\" share_url=\"$url\" href=\"http://www.facebook.com/sharer.php?u=$url&t=$title\">Share</a><script src=\"http://static.ak.fbcdn.net/connect.php/js/FB.Share\" type=\"text/javascript\"></script>";
		  break;
		case "box_count":  
          $tmp = "<a name=\"fb_share\" type=\"box_count\" share_url=\"$url\" href=\"http://www.facebook.com/sharer.php?u=$url&t=$title\">Share</a><script src=\"http://static.ak.fbcdn.net/connect.php/js/FB.Share\" type=\"text/javascript\"></script>";
          break;
		case "text":   
          $tmp = "<script>function fbs_click() {u=$url;t=document.title;window.open('http://www.facebook.com/sharer.php?u=$url&t=$title','sharer','toolbar=0,status=0,width=626,height=436');return false;}</script><a rel=\"nofollow\" href=\"http://www.facebook.com/share.php?u=$url\" share_url=\"$url\" onclick=\"return fbs_click()\" target=\"_blank\">Share on Facebook</a>";
          break;
		case "icon":
           $tmp = "<script>function fbs_click() {u=$url;t=$title;window.open('http://www.facebook.com/sharer.php?u=$url&t=$title','sharer','toolbar=0,status=0,width=626,height=436');return false;}</script><style> html .fb_share_button { display: -moz-inline-block; display:inline-block; padding:1px 20px 0 5px; height:15px; border:1px solid #d8dfea; background:url(http://static.ak.facebook.com/images/share/facebook_share_icon.gif?6:26981) no-repeat top right; } html .fb_share_button:hover { color:#fff; border-color:#295582; background:#3b5998 url(http://static.ak.facebook.com/images/share/facebook_share_icon.gif?6:26981) no-repeat top right; text-decoration:none; } </style> <a rel=\"nofollow\" href=\"http://www.facebook.com/share.php?u=$url\" share_url=\"$url\" class=\"fb_share_button\" onclick=\"return fbs_click()\" target=\"_blank\" style=\"text-decoration:none;\">Share</a>"; 
		   break;
    }
    if ($enable_share==1){                 
      if ($container_share==1){
        $htmlCode .="<div $css_share>$tmp</div>";
      } elseif ($container_share==2) {
        $htmlCode .="<p $css_share>$tmp</p>";
      } else {
        $htmlCode .=$tmp; 
      };
    }
    if ($container_buttons==1){
      $htmlCode .="</div>";
    } elseif ($container_buttons==2) {
      $htmlCode .="</p>";
    }
    
    return $htmlCode; 
  }
  
  private function getPageUrl($obj){
    if (!is_null($obj)) {
      $url = JRoute::_(ContentHelperRoute::getArticleRoute($obj->id, $obj->catid));
      $uri = JURI::getInstance();
      $base  = $uri->toString( array('scheme', 'host', 'port'));
      $url = $base . $url;
      $url = JRoute::_($url, true, 0);
      return $url;
    }
  }
}
?>
