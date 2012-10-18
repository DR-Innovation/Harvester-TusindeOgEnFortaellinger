<?php
namespace toef;
use RuntimeException, Exception;
class TOEFSpeakExtractor extends \AChaosFileExtractor {
	
	const KULTURARV_BASE_PATH = 'http://www.kulturarv.dk/1001fortaellinger/';
	// uploads/images/
	
	public $_speakFormatID;
	
	public $_speakDestinationID;
	/**
	 * Process the DFI movieitem.
	 * @param \TOEFHarvester $harvester The Chaos client to use for the importing.
	 * @param stdClass $object Representing the DKA program in the Chaos service, of which the images should be added to.
	 * @param stdClass $sight The TOEF sight item.
	 * @return array An array of processed files.
	 */
	function process($harvester, $object, $sight, &$extras) {
		if($object == null) {
			throw new Exception("Cannot extract files from an empty object.");
		}
		
		$speakProcessed = array();
		$urlBase = self::KULTURARV_BASE_PATH;
		
		printf("\tUpdating the file for the speak: ");
		// Update the thumbnail.
		// TODO: Should maybe use some variation of the uri.
		$speak = strval($sight->speak);
		//$mainImage = $sight->mainImage->uri;
		$filenameMatches = array();
		if(preg_match("#$urlBase(.*)#", $speak, $filenameMatches) === 1) {
			$pathinfo = pathinfo($filenameMatches[1]);
			$response = $this->getOrCreateFile($harvester, $object, null, $this->_speakFormatID, $this->_speakDestinationID, $pathinfo['basename'], $pathinfo['basename'], $pathinfo['dirname']);
			
			if($response == null) {
				throw new RuntimeException("Failed to create the speak file.");
			} else {
				$speakProcessed[] = $response;
			}
		} else {
			printf("no speak detected:");
		}
		printf(" Done.\n");
		
		return $speakProcessed;
	}
}