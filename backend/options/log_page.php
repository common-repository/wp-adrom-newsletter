<?php 
$ang = new adromNewsletterGlobal();
?>
<div class="wrap">
<h2>WP adRom Newsletter: General</h2>

	<h3 class="title"><?php _e('Logs', 'wp-adrom-newsletter');?></h3>
	<div class="wp_adrom_newsletter_groupbox" style="overflow:auto;height:700px;">
		<?php 
			$wp_upload_dir = wp_upload_dir();			
			//$wp_upload_dir['basedir']
			$found = false;
			if ($handle = opendir($wp_upload_dir['basedir'] . '/wp_adrom_newsletter')) {
				while (false !== ($file = readdir($handle))) {					
					if (strpos($file,'.log') !== false) { //we only want files with ".log"
						
						$fh = fopen($wp_upload_dir['basedir'] . '/wp_adrom_newsletter/' . $file,'r');
						echo '<pre class="forceWordWrap">';
						while ($line = fgets($fh)) {
						   echo $line;
						}
						echo '</pre>';
						fclose($fh);		
						$found = true;						
					}
				}

				closedir($handle);
			}
			
			if(!$found){
				_e('no logs available', 'wp-adrom-newsletter');
			}
			
		?>
	</div>

</div>