<?php



class PageLinesExtendPanel{

	function __construct(){

		add_filter('pl_toolbar_config', array(&$this, 'toolbar'));
		add_action('pagelines_editor_scripts', array(&$this, 'scripts'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){
		wp_enqueue_script( 'pl-js-extend', $this->url . '/js/pl.extend.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}

	function toolbar( $toolbar ){
		$toolbar['pl-extend'] = array(
			'name'	=> 'Store',
			'icon'	=> 'icon-download',
			'pos'	=> 80,
			'panel'	=> array(
				'heading'	=> "Extend PageLines",
				'store'		=> array(
					'name'	=> 'PageLines Store',
					'filter'=> '*',
					'type'	=> 'call',
					'call'	=> array(&$this, 'the_store_callback'),
					'icon'	=> 'icon-briefcase'
				),
				'heading2'	=> "<i class='icon-filter'></i> Filters",
				'plus'		=> array(
					'name'	=> 'Plus Extensions',
					'href'	=> '#store',
					'filter'=> '.plus',
					'icon'	=> 'icon-plus-sign'
				),
				'featured'		=> array(
					'name'	=> 'Featured',
					'href'	=> '#store',
					'filter'=> '.featured',
					'icon'	=> 'icon-star'
				),
				
				'sections'		=> array(
					'name'	=> 'Sections',
					'href'	=> '#store',
					'filter'=> '.sections',
					'icon'	=> 'icon-random'
				),
				'plugins'		=> array(
					'name'	=> 'Plugins',
					'href'	=> '#store',
					'filter'=> '.plugins',
					'icon'	=> 'icon-download-alt'
				),
				'themes'		=> array(
					'name'	=> 'Themes',
					'href'	=> '#store',
					'filter'=> '.themes',
					'icon'	=> 'icon-picture'
				),
				'free'		=> array(
					'name'	=> 'Free',
					'href'	=> '#store',
					'filter'=> '.free-item',
					'icon'	=> 'icon-tag'
				),
				'premium'		=> array(
					'name'	=> 'Premium',
					'href'	=> '#store',
					'filter'=> '.premium-item',
					'icon'	=> 'icon-shopping-cart'
				),
				'heading3'	=> "Tools",
				'upload'	=> array(
					'name'	=> 'Upload',
					'icon'	=> 'icon-upload',
					'call'	=> array(&$this, 'upload_callback'),
				),
				'search'	=> array(
					'name'	=> 'Search',
					'icon'	=> 'icon-search',
					'call'	=> array(&$this, 'search_callback'),
				),
			)
		);

		return $toolbar;
	}

	function upload_callback(){
			?>

			<form class="opt standard-form form-save-template">
				<fieldset>
					<span class="help-block">Upload a .zip extension into your PageLines install using this tool.</span>
					<label for="template-name">Extension File (zip file - required)</label>
					<input type="upload" id="template-name" name="template-name" required />
					<button type="submit" class="btn btn-primary btn-save-template">Upload Extension</button>
				</fieldset>
			</form>

			<?php
	}

	function search_callback(){
			?>

			<form class="opt standard-form form-save-template">
				<fieldset>
					<span class="help-block">Search the PageLines store for extensions.</span>

					<input class="" id="appendedInputButton" type="text">
					<button class="btn" type="button">Search!</button>

				</fieldset>
			</form>

			<?php
	}


	function the_store_callback(){

		$this->xlist = new EditorXList;

		$list = '';

		global $storeapi;
		$mixed_array = $storeapi->get_latest();
//plprint($mixed_array);
		foreach( $mixed_array as $key => $item){

			$class = $item['class_array'];

			$class[] = 'x-storefront';

			$img = sprintf('<img src="%s" style=""/>', $item['thumb']);

			$sub = ($item['price'] == 'free') ? __('Free!', 'pagelines') : '$'.$item['price'];

			$class[] = ($item['price'] == 'free') ? 'free-item' : 'premium-item';

			$args = array(
				'id'			=> $item['slug'],
				'class_array' 	=> $class,
				'data_array'	=> array(
					'store-id' 	=> $item['slug']
				),
				'thumb'			=> $item['thumb'],
				'splash'		=> $item['splash'],
				'name'			=> $item['name'],
				'sub'			=> $sub
			);

			$list .= $this->xlist->get_x_list_item( $args );


		}

		printf('<div class="x-list x-store" data-panel="x-store">%s</div>', $list);
	}


}