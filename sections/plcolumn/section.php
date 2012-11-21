<?php
/*
	Section: Column
	Class Name: PLColumn	
*/

class PLColumn extends PageLinesSection {


	function section_template() { 

		?>
		<div class="pl-column-sortable pl-sortable-area editor-row">
			
			<?php
			
			render_nested_sections( $this->meta['content'] );
			
			?>&nbsp;
		</div>
	<?php 
	
	}

}