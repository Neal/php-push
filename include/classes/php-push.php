<?php

/**
 * php-push
 *
 * PHP wrapper for Push.co API: http://push.co/api
 *
 * @author Neal <neal@ineal.me>
 * @version 0.1
 * @package php-push
 * @example send-push.php
 * @link http://push.co/api
 * @license MIT License
 */

class Push {

	const VERSION = '0.1';

	const API_URL = 'http://api.push.co//1.0/';

	/** @var api_key */
	private $api_key;

	/** @var api_secret */
	private $api_secret;

	/** @var message */
	private $message;

	/** @var notification_type */
	private $notification_type;

	/** @var view_type */
	private $view_type;

	/** @var article */
	private $article;

	/** @var url */
	private $url;

	/** @var latitude */
	private $latitude;

	/** @var longitude */
	private $longitude;


	/**
	 * Constructor
	 *
	 * @param  ctype_xdigit  api_key
	 * @param  ctype_xdigit  api_secret
	 * @throws PushException if cURL extension is not loaded
	 * @throws PushException if $api_key is not a hexadecimal
	 * @throws PushException if $api_secret is not a hexadecimal
	 */
	public function __construct($api_key, $api_secret) {
		if (!extension_loaded('curl')) throw new PushException('PHP extension cURL is not loaded.');
		if (!ctype_xdigit($api_key)) throw new PushException('Expected $api_key to be a hexadecimal');
		if (!ctype_xdigit($api_secret)) throw new PushException('Expected $api_secret to be a hexadecimal');

		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
	}



	/**
	 * set_message
	 *
	 * The message you want to display in the Push Notification.
	 * Maximum amount of characters including whitespace should be 140.
	 *
	 * @param  string  message
	 * @throws PushException if $message is not a string
	 * @throws PushException if $message is greater than 140 characters
	 */
	public function set_message($message) {
		if (!is_string($message)) throw new PushException('Expected $message to be a string');
		if (strlen($message) > 140) throw new PushException('Expected $message to be less than 140 characters');

		$this->message = $message;
	}


	/**
	 * set_notification_type
	 *
	 * The Notification Type is the way to send Push Notifications to a group of subscriptions (and thus users).
	 * When you set the notification_type it will target all subscriptions with the same notification_type value stored.
	 *
	 * @param  string  notification_type
	 */
	public function set_notification_type($notification_type) {
		$this->notification_type = $notification_type;
	}


	/**
	 * set_view_type
	 *
	 * The view_type represents the way the endpoint view in the app gets displayed.
	 * Do you only want to show the notification? Do you want to present an article via URL in a WebView?
	 *
	 * Value: 0
	 *   Message View. Shows the notification with optional some additional content or image.
	 * Value: 1
	 *   Web View. Combined with the send URL, it'll display the contents in a UIWebView. In other words, the app will display the webpage specified in the URL.
	 * Value: 2
	 *   Map View. Place a pin with the message on a map.
	 *
	 * @param  int  view_type
	 * @throws PushException if $view_type is not a int
	 * @throws PushException if $view_type is not either 0, 1, 2
	 */
	public function set_view_type($view_type) {
		if (!is_int($view_type)) throw new PushException('Expected $view_type to be an int');
		if (!in_array($view_type, array(0, 1, 2))) throw new PushException('Expected $view_type to be either 0, 1, 2');

		$this->view_type = $view_type;
	}


	/**
	 * set_article
	 *
	 * Additional content for the message view. Could be a paragraph of an article for example.
	 * Will be placed under the notification message.
	 *
	 * @param  string  article
	 * @throws PushException if $article is not a string
	 */
	public function set_article($article) {
		if (!is_string($article)) throw new PushException('Expected $article to be a string');

		$this->article = $article;
	}


	/**
	 * set_image
	 *
	 * Additional image for the message view. You should supply an url pointing to an image.
	 * You can use it to spice the message view up. For the best results, the image should be big enough to support retina. (downscale by half)
	 *
	 * @param  string  image    image url
	 * @throws PushException if $image is not a string
	 */
	public function set_image($image) {
		if (!is_string($image)) throw new PushException('Expected $image to be a string');

		$this->image = $image;
	}


	/**
	 * set_url
	 *
	 * An url pointing to a webpage with content representing the notification.
	 * The UIWebView will load the url and show the content.
	 *
	 * @param  string  url
	 */
	public function set_url($url) {
		$this->url = $url;
	}


	/**
	 * set_latitude
	 *
	 * Latitude for the pin location on the map. If you don't specify a latitude and longitude it will drop a pin on the lovely village Slootdorp.
	 *
	 * @param  string  latitude
	 */
	public function set_latitude($latitude) {
		$this->latitude = $latitude;
	}


	/**
	 * set_longitude
	 *
	 * Longitude for the pin location on the map. If you don't specify a latitude and longitude it will drop a pin on the lovely village Slootdorp.
	 *
	 * @param  string  longitude
	 */
	public function set_longitude($longitude) {
		$this->longitude = $longitude;
	}


	/**
	 * push
	 *
	 * This method allows you the put a Push Notification in Push.co's queue,
	 * they will do the rest by sending it to the Push.co app.
	 *
	 * @throws PushException if $message is not set
	 * @throws PushException if $url is not set and $view_type is 1
	 */
	public function push() {
		if (!isset($this->message)) throw new PushException('"message" not set');
		if (!isset($this->view_type)) $this->view_type = 0;

		$post_data = array();

		$post_data['message'] = $this->message;
		$post_data['api_key'] = $this->api_key;
		$post_data['api_secret'] = $this->api_secret;

		if (isset($this->notification_type)) $post_data['notification_type'] = $this->notification_type;

		switch ($this->view_type) {
			case 0:
				if (isset($this->article)) $post_data['article'] = $this->article;
				if (isset($this->image)) $post_data['image'] = $this->image;
				break;

			case 1:
				if (!isset($this->url)) throw new PushException('"url" not set');
				$post_data['url'] = $this->url;
				break;

			case 2:
				if (isset($this->latitude)) $post_data['latitude'] = $this->latitude;
				if (isset($this->longitude)) $post_data['longitude'] = $this->longitude;
				break;
		}

		return $this->http_post('push', $post_data);
	}


	/**
	 * http_post
	 *
	 * Make a HTTP POST request
	 *
	 * @param  string  endpoint
	 * @param  array   post_data
	 */
	private function http_post($endpoint, $post_data = null) {

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_TIMEOUT => 10,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_FORBID_REUSE => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_VERBOSE => false,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post_data,
			CURLOPT_URL => self::API_URL . $endpoint,
			CURLOPT_USERAGENT => 'php-push ' . self::VERSION
		));

		$http_result = curl_exec($curl);
		curl_close($curl);

		return json_decode($http_result);
	}

}

class PushException extends Exception {}

?>
