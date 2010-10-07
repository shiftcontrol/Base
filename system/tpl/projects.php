<!DOCTYPE html> 
<html lang="en"> 
<head> 
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>SC Base SSSSSS : %title%</title>
	<link rel="stylesheet" href="/static/css/reset.css" type="text/css" media="screen" title="stylesheet" charset="utf-8">
	<link rel="stylesheet" href="/static/css/grid.css" type="text/css" media="screen" title="stylesheet" charset="utf-8">
	<link rel="stylesheet" href="/static/css/site.css" type="text/css" media="screen" title="stylesheet" charset="utf-8">

	<?php
		# Requires the Header Module to be loaded
		if( class_exists( Header ) ) Header::Get();
	?>
	
</head>
<body>

<div class="container_22">

	<div id='brand'> 
		<div class='grid_3'> 
			<h3>shiftcontrol</h3> 
		</div>
		<div id="logobarbg" class='grid_1 blackbg'>
			<span id="logobarfg">&nbsp;</span>
		</div>
		<script type="text/javascript">
		/*
			logo = document.getElementById("logobarfg");
			setInterval(function(){
				logo.width = rand(0,100) ."%";
				}, 500);
		*/
		</script>
		
		<div class='clear'>&nbsp;</div>
		<div class='grid_22' id="topline">&nbsp;</div>
		
	</div>
	<div class='clear'>&nbsp;</div>
	
	<div id='content'> 
		<div class='grid_3'> 
			<h2>&nbsp;</h2> 
		</div>
		<div class='grid_1'>
			&nbsp;
		</div>

		<div class='grid_14' id="article"> 
			%body%
		</div>
	
		<div class='grid_1'> 
			<h2>&nbsp;</h2> 
		</div>
		
		<div class='grid_3'>
			<div class='grid_3'>
				<p>Tags: Identity, Realtime, Digital %tags%</p>
			</div>
			<div class='grid_3'>
				<p>by: %author%</p>
				<p>date:<br />%date%</p>
				<p>last modified:<br />%mdate%</p>
			</div>

			<div class='grid_3'>
				<p>For: %client%</p>
				<p>By: %team%</p>
				<p>Country: %country%</p>
			</div>
		</div>
	</div>
	<div class='clear'>&nbsp;</div>
</div>

</body>
</html>
