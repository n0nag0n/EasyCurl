<?php

	namespace n0nag0n;

	class EasyCurl {
		
		public static $last_request_info;
		
		public static function getRequest($url, $data = []) {
			return self::makeCall($url, $data);
		}
		
		public static function postRequest($url, $data = []) {
			$data['method'] = 'POST'; 
			return self::makeCall($url, $data);
		}

		public static function deleteRequest($url, $data = []) {
			$data['method'] = 'DELETE'; 
			return self::makeCall($url, $data);
		}

		public static function putRequest($url, $data = []) {
			$data['method'] = 'PUT'; 
			return self::makeCall($url, $data);
		}
		
		/**
		 * Do the dang curl call
		 * @param string $url
		 * @param string $data [ 'method' => 'GET', 'url' => required, 'params' => array, 'post_body' => string, 'is_json_request' => bool, 'authorization' => string, 'ignore_ssl' => bool ]
		 * @return boolean|json string
		 */
		private static function makeCall($url, $data = []) {
			$headers = [];
			if(!isset($data['method']) || empty($data['method'])) {
				$data['method'] = 'GET';
			}

			if(isset($data['params']) && $data['params']) {
				$data['params'] = http_build_query($data['params']);
			}
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $data['method']);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

			if($data['time_out']) {
				curl_setopt($curl, CURLOPT_TIMEOUT, $data['time_out']);
			} else {
				curl_setopt($curl, CURLOPT_TIMEOUT, 12);
			}

			if(isset($data['http_referrer'])) {
				curl_setopt($curl, CURLOPT_REFERER, $data['http_referrer']);
			}

			if(isset($data['userpwd']) && $data['userpwd']) {
				curl_setopt($curl, CURLOPT_USERPWD, $data['userpwd']);
			}

			if(isset($data['is_json_request']) && $data['is_json_request']) {
				$headers[] = 'Content-Type: application/json';
			}

			if(isset($data['accept_json']) && $data['accept_json']) {
				$headers[] = 'Accept: application/json';
			}

			if(isset($data['authorization']) && $data['authorization']) {
				$headers[] = 'Authorization: '.$data['authorization'];
			}

			if(isset($data['custom_headers']) && is_array($data['custom_headers']) && count($data['custom_headers'])) {
				$headers[] = array_merge($headers, $data['custom_headers']);
			}

			if(strtoupper($data['method']) === 'POST' || strtoupper($data['method']) === 'PUT') {
				curl_setopt($curl, CURLOPT_POST, 1);
				// params_array_raw is when things should not be passed through http_build_query like CURLFile
				$postfields = isset($data['params_array_raw']) && is_array($data['params_array_raw']) && count($data['params_array_raw']) ? $data['params_array_raw'] : $data['params'].$data['post_body'];
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
			}

			if(count($headers)) {
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			}

			if(isset($data['ignore_ssl']) && $data['ignore_ssl']) {
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			}

			$response = curl_exec($curl);
			
			self::$last_request_info = curl_getinfo($curl);
			$curl_error = curl_error($curl);
			$curl_errno = curl_errno($curl);
			
			if($curl_errno)
				throw new \Exception('cURL Error: "'.$curl_errno.' - '.$curl_error.'" cURL Info: '.print_r(curl_getinfo($curl), true));

			curl_close($curl);
			
			return $response;
		}
	}
