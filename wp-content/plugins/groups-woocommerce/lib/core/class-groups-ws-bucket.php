<?php
/**
 * class-groups-ws-bucket.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author Karim Rahimpur
 * @package groups-woocommerce
 * @since groups-woocommerce 1.3.0
 */

/**
 * For atomic operations on user group membership termination timestamp buckets.
 * 
 * Usage:
 * 
 * $b = new Groups_WS_Bucket( $user_id, $group_id );
 * $b->acquire();
 * $content = $b->content;
 * // do stuff with $content
 * $b->content = $content;
 * $b->release();
 */
class Groups_WS_Bucket {

	/**
	 * Lock filename.
	 * @var string
	 */
	const BUCKET = 'bucket';

	/**
	 * Bucket's user ID.
	 * @var int
	 */
	private $user_id = null;

	/**
	 * Bucket's group ID.
	 * @var int
	 */
	private $group_id = null;

	/**
	 * File pointer.
	 * @var int
	 */
	private $h = null;

	/**
	 * Creates the bucket lock file if it doesn't exist.
	 */
	private static function check_bucket_file() {
		$exists = false;
		if ( !file_exists( GROUPS_WS_CORE_LIB . '/' . self::BUCKET ) ) {
			if ( $h = @fopen( GROUPS_WS_CORE_LIB . '/' . self::BUCKET, 'w' ) ) {
				@fclose( $h );
				$exists = true;
			} else {
				error_log( __METHOD__ . ' could not create bucket file' );
			}
		} else {
			$exists = true;
		}
	}

	/**
	 * Create a bucket instance for the user and group.
	 * @param int $user_id
	 * @param int $group_id 
	 */
	public function __construct( $user_id, $group_id ) {
		$this->user_id  = $user_id;
		$this->group_id = $group_id;
	}

	/**
	 * Acquire a lock on the bucket.
	 * Every call to this method must be followed by a call to release() after
	 * work on the bucket has been done.
	 * @return boolean true if a lock on the bucket could be acquired, false on failure
	 */
	public function acquire() {
		$acquired = false;
		if ( self::check_bucket_file() ) {
			if ( $this->h = @fopen( GROUPS_WS_CORE_LIB . '/' . self::BUCKET, 'r+' ) ) {
				if ( flock( $this->h, LOCK_EX ) ) {
					$acquired = true;
				} else {
					error_log( __METHOD__ . ' could not acquire lock on bucket file' );
				}
			} else {
				error_log( __METHOD__ . ' could not open bucket file' );
			}
		}
		return $acquired;
	}

	/**
	 * Release the lock on the bucket.
	 * @return boolean true if the lock on the bucket could be released, false on failure
	 */
	public function release() {
		$released = false;
		if ( $this->h !== null ) {
			if ( self::check_bucket_file() ) {
				if ( flock( $this->h, LOCK_UN ) ) {
					$released = true;
				}
				@fclose( $this->h );
				$this->h = null;
			}
		}
		return $released;
	}

	/**
	 * Return a property, one of int user_id, int group_id, array of int content.
	 * @param string $name property name
	 * @return mixed
	 */
	public function __get( $name ) {
		$result = null;
		switch( $name ) {
			case 'user_id' :
			case 'group_id' :
				$result = $this->$name;
				break;
			case 'content' :
				$user_buckets = get_user_meta( $this->user_id, '_groups_buckets', true );
				if ( !is_array( $user_buckets ) ) {
					$user_buckets = array();
				}
				$content = array();
				if ( isset( $user_buckets[$this->group_id] ) ) {
					$content = array_map( 'intval', $user_buckets[$this->group_id] );
				}
				$result = $content;
				break;
		}
		return $result;
	}

	/**
	 * Set a property, one of int user_id, int group_id, array content.
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set( $name, $value ) {
		switch ( $name ) {
			case 'user_id' :
			case 'group_id' :
				$this->$name = intval( $value );
				break;
			case 'content' :
				if ( is_array( $value ) ) {
					$user_buckets = get_user_meta( $this->user_id, '_groups_buckets', true );
					if ( !is_array( $user_buckets ) ) {
						$user_buckets = array();
					}
					$user_buckets[$this->group_id] = $value;
					delete_user_meta( $this->user_id, '_groups_buckets' );
					add_user_meta( $this->user_id, '_groups_buckets', $user_buckets );
				}
				break; 
		}
	}
}
