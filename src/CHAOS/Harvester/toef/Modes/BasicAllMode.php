<?php
namespace CHAOS\Harvester\toef\Modes;
use CHAOS\Harvester\Modes\AllMode;

class BasicAllMode extends AllMode {
	
	public function __construct($harvester, $name, $parameters = null) {
		$this->_harvester = $harvester;
		$this->_harvester->debug("A ".__CLASS__." named '$name' was constructing.");
	}
	
	public function execute() {
		$toef = $this->_harvester->getExternalClient('toef');
		/* @var $toef \CHAOS\Harvester\toef\TOEFClient */
		$result = array();
		
		// TODO: Consider this might be a problem if the service changes.
		$page = 0;
		$offset = 0;
		// The webservice is one-indexed.
		$page += 1;
		
		$s = 1;
		
		do {
			timed();
			$response = $toef->sights($page);
			timed('toef');
			foreach($response->sight as $sight) {
				// Print the number of sightsthat we have processed so far.
				printf("[#%u] ", $s++);
				
				$sightShadow = $this->_harvester->process('sight', $sight);
				
				echo "\n";
			}
			$page++;
		} while($response->sight->count() > 0);
	}
}