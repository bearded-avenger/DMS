<?php

/*
 *	Editor functions - Always loaded
 */

function pl_has_editor(){

	return (class_exists('PageLinesTemplateHandler')) ? true : false;

}


// Function to be used w/ compabibility mode to de
function pl_deprecate_v2(){

	return true;

}


function pl_use_editor(){

	return true;

}


// Process old function type to new format
function process_to_new_option_format( $old_options ){

	$new_options = array();

	foreach($old_options as $key => $o){

		if($o['type'] == 'multi_option' || $o['type'] == 'text_multi'){

			$sub_options = array();
			foreach($o['selectvalues'] as $sub_key => $sub_o){
				$sub_options[ ] = process_old_opt($sub_key, $sub_o, $o);
			}
			$new_options[ ] = array(
				'type' 	=> 'multi',
				'title'	=> $o['title'],
				'opts'	=> $sub_options
			);
		} else {
			$new_options[ ] = process_old_opt($key, $o);
		}

	}

	return $new_options;
}

function process_old_opt( $key, $old, $otop = array()){

	if(isset($otop['type']) && $otop['type'] == 'text_multi')
		$old['type'] = 'text';

	$defaults = array(
        'type' 			=> 'check',
		'title'			=> '',
		'inputlabel'	=> '',
		'exp'			=> '',
		'shortexp'		=> '',
		'count_start'	=> 0,
		'count_number'	=> '',
		'selectvalues'	=> array(),
		'taxonomy_id'	=> '',
		'post_type'		=> '',
		'span'			=> 1
	);

	$old = wp_parse_args($old, $defaults);

	$exp = ($old['exp'] == '' && $old['shortexp'] != '') ? $old['shortexp'] : $old['exp'];

	if($old['type'] == 'text_small'){
		$type = 'text';
	} elseif($old['type'] == 'colorpicker'){
		$type = 'color';
	} elseif($old['type'] == 'check_multi'){
		$type = 'multi';
		
		foreach($old['selectvalues'] as $key => &$info){
			$info['type'] = 'check';
		}
	} else
		$type = $old['type'];

	$new = array(
		'key'			=> $key,
		'title'			=> $old['title'],
		'label'			=> $old['inputlabel'],
		'type'			=> $type,
		'help'			=> $exp,
		'opts'			=> $old['selectvalues'],
		'span'			=> $old['span'],
		
	);

	if($old['type'] == 'count_select'){
		$new['count_start'] = $old['count_start'];
		$new['count_number'] = $old['count_number'];
	}

	if($old['taxonomy_id'] != '')
		$new['taxonomy_id'] = $old['taxonomy_id'];

	if($old['post_type'] != '')
		$new['post_type'] = $old['post_type'];

	return $new;
}

function pl_create_id( $string ){

	$string = str_replace( ' ', '_', trim( strtolower( $string ) ) );
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);

	return $string;
}

function pl_new_clone_id(){
	return substr(uniqid(), -6);
}


/*
 * Lets document utility functions
 */
function pl_add_query_arg( $args ) {

	global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
	return add_query_arg( $args, $current_url );
}

/*
 * This function recursively converts an multi dimensional array into a multi layer object
 * Needed for json conversion in < php 5.2
 */
function pl_arrays_to_objects( array $array ) {

	$objects = new stdClass;

	if( is_array($array) ){
		foreach ( $array as $key => $val ) {

			if($key === ''){
				$key = 0;
			}

	        if ( is_array( $val ) && !empty( $val )) {


				$objects->{$key} = pl_arrays_to_objects( $val );

	        } else {

	            $objects->{$key} = $val;

	        }
	    }

	}

    return $objects;
}

function pl_animation_array(){
	$animations = array(
		'no-anim'			=> 'No Animation',
		'pla-fade'			=> 'Fade',
		'pla-scale'			=> 'Scale',
		'pla-from-left'		=> 'From Left',
		'pla-from-right'	=> 'From Right', 
		'pla-from-bottom'	=> 'From Bottom', 
		'pla-from-top'		=> 'From Top', 
	); 
	
	return $animations;
}

function pl_icon_array(){

	$icons = array(
		'glass',
		'music',
		'search',
		'envelope',
		'heart',
		'star',
		'star-empty',
		'user',
		'film',
		'th-large',
		'th',
		'th-list',
		'ok',
		'remove',
		'zoom-in',
		'zoom-out',
		'off',
		'signal',
		'cog',
		'trash',
		'home',
		'file',
		'time',
		'road',
		'download-alt',
		'download',
		'upload',
		'inbox',
		'play-circle',
		'repeat',
		'refresh',
		'list-alt',
		'lock',
		'flag',
		'headphones',
		'volume-off',
		'volume-down',
		'volume-up',
		'qrcode',
		'barcode',
		'tag',
		'tags',
		'book',
		'bookmark',
		'print',
		'camera',
		'font',
		'bold',
		'italic',
		'text-height',
		'text-width',
		'align-left',
		'align-center',
		'align-right',
		'align-justify',
		'list',
		'indent-left',
		'indent-right',
		'facetime-video',
		'picture',
		'pencil',
		'map-marker',
		'adjust',
		'tint',
		'edit',
		'share',
		'check',
		'move',
		'step-backward',
		'fast-backward',
		'backward',
		'play',
		'pause',
		'stop',
		'forward',
		'fast-forward',
		'step-forward',
		'eject',
		'chevron-left',
		'chevron-right',
		'plus-sign',
		'minus-sign',
		'remove-sign',
		'ok-sign',
		'question-sign',
		'info-sign',
		'screenshot',
		'remove-circle',
		'ok-circle',
		'ban-circle',
		'arrow-left',
		'arrow-right',
		'arrow-up',
		'arrow-down',
		'share-alt',
		'resize-full',
		'resize-small',
		'plus',
		'minus',
		'asterisk',
		'exclamation-sign',
		'gift',
		'leaf',
		'fire',
		'eye-open',
		'eye-close',
		'warning-sign',
		'plane',
		'calendar',
		'random',
		'comment',
		'magnet',
		'chevron-up',
		'chevron-down',
		'retweet',
		'shopping-cart',
		'folder-close',
		'folder-open',
		'resize-vertical',
		'resize-horizontal',
		'bar-chart',
		'twitter-sign',
		'facebook-sign',
		'camera-retro',
		'key',
		'cogs',
		'comments',
		'thumbs-up',
		'thumbs-down',
		'star-half',
		'heart-empty',
		'signout',
		'linkedin-sign',
		'pushpin',
		'external-link',
		'signin',
		'trophy',
		'github-sign',
		'upload-alt',
		'lemon',
		'phone',
		'check-empty',
		'bookmark-empty',
		'phone-sign',
		'twitter',
		'facebook',
		'github',
		'unlock',
		'credit-card',
		'rss',
		'hdd',
		'bullhorn',
		'bell',
		'certificate',
		'hand-right',
		'hand-left',
		'hand-up',
		'hand-down',
		'circle-arrow-left',
		'circle-arrow-right',
		'circle-arrow-up',
		'circle-arrow-down',
		'globe',
		'wrench',
		'tasks',
		'filter',
		'briefcase',
		'fullscreen',
		'group',
		'link',
		'cloud',
		'beaker',
		'cut',
		'copy',
		'paper-clip',
		'save',
		'sign-blank',
		'reorder',
		'list-ul',
		'list-ol',
		'strikethrough',
		'underline',
		'table',
		'magic',
		'truck',
		'pinterest',
		'pinterest-sign',
		'google-plus-sign',
		'google-plus',
		'money',
		'caret-down',
		'caret-up',
		'caret-left',
		'caret-right',
		'columns',
		'sort',
		'sort-down',
		'sort-up',
		'envelope-alt',
		'linkedin',
		'undo',
		'legal',
		'dashboard',
		'comment-alt',
		'comments-alt',
		'bolt',
		'sitemap',
		'umbrella',
		'paste',
		'lightbulb',
		'exchange',
		'cloud-download',
		'cloud-upload',
		'user-md',
		'stethoscope',
		'suitcase',
		'bell-alt',
		'coffee',
		'food',
		'file-alt',
		'building',
		'hospital',
		'ambulance',
		'medkit',
		'fighter-jet',
		'beer',
		'h-sign',
		'plus-sign-alt',
		'double-angle-left',
		'double-angle-right',
		'double-angle-up',
		'double-angle-down',
		'angle-left',
		'angle-right',
		'angle-up',
		'angle-down',
		'desktop',
		'laptop',
		'tablet',
		'mobile-phone',
		'circle-blank',
		'quote-left',
		'quote-right',
		'spinner',
		'circle',
		'reply',
		'github-alt',
		'folder-close-alt',
		'folder-open-alt',
	);
	
	$r = asort($icons);
	$icons = array_values($icons);
	return $icons;
}

function get_sidebar_select(){


	global $wp_registered_sidebars;
	$allsidebars = $wp_registered_sidebars;
	ksort($allsidebars);

	$sidebar_select = array();
	foreach($allsidebars as $key => $sb){

		$sidebar_select[ $sb['id'] ] = array( 'name' => $sb['name'] );
	}

	return $sidebar_select;
}

function pl_count_sidebar_widgets( $sidebar_id ){

	$total_widgets = wp_get_sidebars_widgets();

	if(isset($total_widgets[ $sidebar_id ]))
		return count( $total_widgets[ $sidebar_id ] );
	else
		return false;
}



