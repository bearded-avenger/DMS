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
    function section_styles() {
 
        wp_enqueue_script( 'twitter', $this->base_url.'/twitter.js', array( 'pagelines-bootstrap-all' ), null, true );
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

		$tweet_data = pagelines_get_tweets( $account, true );

		if( isset( $tweet_data['text'] ) )
			$twitter = sprintf(
				'<span class="twitter">%s &nbsp;&mdash;&nbsp;<a class="twitteraccount" href="http://twitter.com/#!/%s" %s>%s</a></span>',
				pagelines_tweet_clickable( $tweet_data['text'] ),
				$account,
				sprintf( 'rel="twitterpopover" data-img="https://api.twitter.com/1/users/profile_image?user_id=%s&size=bigger" data-original-title="@%s"', $tweet_data['user']['id'], $account ),
				$account
			);
		else
			$twitter = sprintf( '<span class="twitter">%s</span>', __( 'Twitter error.', 'pagelines' ) );

		printf('<div class="tbubble"><div class="tbubble-pad">%s</div></div>', $twitter);
	}
}
