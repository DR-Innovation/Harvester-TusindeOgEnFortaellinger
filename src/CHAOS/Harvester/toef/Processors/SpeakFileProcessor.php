<?php
namespace CHAOS\Harvester\toef\Processors;
use CHAOS\Harvester\Shadows\ObjectShadow;
use \SimpleXMLElement;

class SpeakFileProcessor extends \CHAOS\Harvester\Processors\FileProcessor {
	
	const KULTURARV_BASE_PATH = 'http://www.kulturarv.dk/1001fortaellinger/';
	
	public function process($externalObject, $shadow = null) {
		$this->_harvester->debug(__CLASS__." is processing.");
		
		assert($externalObject instanceof SimpleXMLElement && $shadow instanceof ObjectShadow);
		
		$urlBase = self::KULTURARV_BASE_PATH;
		$speak = strval($externalObject->speak);
		$filenameMatches = array();
		if(preg_match("#$urlBase(.*)#", $speak, $filenameMatches) === 1) {
			$pathinfo = pathinfo($filenameMatches[1]);
			$shadow->fileShadows[] = $this->createFileShadow($pathinfo['dirname'], $pathinfo['basename']);
		}
		
		return $shadow;
	}
	
}