<?php
namespace CHAOS\Harvester\toef\Processors;
class MainImageFileProcessor extends \CHAOS\Harvester\Processors\FileProcessor {
	
	const KULTURARV_BASE_PATH = 'http://www.kulturarv.dk/1001fortaellinger/';
	
	public function process($externalObject, $shadow = null) {
		$mainImage = $externalObject->mainImage->thumbnail;
		
		$urlBase = self::KULTURARV_BASE_PATH;
		$filenameMatches = array();
		if($mainImage->count() > 0 && preg_match("#$urlBase(.*)#", $mainImage[0], $filenameMatches) === 1) {
			$pathinfo = pathinfo($filenameMatches[1]);
			$shadow->fileShadows[] = $this->createFileShadow($pathinfo['dirname'], $pathinfo['basename']);
		} else {
			$this->_harvester->info("Couldn't find a main image for the sight.");
		}
		
		return $shadow;
	}
	
}