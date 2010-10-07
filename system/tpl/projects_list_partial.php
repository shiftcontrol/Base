<div class='project_item'>
	<div class='title'> 
		<h2><a href="<?php echo $permalink; ?>">%title%</a></h2> 
	</div>
	<div class='image'>
		<?php
			if( $thumb ) echo '<img src="'. $pathToFolder.$thumb .'">';
		?>
	</div>
	
	<div class='text'> 
		<p><?php echo substr($body, 0, 100) ."..."; ?></p> 
	</div>
	
</div>
<br />
<br />
