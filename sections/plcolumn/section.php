<?php
/*
	Section: Column
	Class Name: PLColumn	
*/

class PLColumn extends PageLinesSection {


	function section_template() { 

		?>
		<div class="pl-column-sortable editor-row not-column-inherit">
			
			<?php
			
			render_nested_sections( $this->meta['content'] );
			
			?>
		</div>
	<?php 
	
	}

}