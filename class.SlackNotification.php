<?php

	/****************************************************************************************************************************************************************************
	 *
	 *	There are two classes in this file, just because they will almost always be used together so it seemed dumb to always include two files instead of one.
	 *
	 *	SlackNotification is a class to create and send a notification.
	 *	It expects a URL parameter, which is the Webhook URL from slack.
	 *	It also expects a SlackNotificationOptions object, which is the second class.
	 *	There is an optional third parameter for debug being true. It defaults to false.
	 *
	 *	Here is typical usage:
	 *	$options = new SlackNotificationOptions();
	 *	$options->setChannel($channelToPostIn);
	 *	$options->setText($textToPost);
	 *	$slack = new SlackNotification('https://hooks.slack.com/services/____/____/____',$options);
	 *	$slack->send();
	 *
	 ****************************************************************************************************************************************************************************/

	class SlackNotification {

		protected $url;
		protected $payload;
		protected $response;
		protected $info;

		protected $channel;
		protected $username;
		protected $icon_url;
		protected $icon_emoji;
		protected $text;
		protected $attachments;

		protected $debug;

		public function __construct($url = null,$options = null,$debug = false) {
			$this->debug = $debug;
			$this->url = $url;
			$this->channel = ($options !== null) ? $options->getChannel() : null;
			$this->username = ($options !== null) ? $options->getUsername() : null;
			$this->icon_url = ($options !== null) ? $options->getIconURL() : null;
			$this->icon_emoji = ($options !== null) ? $options->getIconEmoji() : null;
			$this->text = ($options !== null) ? $options->getText() : null;
			$this->attachments = ($options !== null) ? $options->getAttachments() : null;
			if($this->text !== null || $this->attachments != null) {
				$this->buildPayload();
			}
		}

		public function flush() {
			$this->url			= null;
			$this->payload		= null;
			$this->response		= null;
			$this->info			= null;
			$this->channel		= null;
			$this->username		= null;
			$this->icon_url		= null;
			$this->icon_emoji	= null;
			$this->text			= null;
			$this->attachments	= null;
		}

		private function buildPayload() {
			$this->payload = 'payload={"link_names":"1"';
			if($this->debug) {
				$this->payload.= ',"channel":"#testing"';
			} else if($this->channel !== null) {
				$this->payload.= ',"channel":"' . $this->channel . '"';
			}
			if($this->username !== null) {
				$this->payload.= ',"username":"' . $this->username . '"';
			}
			if($this->icon_url !== null) {
				$this->payload.= ',"icon_url":"' . $this->icon_url . '"';
			} else if($this->icon_emoji !== null) {
				$this->payload.= ',"icon_emoji":"' . $this->icon_emoji . '"';
			}
			if($this->text != null) {
				$textToEncode = $this->text;
				if($this->debug) {
					$textToEncode.= '\n' . "Would have posted to " . $this->channel;
				}
				$this->payload.= ',"text":"' . $textToEncode . '"';
			}
			if($this->attachments != null) {
				$this->payload.= ',"attachments":' . json_encode($this->attachments);
			}
			$this->payload.= '}';
		}

		public function send() {
			$ch = curl_init($this->url);
			try	{
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->payload);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				    'Accept: application/json',
				    'Content-Length: ' . strlen($this->payload))
				);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$this->response = curl_exec($ch);
				$this->info	= curl_getinfo($ch);
				curl_close($ch);
			}
			catch (Exception $e) {
				curl_close($ch);
				throw $e;
				return false;
			}
			if($this->debug) {
				$this->echoData();
			}
			return true;
		}

		private function echoData($verbose = false) {
			echo "<strong>Payload:</strong> ";
			print_r($this->payload);
			echo "<br><br><strong>Response:</strong> ";
			print_r($this->response);
			if($verbose) {
				echo "<br><br><strong>Info:</strong> ";
				print_r($this->info);
			}
		}

	}

	class SlackNotificationOptions {

		protected $channel;
		protected $username;
		protected $icon_url;
		protected $icon_emoji;
		protected $text;
		protected $attachments;

		public function __construct() {
			$this->channels		= null;
			$this->username		= null;
			$this->icon_url		= null;
			$this->icon_emoji	= null;
			$this->text			= null;
			$this->attachments	= null;
		}

		public function flush() {
			$this->channel		= null;
			$this->username		= null;
			$this->icon_url		= null;
			$this->icon_emoji	= null;
			$this->text			= null;
			$this->attachments	= null;
		}

		public function setChannel($channel) {
			$this->channel = $channel;
		}
		public function setUsername($username) {
			$this->username = $username;
		}
		public function setIconURL($icon_url) {
			$this->icon_url = $icon_url;
		}
		public function setIconEmoji($icon_emoji) {
			$this->icon_emoji = $icon_emoji;
		}
		public function setText($text) {
			$this->text = $text;
		}
		public function setAttachments($attachments) {
			$this->attachments = $attachments;
		}

		public function getChannel() {
			return $this->channel;
		}
		public function getUsername() {
			return $this->username;
		}
		public function getIconURL() {
			return $this->icon_url;
		}
		public function getIconEmoji() {
			return $this->icon_emoji;
		}
		public function getText() {
			return $this->text;
		}
		public function getAttachments() {
			return $this->attachments;
		}

	}
?>