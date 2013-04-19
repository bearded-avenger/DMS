<?php
/*
	Section: Hero
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive full width image and text area with button.
	Class Name: PLheroUnit
	Workswith: templates, main, header, morefoot, content
	Cloning: true
	Filter: component
*/

/*
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLheroUnit extends PageLinesSection {

	function section_opts() {

		$opts = array(

			array(
				'title'			=> 'Hero Settings',
				'type'			=> 'multi',
				'opts'			=> array(

			array(
				'key'			=> 'pagelines_herounit_title',
				'type'			=> 'text',
				'label'			=> 'Heading' ),

			array(
				'key'			=> 'pagelines_herounit_tagline',
				'type'			=> 'textarea',
				'label'			=> 'Subtext' )
										)
									),

			array(
				'title'			=> 'Hero Image',
				'key'			=> 'pagelines_herounit_image',
				'type'			=> 'image_upload',
				'label'			=> 'Upload Custom Image' ),

			array(
				'title'			=> 'Content Widths',
				'type'			=> 'multi',
				'opts'			=> array(

			array(
				'label'			=> 'Text Area Width',
				'key'			=> 'herounit_left_width',
				'default'		=> 'span6',
				'type'			=> 'select',
				'opts'			=> array(

				'span3'			=> array( 'name' => '25%' ),
				'span4'			=> array( 'name' => '33%' ),
				'span6'			=> array( 'name' => '50%' ),
				'span8'			=> array( 'name' => '66%' ),
				'span9'			=> array( 'name' => '75%' ),
				'span7'			=> array( 'name' => '90%' )
										)
									),

			array(
				'label'			=> 'Image Area Width',
				'key'			=> 'herounit_right_width',
				'default'		=> 'span6',
				'type'			=> 'select',
				'opts'			=> array(

				'span3'			=> array( 'name' => '25%' ),
				'span4'			=> array( 'name' => '33%' ),
				'span6'			=> array( 'name' => '50%' ),
				'span8'			=> array( 'name' => '66%' ),
				'span9'			=> array( 'name' => '75%' ),
				'span7'			=> array( 'name' => '90%' )
										)
									)
								)
							),

			array(
				'title'			=> 'Hero Action Button',
				'type'			=> 'multi',
				'opts'			=> array(

			array(
				'key'			=> 'herounit_button_link',
				'type'			=> 'text',
				'label'			=> 'Button link destination (URL - Required)' ),

			array(
				'key'			=> 'herounit_button_text',
				'type'			=> 'text',
				'label'			=> 'Hero Button Text' ),

			array(
				'key'			=> 'herounit_button_target',
				'type'			=> 'check',
				'default'		=> false,
				'label'			=> 'Open link in new window' ),

			array(
				'label'			=> 'Select Button Color',
				'key'			=> 'herounit_button_theme',
				'default'		=> 'primary',
				'type'			=> 'select',
				'opts'			=> array(

				'primary'		=> array( 'name' => 'Blue' ),
				'warning'		=> array( 'name' => 'Orange' ),
				'important'		=> array( 'name' => 'Red' ),
				'success'		=> array( 'name' => 'Green' ),
				'info'			=> array( 'name' => 'Light Blue' ),
				'reverse'		=> array( 'name' => 'Grey' )
										)
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

		$hero_lt_width = $this->opt( 'herounit_left_width', $this->oset );
		$hero_rt_width = $this->opt( 'herounit_right_width', $this->oset );
   		$hero_title = $this->opt( 'pagelines_herounit_title', $this->tset );
		$hero_tag = $this->opt( 'pagelines_herounit_tagline', $this->tset );
		$hero_img = $this->opt( 'pagelines_herounit_image', $this->tset );
		$hero_butt_link = $this->opt( 'herounit_button_link', $this->oset );
		$hero_butt_text = $this->opt( 'herounit_button_text', $this->oset );
		$hero_butt_target = ( $this->opt( 'herounit_button_target', $this->oset ) ) ? ' target="_blank"': '';
		$hero_butt_theme = $this->opt( 'herounit_button_theme', $this->oset );

		if ( ! $hero_rt_width )
			$hero_rt_width = 'span6';
			
		if ( ! $hero_lt_width )
			$hero_lt_width = 'span6';
			
		$hero_title = ($hero_title) ? $hero_title : __('The Hero!', 'pagelines'); 
		$hero_tag = ($hero_tag) ? $hero_tag : __('Now just set up your Hero section options', 'pagelines'); 
			
   	?>

	   	<div class="pl-hero-wrap row">

	   	<?php
	   	if( $hero_lt_width )
			printf( '<div class="pl-hero %s">', $hero_lt_width );
			?>
				<?php

					if( $hero_title )
						printf( '<h1 class="m-bottom">%s</h1>', $hero_title );

					if( $hero_tag )
		  				printf( '<p>%s</p>', $hero_tag );

	  			    if( $hero_butt_link )
					printf( '<a %s class="btn btn-%s btn-large" href="%s">%s</a> ', $hero_butt_target, $hero_butt_theme, $hero_butt_link, $hero_butt_text );
	  			?>
			</div>

	   	<?php
	   	if( $hero_rt_width )
			printf( '<div class="pl-hero-image %s">', $hero_rt_width );

		if( $hero_img )
			printf( '<div class="hero_image"><img class="pl-imageframe" src="%s" /></div>', apply_filters( 'pl_hero_image', $hero_img ) );

		?>
			</div>

		</div>

		<?php

	}

}