<?php
/**
 * Tests functions admin/post-meta.php.
 *
 * @since 1.0.0
 * @package TenUp\Auto_Tweet
 */

namespace TenUp\Auto_Tweet\Tests;

use WP_UnitTestCase;

use const TenUp\Auto_Tweet\Core\Post_Meta\TWITTER_STATUS_KEY;

use function TenUp\Auto_Tweet\Core\Post_Meta\get_tweet_status_message;

/**
 * TestUtils class.
 *
 * @sincd 1.0.0
 */
class TestPostMeta extends WP_UnitTestCase {
	public function test_get_tweet_status_message() {
		$this->assertEquals(
			[
				'message' => '',
			],
			get_tweet_status_message( -1 )
		);


		$post = $this->factory->post->create( [ 'status' => 'publish' ] );
		
		$published_filter = function( $data, $id, $key ) use ( $post ) {
			if ( intval( $post ) === intval( $id ) && $key === TWITTER_STATUS_KEY ) {
				return [
					'status'     => 'published',
					'created_at' => '2017-01-01',
					'twitter_id' => 444,
				];
			}

			return $data;
		};
		add_filter( 'autotweet_meta', $published_filter, 10, 3 );
		$this->assertEquals(
			[
				'message' => 'Tweeted on 2017-01-01 @ 12:00AM',
				'url'     => 'https://twitter.com//status/444',
			],
			get_tweet_status_message( $post )
		);
		remove_filter( 'autotweet_meta', $published_filter );

		$failed_filter = function( $data, $id, $key ) use ( $post ) {
			if ( intval( $post ) === intval( $id ) && $key === TWITTER_STATUS_KEY ) {
				return [
					'status'     => 'error',
					'message'    => 'There was an error.'
				];
			}

			return $data;
		};
		add_filter( 'autotweet_meta', $failed_filter, 10, 3 );
		$this->assertEquals(
			[
				'message' => 'Failed to tweet: There was an error.',
			],
			get_tweet_status_message( $post )
		);
		remove_filter( 'autotweet_meta', $failed_filter );

		$unknown_filter = function( $data, $id, $key ) use ( $post ) {
			if ( intval( $post ) === intval( $id ) && $key === TWITTER_STATUS_KEY ) {
				return [
					'status'     => 'unknown',
					'message'    => 'There was an error.'
				];
			}

			return $data;
		};
		add_filter( 'autotweet_meta', $unknown_filter, 10, 3 );
		$this->assertEquals(
			[
				'message' => 'There was an error.',
			],
			get_tweet_status_message( $post )
		);
		remove_filter( 'autotweet_meta', $unknown_filter );

		$other_filter = function( $data, $id, $key ) use ( $post ) {
			if ( intval( $post ) === intval( $id ) && $key === TWITTER_STATUS_KEY ) {
				return [
					'status'     => 'other',
				];
			}

			return $data;
		};
		add_filter( 'autotweet_meta', $other_filter, 10, 3 );
		$this->assertEquals(
			[
				'message' => 'This post was not tweeted.',
			],
			get_tweet_status_message( $post )
		);
		remove_filter( 'autotweet_meta', $other_filter );
	}
	
}
