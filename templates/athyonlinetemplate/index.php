<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
   xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

<head>
<jdoc:include type="head" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/athyonlinetemplate/css/template.css" type="text/css" />
<link rel="shortcut icon" href="<?php echo $this->baseurl ?>/templates/athyonlinetemplate/favicon.ico" />
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4987469-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>

<body>

<div class="width">
		
		<!-- page header -->
		
		<div class="head">
		<a href="/">
			<div class="logo">
				<div class="logo-wheel">
				</div>
				<div class="hide">
				<h1>Atherton Online</h1>
				</div>
				<div class="clear"></div>
			</div>
		</a>
			<div class="search">
			<jdoc:include type="modules" name="search" />
			</div>
			<div id="nav">
					<jdoc:include type="modules" name="nav" />
					<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		
		<!-- page content -->
		
		<div class="content">
			<div class="home">
			<jdoc:include type="modules" name="beforecomp" style="xhtml" />
		    <div class="clear"></div>
		    <jdoc:include type="component" />
		    <div class="clear"></div>
		    <jdoc:include type="modules" name="aftercomp" style="xhtml" />
		    <div class="clear"></div>
		    </div>			
		</div>
		
		<!-- page footer -->
		<div class="clear"></div>
		<div class="footer">
			<jdoc:include type="modules" name="footer" style="xhtml" />
		</div>
	</div>
<!-- code can be found at https://github.com/organizations/AthertonOnline. Please contribute if possible -->
</body>