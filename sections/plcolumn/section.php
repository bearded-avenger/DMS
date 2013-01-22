<?php
/*
	Section: Column
	Class Name: PLColumn
	Filter: layout	
*/

class PLColumn extends PageLinesSection {


	function section_template() { 

		?>
		<div class="pl-sortable-column pl-sortable-area editor-row">
			
			<?php
			
			render_nested_sections( $this->meta['content'] );
			
			?>
			<span class="pl-column-forcer">&nbsp;</span>
		</div>
	<?php 
	
	}

}