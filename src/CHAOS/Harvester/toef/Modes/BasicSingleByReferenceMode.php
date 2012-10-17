<?php
namespace CHAOS\Harvester\toef\Modes;
use CHAOS\Harvester\Modes\SingleByReferenceMode;

class BasicSingleByReferenceMode extends SingleByReferenceMode {
	
	public function __construct($harvester, $name, $parameters = null) {
		$this->_harvester = $harvester;
		$this->_harvester->debug("A ".__CLASS__." named '$name' was constructing.");
	}
	
	public function execute($reference) {
		$toef = $this->_harvester->getExternalClient('toef');
		/* @var $toef \CHAOS\Harvester\toef\TOEFClient */
		$sight = $toef->sight(intval($reference));
		$this->_harvester->process('sight', $sight);
	}
}