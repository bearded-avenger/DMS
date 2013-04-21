<?php
/*
	Section: TwitterBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Loads twitter feed into the site footer
	Class Name: PageLinesTwitterBar
	Workswith: morefoot, footer
	Edition: Pro
	Filter: social
*/

/**
 * Twitter Feed Section
 *
 * Uses pagelines_get_tweets() to display the latest tweet in the morefoot area.
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesTwitterBar extends PageLinesSection {

	function section_opts(){
		$opts = array(
			array(
				'key'		=> 'twitterbar_info',
				'type' 		=> 'help',
				'help' 		=> __( 'Set up your Twitter account information under "settings" > "social".', 'pagelines' )
			),


		);


		return $opts;
	}

	/**
	* Section template.
	*/
	function section_template() {

		if( !pl_setting('twittername') ) :
			printf('<div class="tbubble"><div class="tbubble-pad">%s</div></div>', __('Set your Twitter account name in your settings to use the TwitterBar Section.', 'pagelines'));

			return;
		endif;

		$account = pl_setting('twittername');

		$twitter = sprintf(
			'<span class="twitter">%s &nbsp;&mdash;&nbsp;<a class="twitteraccount" href="http://twitter.com/#!/%s">%s</a></span>',
			pagelines_tweet_clickable( pagelines_get_tweets( $account, true ) ),
			$account,
			$account
		);

		printf('<div class="tbubble"><div class="tbubble-pad">%s</div></div>', $twitter);
	}
}