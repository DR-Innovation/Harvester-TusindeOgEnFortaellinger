<?php
namespace CHAOS\Harvester\toef\Processors;
use CHAOS\Harvester\Shadows\ObjectShadow;
use CHAOS\Harvester\Shadows\FileShadow;
use \SimpleXMLElement;

class LowresImageFileProcessor extends \CHAOS\Harvester\Processors\FileProcessor {
	
	// const KULTURARV_BASE_PATH = 'http://www.kulturarv.dk/1001fortaellinger/';
	
	public function process($externalObject, &$shadow = null) {
		$this->_harvester->debug(__CLASS__." is processing.");
		
		assert($externalObject instanceof SimpleXMLElement && $shadow instanceof ObjectShadow);
		/*
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
				
				// Update the extras.
				if(!in_array('Image', $shadow->extras['fileTypes'])) {
					$shadow->extras['fileTypes'][] = 'Image';
				}
			} else {
				trigger_error("Found an image with unknown URL.\n", E_USER_WARNING);
			}
			$position++;
		}
		*/
		$position = 0;
		foreach($externalObject->images->image as $i) {
			$fileShadow = $this->createFileShadowFromURL($i->thumbnail);
			if($fileShadow) {
				// Find the highres version of this file.
				$fileShadow->parentFileShadow = $shadow->fileShadows[$position];
				
				// Add it to the shadows.
				$shadow->fileShadows[] = $fileShadow;
				
				// Update the extras.
				if(!in_array('Image', $shadow->extras['fileTypes'])) {
					$shadow->extras['fileTypes'][] = 'Image';
				}
			} else {
				$this->_harvester->info("Skipping the file %s as it seems to not exist or no destination can be used.", $i->thumbnail);
			}
			$position++;
		}
	
		return $shadow;
		
		/*
		// Find the highres version of this file.
		*/
	}
	
}