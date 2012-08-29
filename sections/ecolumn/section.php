<?php
/*
	Section: Column
	Class Name: eColumn	
*/

class eColumn extends PageLinesSection {


	function section_template() { 
		
		?>
		<div class="ecolumn-inner pl_sortable_area editor-row not-column-inherit">
			
			<?php
			
			render_nested_sections( $this->meta['content'] );
			
			?>
		</div>
	<?php 
	
	}

}