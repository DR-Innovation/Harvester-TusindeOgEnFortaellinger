<?php
namespace toef;
use RuntimeException, Exception;
class TOEFImageExtractor extends \AChaosFileExtractor {
	const KULTURARV_BASE_PATH = 'http://www.kulturarv.dk/1001fortaellinger/';
	// uploads/images/
	
	public $_imageFormatID;
	public $_lowResImageFormatID;
	public $_thumbnailImageFormatID;
	
	public $_imageDestinationID;
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
		
		$imagesProcessed = array();
		$urlBase = self::KULTURARV_BASE_PATH;
		
		printf("\tUpdating the file for the main (thumbnail) image: ");
		// Update the thumbnail.
		// TODO: Should maybe use some variation of the uri.
		$mainImage = $sight->mainImage->thumbnail;
		//$mainImage = $sight->mainImage->uri;
		$filenameMatches = array();
		if($mainImage->count() > 0 && preg_match("#$urlBase(.*)#", $mainImage[0], $filenameMatches) === 1) {
			$pathinfo = pathinfo($filenameMatches[1]);
			$response = $this->getOrCreateFile($harvester, $object, null, $this->_thumbnailImageFormatID, $this->_imageDestinationID, $pathinfo['basename'], $pathinfo['basename'], $pathinfo['dirname']);
			
			if($response == null) {
				throw new RuntimeException("Failed to create the main image file.");
			} else {
				$imagesProcessed[] = $response;
			}
		} else {
			printf("no main image detected:");
		}
		printf(" Done.\n");
		
		printf("\tUpdating files for %u images:\t", $sight->images->image->count());
		//$this->resetProgress(count($images->PictureItem));
		//$progress = 0;
		echo self::PROGRESS_END_CHAR;
		
		foreach($sight->images->image as $i) {
			//$this->updateProgress($progress++);
			// The following line is needed as they forget to set their encoding.
			//$i->Caption = iconv( "UTF-8", "ISO-8859-1//TRANSLIT", $i->Caption );
			//echo "\$caption = $caption\n";
			//printf("\tFound an image with the caption '%s'.\n", $i->Caption);
			$miniImageID = null;
			$filenameMatches = array();
			if(preg_match("#$urlBase(.*)#", $i->original, $filenameMatches) === 1) {
				$pathinfo = pathinfo($filenameMatches[1]);
				// TODO: Uncomment to fix.
				//$pathinfo['dirname'] = '/' . $pathinfo['dirname'] . '/';
				
				$response = $this->getOrCreateFile($harvester, $object, null, $this->_imageFormatID, $this->_imageDestinationID, $pathinfo['basename'], $pathinfo['basename'], $pathinfo['dirname']);
			
				if($response == null) {
					throw new RuntimeException("Failed to create an image file.");
				} else {
					$imagesProcessed[] = $response;
					$miniImageID = $response->ID;
				}
			} else {
				printf("\tWarning: Found an images which was didn't have a scanpix/mini URL. This was not imported.\n");
			}
			
			$filenameMatches = array();
			if(preg_match("#$urlBase(.*)#", $i->thumbnail, $filenameMatches) === 1) {
				$pathinfo = pathinfo($filenameMatches[1]);
				$response = $this->getOrCreateFile($harvester, $object, $miniImageID, $this->_lowResImageFormatID, $this->_imageDestinationID, $pathinfo['basename'], $pathinfo['basename'], $pathinfo['dirname']);
					
				if($response == null) {
					throw new RuntimeException("Failed to create an image file.");
				} else {
					$imagesProcessed[] = $response;
				}
			} else {
				printf("\tWarning: Found an images which was didn't have a scanpix/mini URL. This was not imported.\n");
			}
		}
		echo self::PROGRESS_END_CHAR;
		
		printf(" Done\n");
		return $imagesProcessed;
	}
}