<?php
/*
	Section: Callout
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shows a callout banner with optional graphic call to action
	Class Name: PageLinesCallout
	Cloning: true
	Workswith: templates, main, header, morefoot
	Filter: component
*/

/**
 * Callout Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesCallout extends PageLinesSection {

	function section_opts(){
		$opts = array(
			 		array(
						'type' 			=> 'multi',
						'title' 		=> 'Enter text for your callout banner section',
						'opts'			=> array(

					array(
						'key'			=> 'pagelines_callout_header',
						'type' 			=> 'text',
						'size'			=> 'big',
						'label' 		=> 'Callout Header'),

					array(
						'key'			=> 'pagelines_callout_subheader',
						'type' 			=> 'text',
						'size'			=> 'big',
						'label' 		=> 'Callout Subtext'),

					array(
			 			'type'			=> 'select',
			 			'title'			=> 'Callout Alignment',
			 			'key'			=> 'pagelines_callout_align',
			 			'label'			=> 'Select alignment.',
			 			'opts'			=> array(

			 			'right'			=> array( 'name' => 'Right' ),
			 			'left'			=> array( 'name' => 'Left' ),
			 			'center'		=> array( 'name' => 'Center' )
									)
			 					)
							)
						),

			 		array(
			 			'type'			=> 'multi',
			 			'title'			=> 'Callout Action Button',
			 			'opts'			=> array(

			 		array(
			 			'type'			=> 'text',
			 			'key'			=> 'pagelines_callout_button_link',
			 			'label'			=> 'Button link destination (URL - Required)' ),

			 		array(
			 			'key'			=> 'pagelines_callout_button_text',
			 			'type'			=> 'text',
			 			'label'			=> 'Callout Button Text'),

			 		array(
			 			'key'			=> 'pagelines_callout_button_target',
			 			'type'			=> 'check',
			 			'label'			=> 'Open link in new window'),

			 		array(
			 			'type'			=> 'select',
			 			'title'			=> 'Button Theme',
			 			'key'			=> 'pagelines_callout_button_theme',
			 			'opts'			=> array(

			 			'primary'		=> array('name' => 'Blue'),
						'warning'		=> array('name' => 'Orange'),
						'important'		=> array('name' => 'Red'),
						'success'		=> array('name' => 'Green'),
						'info'			=> array('name' => 'Light Blue'),
						'reverse'		=> array('name' => 'Grey')
			 						)
			 					)
							)
			 			),
			 		array(
			 			'type'			=> 'image_upload',
			 			'key'			=> 'pagelines_callout_image',
			 			'title'			=> 'Callout Image'
					)
				);
		return $opts;
	}

	/**
	* Section template.
	*/
 	function section_template() {

		$call_title = $this->opt( 'pagelines_callout_header', $this->tset );
		$call_sub = $this->opt( 'pagelines_callout_subheader', $this->tset );
		$call_img = $this->opt( 'pagelines_callout_image', $this->oset );
		$call_link = $this->opt( 'pagelines_callout_button_link', $this->tset );
		$call_btext = $this->opt( 'pagelines_callout_button_text', $this->tset );
		$call_btheme = $this->opt( 'pagelines_callout_button_theme', $this->tset );
		$target = ( $this->opt( 'pagelines_callout_button_target', $this->oset ) ) ? 'target="_blank"' : '';
		$call_action_text = ($this->opt('pagelines_callout_action_text', $this->oset)) ? $this->opt( 'pagelines_callout_action_text', $this->oset ) : __( 'Start Here', 'pagelines' );

		$styling_class = ($call_sub) ? 'with-callsub' : '';

		$alignment = $this->opt( 'pagelines_callout_align', $this->oset );

		$call_align = ( $alignment == 'left' ) ? '' : 'rtimg';

		if( $call_title || $call_img ) {

			if( $alignment == 'center' ): ?>
<div class="callout-area fix callout-center <?php echo $styling_class;?>">
	<div class="callout_text">
		<div class="callout_text-pad">
			<?php $this->draw_text( $call_title, $call_sub, $call_img ); ?>
		</div>
	</div>
	<div class="callout_action <?php echo $call_align;?>">
		<?php $this->draw_action( $call_link, $target, $call_img, $call_btheme, $call_btext ); ?>
	</div>

</div>
<?php else: ?>
<div class="callout-area media fix <?php echo $styling_class;?>">
	<div class="callout_action img <?php echo $call_align;?>">
		<?php $this->draw_action( $call_link, $target, $call_img, $call_btheme, $call_btext ); ?>
	</div>
	<div class="callout_text bd">
		<div class="callout_text-pad">
			<?php $this->draw_text( $call_title, $call_sub, $call_img ); ?>
		</div>
	</div>
</div>
<?php endif;
		} else
			echo setup_section_notify( $this, __( 'Set Callout page options to activate.', 'pagelines' ) );
	}

	function draw_action( $call_link, $target, $call_img, $call_btheme, $call_btext ){
		if( $call_img )
			printf('<div class="callout_image"><a %s href="%s" ><img src="%s" /></a></div>', $target, $call_link, $call_img );
		else
			printf('<a %s class="btn btn-%s btn-large" href="%s">%s</a> ', $target, $call_btheme, $call_link, $call_btext );

	}

	function draw_text( $call_title, $call_sub, $call_img ){
		printf( '<h2 class="callout_head %s">%s</h2>', ( ! $call_img ) ? 'noimage' : '', $call_title );

		if( $call_sub )
			printf( '<p class="callout_sub subhead %s">%s</p>', ( ! $call_img ) ? 'noimage' : '', $call_sub );
	}
}