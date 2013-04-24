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

			array(
				'type' 			=> 'image_upload',
				'title' 		=> 'Site Image',
				'key'			=> 'pagelines_custom_logo',
				'default'		=> PL_IMAGES.'/designer.png',
			),


			array(
				'type' 			=> 'multi',
				'title' 		=> 'Social Links',
				'opts'	=> array(
						array(
							'type' 			=> 'check',
							'title' 		=> 'Show RSS',
							'key'			=> 'rsslink',
							'label'			=> 'Show RSS Link?'
						),
						array(
							'key'			=> 'twitterlink',
							'type' 			=> 'text',
							'size'			=> 'big',
							'label' 		=> 'Twitter URL'
						),

						array(
							'key'			=> 'facebooklink',
							'type' 			=> 'text',
							'size'			=> 'big',
							'label' 		=> 'Facebook URL'
						),

						array(
							'key'			=> 'linkedinlink',
							'type' 			=> 'text',
							'size'			=> 'big',
							'label' 		=> 'LinkedIn URL'
						),

						array(
							'key'			=> 'youtubelink',
							'type' 			=> 'text',
							'size'			=> 'big',
							'label' 		=> 'Youtube URL'
						),

						array(
							'key'			=> 'gpluslink',
							'type' 			=> 'text',
							'size'			=> 'big',
							'label' 		=> 'Google Plus URL'
						)
					)
				),
				array(
					'type' 			=> 'multi',
					'title' 		=> 'Icon Positioning',
					'help'			=> 'Enter offset pixel values for the icons in your branding section.',
					'opts'	=> array(

						array(
							'key'	=> 'icon_pos_bottom',
							'type'	=> 'text',
							'size'	=> 'small',
							'label'	=> __( 'Distance From Bottom (in pixels)', 'pagelines' ),
							'default'=> 12
						),
						array(
							'key'	=> 'icon_pos_right',
							'type'	=> 'text',
							'size'	=> 'small',
							'label'	=> __( 'Distance From Right (in pixels)', 'pagelines' ),
							'default'=> 1
						),
					)
				)

		);
		return $opts;
	}

	/**
	* Section template.
	*/
   function section_template() {

			echo '<div class="branding_wrap fix">';

				$this->logo();

				pagelines_register_hook( 'pagelines_before_branding_icons', 'branding' ); // Hook

				printf( '<div class="icons" style="bottom: %spx; right: %spx;">', intval( $this->opt( 'icon_pos_bottom' ) ), $this->opt( 'icon_pos_right' ) );

					pagelines_register_hook( 'pagelines_branding_icons_start', 'branding' ); // Hook

					if( $this->opt( 'rsslink' ) )
						printf( '<a target="_blank" href="%s" class="rsslink"><img src="%s" alt="RSS"/></a>', apply_filters( 'pagelines_branding_rssurl', get_bloginfo( 'rss2_url' ) ), $this->base_url . '/rss.png' );

					if( VPRO ) {
						if( $this->opt( 'twitterlink' ) )
							printf('<a target="_blank" href="%s" class="twitterlink"><img src="%s" alt="Twitter"/></a>', $this->opt( 'twitterlink' ), $this->base_url . '/twitter.png' );

						if( $this->opt( 'facebooklink' ) )
							printf('<a target="_blank" href="%s" class="facebooklink"><img src="%s" alt="Facebook"/></a>', $this->opt( 'facebooklink' ), $this->base_url . '/facebook.png' );

						if( $this->opt( 'linkedinlink' ) )
							printf('<a target="_blank" href="%s" class="linkedinlink"><img src="%s" alt="LinkedIn"/></a>', $this->opt( 'linkedinlink' ), $this->base_url . '/linkedin.png' );

						if( $this->opt( 'youtubelink' ) )
							printf('<a target="_blank" href="%s" class="youtubelink"><img src="%s" alt="Youtube"/></a>', $this->opt( 'youtubelink' ), $this->base_url . '/youtube.png' );

						if( $this->opt( 'gpluslink' ) )
							printf( '<a target="_blank" href="%s" class="gpluslink"><img src="%s" alt="Google+"/></a>', $this->opt( 'gpluslink' ), $this->base_url . '/google.png' );

						pagelines_register_hook( 'pagelines_branding_icons_end', 'branding' ); // Hook

					}

			echo '</div></div>';

			pagelines_register_hook( 'pagelines_after_branding_wrap', 'branding' ); // Hook

			?>
			<script>
				jQuery('.icons a').hover(function(){ jQuery(this).fadeTo('fast', 1); },function(){ jQuery(this).fadeTo('fast', 0.5);});
			</script>
<?php

		}


	function logo( ){

		$site_name = get_bloginfo('name');
		$site_desc = get_bloginfo('description');

		if($this->opt('pagelines_custom_logo') || apply_filters('pagelines_site_logo', '') || apply_filters('pagelines_logo_url', '')){

			$logo = apply_filters('pagelines_logo_url', esc_url($this->opt('pagelines_custom_logo', $this->oset) ));


			$logo_url = ( esc_url($this->opt('pagelines_custom_logo_url', $this->oset) ) ) ? esc_url($this->opt('pagelines_custom_logo_url', $oset) ) : home_url();

			$site_logo = sprintf(
				'<a class="plbrand mainlogo-link" href="%s" title="%s"><img class="mainlogo-img" src="%s" alt="%s" /></a>',
				$logo_url,
				$site_name,
				$logo,
				$site_name
			);

			echo apply_filters('pagelines_site_logo', $site_logo);

		} else {

			$site_title = sprintf(
				'<div class="title-container"><a class="home site-title" href="%s" title="%s">%s</a><h6 class="site-description subhead">%s</h6></div>',
				esc_url(home_url()),
				__('Home','pagelines'),
				$site_name,
				$site_desc
			);

			echo apply_filters('pagelines_site_title', $site_title);
		}
	}
}