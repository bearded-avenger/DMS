<?php
/**
 * FOOTER
 *
 * This file controls the ending HTML </body></html> and common graphical
 * elements in your site footer. You can control what shows up where using
 * WordPress and PageLines PHP conditionals
 *
 * @package     PageLines Framework
 * @since       1.0
 *
 * @link        http://www.pagelines.com/
 * @link        http://www.pagelines.com/tour
 *
 * @author      PageLines   http://www.pagelines.com/
 * @copyright   Copyright (c) 2008-2012, PageLines  hello@pagelines.com
 *
 * @internal    last revised February November 21, 2011
 * @version     ...
 *
 * @todo Define version
 */

if(!has_action('override_pagelines_body_output')):
	
			pagelines_register_hook('pagelines_start_footer'); // Hook ?>
						</div>
						<?php pagelines_register_hook('pagelines_after_main'); // Hook ?>
						<div id="morefoot_area" class="container-group">
							<?php
								pagelines_template_area('pagelines_morefoot', 'morefoot'); // Hook
								pagelines_template_area('pagelines_page_footer', 'morefoot'); // Hook
							?>
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>

	<?php pagelines_register_hook('pagelines_before_footer'); // Hook ?>
			<footer id="footer" class="footer pl-region" data-region="footer">
				<div class="page-area outline pl-area-container fix">
				<?php
					pagelines_template_area('pagelines_footer', 'footer'); // Hook
					pagelines_register_hook('pagelines_after_footer'); // Hook
				?>
				</div>
			</footer>
		</div>
		
	</div>
	<?php if(!pl_is_pro()):?>
	<a class="pl-credit" href="http://www.pagelines.com/" title="Built with PageLines DMS [basic]" target="_blank" style="display: block; visibility: visible;">
		<i class="icon-pagelines pl-transit"></i> <span class="fademein">DMS</span>
	</a>
	<?php endif; ?>
	<div id="supersized"></div>
</div>
<?php

endif;

	print_pagelines_option('footerscripts'); // Load footer scripts option
	wp_footer(); // Hook (WordPress)
?>
</body>
</html>