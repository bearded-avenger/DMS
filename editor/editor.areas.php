<?php 


class PageLinesAreas {
	
	
	function __construct(){
	
	
		add_action('pagelines_editor_scripts', array(&$this, 'scripts'));
		$this->url = PL_PARENT_URL . '/editor';
	}
	
	function scripts(){
		wp_enqueue_script( 'pl-js-areas', $this->url . '/js/pl.areas.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}
	
	
	function area_controls($a){

		ob_start();
		?>

		<div class="pl-area-controls">
			<a class="area-control" data-area-action="add" >
				<i class="icon-plus"></i>
			</a><a class="area-control" data-area-action="up" >
				<i class="icon-chevron-up"></i>
			</a><a class="area-control" data-area-action="down" >
				<i class="icon-chevron-down"></i>
			</a>
		</div>
		<?php

		return ob_get_clean();
	}
	
	
	function area_start($a){

		printf(
			'<div class="pl-area area-tag" data-area-number="%s">%s<div class="pl-content"><div class="pl-inner area-region pl-sortable-area editor-row">%s',
			$a['area_number'],
			$this->area_controls($a),
			$this->area_sortable_buffer()
		);

	}
	
	function area_end(){
		printf('%s</div></div></div>', $this->area_sortable_buffer());
	}

	/*
	 * Used to allow for dropping at top of area, gets around floated element problems
	 */
	function area_sortable_buffer(){

		return (pl_draft_mode()) ? sprintf('<div class="pl-sortable pl-sortable-buffer span12 offset0"></div>') : '';
	}

	

	
	
}