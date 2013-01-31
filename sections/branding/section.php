<?php
/*
	Section: Branding
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shows the main site logo or the site title and description.
	Class Name: PageLinesBranding
	Workswith: header 
	Filter: component
*/

/**
 * Branding Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesBranding extends PageLinesSection {

	function section_opts(){
		$opts = array(
			
			'social_links' => array(
				'type' 			=> 'multi',
				'title' 		=> 'Social Links',
				'opts'	=> array(
					array(
						'key'			=> 'twitterlink',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'label' 		=> 'Twitter URL',
					),
					array(
						'key'			=> 'facebooklink',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'label' 		=> 'Facebook URL',
					),
					array(
						'key'			=> 'linkedinlink',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'label' 		=> 'LinkedIn URL',
					),
					array(
						'key'			=> 'youtubelink',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'label' 		=> 'Youtube URL',
					),
					array(
						'key'			=> 'gpluslink',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'label' 		=> 'Google Plus URL',
					)

				)
			)
				
		);
		
		return $opts;
		
	}

	/**
	* Section template.
	*/
   function section_template() { 
			
			printf('<div class="branding_wrap fix">');
			
				pagelines_main_logo(); 
			
				pagelines_register_hook( 'pagelines_before_branding_icons', 'branding' ); // Hook 
					
				printf('<div class="icons" style="bottom: %spx; right: %spx;">', intval(pagelines_option('icon_pos_bottom')), pagelines_option('icon_pos_right'));
					
					pagelines_register_hook( 'pagelines_branding_icons_start', 'branding' ); // Hook 
			
					if(ploption('rsslink'))
						printf('<a target="_blank" href="%s" class="rsslink"><img src="%s" alt="RSS"/></a>', apply_filters( 'pagelines_branding_rssurl', get_bloginfo('rss2_url') ), $this->base_url.'/rss.png' );
					
					if(VPRO) {
						if($this->opt('twitterlink'))
							printf('<a target="_blank" href="%s" class="twitterlink"><img src="%s" alt="Twitter"/></a>', $this->opt('twitterlink'), $this->base_url.'/twitter.png');
					
						if($this->opt('facebooklink'))
							printf('<a target="_blank" href="%s" class="facebooklink"><img src="%s" alt="Facebook"/></a>', $this->opt('facebooklink'), $this->base_url.'/facebook.png');
						
						if($this->opt('linkedinlink'))
							printf('<a target="_blank" href="%s" class="linkedinlink"><img src="%s" alt="LinkedIn"/></a>', $this->opt('linkedinlink'), $this->base_url.'/linkedin.png');
						
						if($this->opt('youtubelink'))
							printf('<a target="_blank" href="%s" class="youtubelink"><img src="%s" alt="Youtube"/></a>', $this->opt('youtubelink'), $this->base_url.'/youtube.png');
						
						if($this->opt('gpluslink'))
							printf('<a target="_blank" href="%s" class="gpluslink"><img src="%s" alt="Google+"/></a>', $this->opt('gpluslink'), $this->base_url.'/google.png');
						
						pagelines_register_hook( 'pagelines_branding_icons_end', 'branding' ); // Hook 
				
					}
					
			echo '</div></div>';
					
			pagelines_register_hook( 'pagelines_after_branding_wrap', 'branding' ); // Hook
				
			?>		
			<script type="text/javascript"> 
				jQuery('.icons a').hover(function(){ jQuery(this).fadeTo('fast', 1); },function(){ jQuery(this).fadeTo('fast', 0.5);});
			</script>
<?php 	
				
		}
}