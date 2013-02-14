<?php
namespace CHAOS\Harvester\toef\Modes;
use CHAOS\Harvester\Modes\SingleByReferenceMode;

class BasicSingleByReferenceMode extends SingleByReferenceMode {
	
	public function execute($reference) {
		$toef = $this->_harvester->getExternalClient('toef');
		$reference = is_numeric($reference) ? intval($reference) : $reference;
		
		$this->_harvester->info("Fetching external object of %s.", $reference);
		/* @var $toef \CHAOS\Harvester\toef\TOEFClient */
		$sight = $toef->sight($reference);
		$sightAttributes = $sight->attributes();
		if(strval($sightAttributes['code']) != '404') {
			$sightShadow = $this->_harvester->process('sight', $sight);
		} else {
			throw new \RuntimeException("Invalid reference: Got a 404 not found from the service.");
		}
	}
}