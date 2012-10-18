<?php
namespace CHAOS\Harvester\toef\Processors;
use CHAOS\Harvester\Shadows\ObjectShadow;
use CHAOS\Harvester\Shadows\FileShadow;
use \SimpleXMLElement;

class LowresImageFileProcessor extends \CHAOS\Harvester\Processors\FileProcessor {
	
	const KULTURARV_BASE_PATH = 'http://www.kulturarv.dk/1001fortaellinger/';
	
	public function process($externalObject, $shadow = null) {
		$this->_harvester->debug(__CLASS__." is processing.");
		
		assert($externalObject instanceof SimpleXMLElement && $shadow instanceof ObjectShadow);

		$position = 0;
		$urlBase = self::KULTURARV_BASE_PATH;
		foreach($externalObject->images->image as $i) {
			$filenameMatches = array();
			if(preg_match("#$urlBase(.*)#", $i->thumbnail, $filenameMatches) === 1) {
				$pathinfo = pathinfo($filenameMatches[1]);
				$fileShadow = $this->createFileShadow($pathinfo['dirname'], $pathinfo['basename']);
				
				// Find the highres version of this file.
				$fileShadow->parentFileShadow = $shadow->fileShadows[$position];
				
				$shadow->fileShadows[] = $fileShadow;
			} else {
				trigger_error("Found an image with unknown URL.\n", E_USER_WARNING);
			}
			$position++;
		}
	
		return $shadow;
		
		/*
		// Find the highres version of this file.
		*/
	}
	
}