<?php
namespace CHAOS\Harvester\toef\Processors;
use CHAOS\Harvester\Shadows\ObjectShadow;
use \SimpleXMLElement;

class SpeakFileProcessor extends \CHAOS\Harvester\Processors\FileProcessor {
	
	// const KULTURARV_BASE_PATH = 'http://www.kulturarv.dk/1001fortaellinger/';
	
	public function process(&$externalObject, &$shadow = null) {
		$this->_harvester->debug(__CLASS__." is processing.");
		
		assert($externalObject instanceof SimpleXMLElement && $shadow instanceof ObjectShadow);
		
		/*
		$urlBase = self::KULTURARV_BASE_PATH;
		$speak = strval($externalObject->speak);
		$filenameMatches = array();
		if(preg_match("#$urlBase(.*)#", $speak, $filenameMatches) === 1) {
			$pathinfo = pathinfo($filenameMatches[1]);
			$shadow->fileShadows[] = $this->createFileShadow($pathinfo['dirname'], $pathinfo['basename']);
			// Update the extras.
			if(!in_array('Sound', $shadow->extras['fileTypes'])) {
				$shadow->extras['fileTypes'][] = 'Sound';
			}
		}
		*/
		
		$speak = strval($externalObject->speak);
		$fileShadow = $this->createFileShadowFromURL($speak);
		if($fileShadow) {
			// Add it to the shadows.
			$shadow->fileShadows[] = $fileShadow;
			
			// Update the extras.
			if(!in_array('Sound', $shadow->extras['fileTypes'])) {
				$shadow->extras['fileTypes'][] = 'Sound';
			}
		} else {
			$this->_harvester->info("Skipping the file %s as it seems to not exist or no destination can be used.", $speak);
		}
		
		return $shadow;
	}
	
}