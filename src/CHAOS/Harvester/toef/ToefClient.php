<?php
namespace CHAOS\Harvester\toef;
class ToefClient extends \toef\TOEFClient implements \CHAOS\Harvester\IExternalClient {
	
	/**
	 * A reference to the harvester.
	 * @var \CHAOS\Harvester\ChaosHarvester
	 */
	protected $harvester;
	
	protected $_parameters;
	
	public function __construct($harvester, $name, $parameters = array()) {
		parent::__construct($parameters['URL'], $parameters['key']);
		
		$this->_harvester = $harvester;
		$this->_harvester->debug("A ".__CLASS__." named '$name' was constructing.");
		
		$this->_parameters = $parameters;
		if(key_exists('URL', $this->_parameters)) {
			$this->_baseURL = $this->_parameters['URL'];
		}
		if(key_exists('key', $this->_parameters)) {
			$this->_key = $this->_parameters['key'];
		}
	}
	
	public function sanityCheck() {
		$result = parent::sanityCheck();
		if($result === true) {
			$this->_harvester->info("%s successfully responded.", $this->_baseURL);
		}
		return $result;
	}
	
	public function request($handle, $arguments = array(), $format = "xml") {
		timed();
		$result = parent::request($handle, $arguments, $format);
		timed('toef');
		return $result;
	}
}