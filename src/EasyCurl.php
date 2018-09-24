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
		
		/**
		 * Do the dang curl call
		 * @param string $url
		 * @param string $data [ 'method' => 'GET', 'url' => required, 'params' => array, 'post_body' => string, 'is_json_request' => bool, 'authorization' => string, 'ignore_ssl' => bool ]
		 * @return boolean|json string
		 */
		private static function makeCall($url, $data = []) {
			$headers = [];
			if(!$data['method'])
				$data['method'] = 'GET';
			
			if($data['params'])
				$data['params'] = http_build_query($data['params']);
			
		    $curl = curl_init($url);
		    curl_setopt($curl, CURLOPT_HEADER, 0);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $data['method']);
			curl_setopt($curl, CURLOPT_TIMEOUT, 6);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

			if($data['is_json_request'])
				$headers[] = 'Content-Type: application/json';

			if($data['authorization'])
				$headers[] = 'Authorization: '.$data['authorization'];
			
			if(strtoupper($data['method']) == 'POST') {
			    curl_setopt($curl, CURLOPT_POST, 1);
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $data['params'].$data['post_body']);
			}
			if(count($headers))
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

			if($data['ignore_ssl']) {
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			}
		    $response = curl_exec($curl);
			
			self::$last_request_info = curl_getinfo($curl);
			$curl_error = curl_error($curl);
			$curl_errno = curl_errno($curl);
			
			if($curl_errno)
				return Crap::message('cURL Error: "'.$curl_errno.' - '.$curl_error.'" cURL Info: '.print_r(curl_getinfo($curl), true));

		    curl_close($curl);
			
		    return $response;
		}
	}
