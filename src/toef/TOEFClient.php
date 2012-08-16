<?php
namespace toef;
class TOEFClient {
	protected $_curlHandle;
	protected $_baseURL;
	protected $_key;
	const PAGE_SIZE = 10;
	
	public function __construct($baseURL, $key) {
		$this->_curlHandle = curl_init($baseURL);
		curl_setopt($this->_curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->_curlHandle, CURLOPT_FOLLOWLOCATION, true);
		$this->_baseURL = $baseURL;
		$this->_key = $key;
	}
	
	public function __destruct() {
		curl_close($this->_curlHandle);
	}
	
	public function request($handle, $arguments = array(), $format = "xml") {
		$data = array();
		foreach($arguments as $key => $value) {
			if($value !== null) {
				$data[$key] = $value;
			}
		}
		if(!array_key_exists('key', $data)) {
			$data['key'] = $this->_key;
		}
		$url = sprintf("%s%s.%s?%s", $this->_baseURL, $handle, $format, http_build_query($data));
		printf("Requesting: %s\n", $url);
		curl_setopt($this->_curlHandle, CURLOPT_URL, $url);
		$result = curl_exec($this->_curlHandle);
		if($result === false) {
			throw new RuntimeException("Problem occured while communicating with the service: ".curl_error($this->_curlHandle));
		} else {
			return simplexml_load_string($result);
		}
	}
	
	public function sights($page = null) {
		return $this->request('sights', array('page' => $page));
	}
	
	public function sight($reference) {
		if(is_int($reference)) {
			// $reference is assumed to be an integer id.
			$reference = 's/'.$reference;
		}
		// Remove the absolute part of the url, if any.
		$reference = str_replace($this->_baseURL, '', $reference);
		return $this->request($reference);
	}
}