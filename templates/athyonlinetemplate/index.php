<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
   xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

<head>
<jdoc:include type="head" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/athyonlinetemplate/css/template.css" type="text/css" />
<link rel="shortcut icon" href="<?php echo $this->baseurl ?>/templates/athyonlinetemplate/favicon.ico" />
</head>

<body>

<div class="width">
		
		<!-- page header -->
		
		<div class="head">
			<div class="logo">
				<div class="hide>
				<h1>Atherton Online</h1>
				</div>
				<div class="clear"></div>
			</div>
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


</body>