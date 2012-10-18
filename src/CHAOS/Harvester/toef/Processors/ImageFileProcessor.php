<?php
namespace CHAOS\Harvester\toef\Processors;
use CHAOS\Harvester\Shadows\ObjectShadow;
use \SimpleXMLElement;

class ImageFileProcessor extends \CHAOS\Harvester\Processors\FileProcessor {
	
	const KULTURARV_BASE_PATH = 'http://www.kulturarv.dk/1001fortaellinger/';
	
	public function process($externalObject, $shadow = null) {
		$this->_harvester->debug(__CLASS__." is processing.");
		
		assert($externalObject instanceof SimpleXMLElement && $shadow instanceof ObjectShadow);
		
		$urlBase = self::KULTURARV_BASE_PATH;
		foreach($externalObject->images->image as $i) {
			$filenameMatches = array();
			if(preg_match("#$urlBase(.*)#", $i->original, $filenameMatches) === 1) {
				$pathinfo = pathinfo($filenameMatches[1]);
				$shadow->fileShadows[] = $this->createFileShadow($pathinfo['dirname'], $pathinfo['basename']);
			} else {
				trigger_error("Found an image with unknown URL.\n", E_USER_WARNING);
			}
		}
	
		return $shadow;
	}
	
}