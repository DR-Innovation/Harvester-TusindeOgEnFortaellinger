<?php
/**
 * This harvester connects to a OAI-PMH compliant webservice and
 * copies information on items into a Chaos service.
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify  
 * it under the terms of the GNU Lesser General Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or  
 * (at your option) any later version.  
 *
 * @author     Kræn Hansen (Open Source Shift) for the danish broadcasting corporation, innovations.
 * @license    http://opensource.org/licenses/LGPL-3.0	GNU Lesser General Public License
 * @version    $Id:$
 * @link       https://github.com/CHAOS-Community/Harvester-OAI-PMH
 * @since      File available since Release 0.1
 */

require "bootstrap.php";
use toef\TOEFClient;

/**
 * Main class of the OAI-PMH Harvester.
 *
 * @author     Kræn Hansen (Open Source Shift) for the danish broadcasting corporation, innovations.
 * @license    http://opensource.org/licenses/LGPL-3.0	GNU Lesser General Public License
 * @version    Release: @package_version@
 * @link       https://github.com/CHAOS-Community/Harvester-OAI-PMH
 * @since      Class available since Release 0.1
 */
class TOEFHarvester extends AChaosImporter {
	
	/**
	 * The client to use when communicating with the OAI-PMH service.
	 * @var TOEFClient
	 */
	protected $_toef;
	
	/**
	 * The base url of the external 1001 Fortællinger REST webservice.
	 * @var string
	 */
	protected $_TOEFBaseUrl;
	
	/**
	 * The key to authenticate towards the external 1001 Fortællinger REST webservice.
	 * @var string
	 */
	protected $_TOEFKey;
	
	/**
	 * The object type of a chaos object, to be used later.
	 * Populated when AChaosImporter::loadConfiguration is called.
	 * @var string
	 */
	protected $_objectTypeID;
	
	/**
	 * The ID of the format to be used when linking images to a DKA Program.
	 * Populated when AChaosImporter::loadConfiguration is called.
	 * @var string
	 */
	protected $_imageFormatID;
	
	/**
	 * The ID of the format to be used when linking lowres-images to a DKA Program.
	 * Populated when AChaosImporter::loadConfiguration is called.
	 * @var string
	 */
	protected $_lowResImageFormatID;
	
	/**
	 * The ID of the format to be used when linking thumbnail.images to a DKA Program.
	 * Populated when AChaosImporter::loadConfiguration is called.
	 * @var string
	 */
	protected $_thumbnailImageFormatID;
	
	/**
	 * The ID of the format to be used when linking images to a DKA Program.
	 * Populated when AChaosImporter::loadConfiguration is called.
	 * @var string
	 */
	protected $_imageDestinationID;
	
	/**
	 * The ID of the format to be used when linking videos to a DKA Program.
	 * Populated when AChaosImporter::loadConfiguration is called.
	 * @var string
	 */
	protected $_speakFormatID;
	
	/**
	 * The ID of the format to be used when linking videos to a DKA Program.
	 * Populated when AChaosImporter::loadConfiguration is called.
	 * @var string
	 */
	protected $_speakDestinationID;
	
	/**
	 * Constructor for the DFI Harvester
	 * @throws RuntimeException if the Chaos services are unreachable or
	 * if the Chaos credentials provided fails to authenticate the session.
	 */
	public function __construct($args) {
		// Adding configuration parameters
		$this->_CONFIGURATION_PARAMETERS["TOEF_BASE_URL"] = "_TOEFBaseUrl";
		$this->_CONFIGURATION_PARAMETERS["TOEF_KEY"] = "_TOEFKey";
		$this->_CONFIGURATION_PARAMETERS["CHAOS_DKA_OBJECT_TYPE_ID"] = "_objectTypeID";
		
		$this->_CONFIGURATION_PARAMETERS["CHAOS_TOEF_IMAGE_FORMAT_ID"] = "_imageFormatID";
		$this->_CONFIGURATION_PARAMETERS["CHAOS_TOEF_LOWRES_IMAGE_FORMAT_ID"] = "_lowResImageFormatID";
		$this->_CONFIGURATION_PARAMETERS["CHAOS_TOEF_THUMBNAIL_IMAGE_FORMAT_ID"] = "_thumbnailImageFormatID";
		$this->_CONFIGURATION_PARAMETERS["CHAOS_TOEF_IMAGE_DESTINATION_ID"] = "_imageDestinationID";
		
		$this->_CONFIGURATION_PARAMETERS["CHAOS_TOEF_SPEAK_FORMAT_ID"] = "_speakFormatID";
		$this->_CONFIGURATION_PARAMETERS["CHAOS_TOEF_SPEAK_DESTINATION_ID"] = "_speakDestinationID";
		
		// Adding xml generators.
		$this->_metadataGenerators[] = new XSLTMetadataGenerator('../stylesheets/DKA2.xsl', '5906a41b-feae-48db-bfb7-714b3e105396');
		$this->_metadataGenerators[] = new XSLTMetadataGenerator('../stylesheets/DKA.xsl', '00000000-0000-0000-0000-000063c30000');
		// Adding file extractors.
		$this->_fileExtractors['image'] = new toef\TOEFImageExtractor();
		$this->_fileExtractors['speak'] = new toef\TOEFSpeakExtractor();
		
		parent::__construct($args);
		$this->TOEF_initialize();
		//$this->testXMLGenerator();
	}
	
	function TOEF_initialize() {
		$this->_toef = new TOEFClient($this->_TOEFBaseUrl, $this->_TOEFKey);
		
		$this->_fileExtractors['image']->_imageFormatID = $this->_imageFormatID;
		$this->_fileExtractors['image']->_lowResImageFormatID = $this->_lowResImageFormatID;
		$this->_fileExtractors['image']->_thumbnailImageFormatID = $this->_thumbnailImageFormatID;
		$this->_fileExtractors['image']->_imageDestinationID = $this->_imageDestinationID;
		$this->_fileExtractors['speak']->_speakDestinationID = $this->_speakDestinationID;
		$this->_fileExtractors['speak']->_speakFormatID = $this->_speakFormatID;
	}
	
	protected function fetchRange($start, $count = null) {
		$result = array();
		// TODO: Consider this might be a problem if the service changes.
		$page = floor($start / TOEFClient::PAGE_SIZE);
		$offset = $page * TOEFClient::PAGE_SIZE;
		// The webservice is one-indexed.
		$page += 1;
		
		do {
			$response = $this->_toef->sights($page);
			foreach($response->sight as $sight) {
				if($offset < $start) {
					$offset++;
					continue;
				} elseif ($count == null || $offset < $count) {
					$result[] = $sight;
					$offset++;
				} else {
					// All done
					break 2;
				}
			}
			$page++;
		} while($response->sight->count() > 0);
		
		return $result;
	}
	
	protected function fetchSingle($reference) {
		return $this->_toef->sight($reference);
	}
	
	protected function externalObjectToString($externalObject) {
		return strval($externalObject->title);
	}
	
	protected function initializeExtras($sight, &$extras) {
		$extras = array('fileTypes' => array());
		if($sight->images->count() > 0) {
			$extras['fileTypes'][] = 'Picture';
		}
		if(strval($sight->speak) != '') {
			$extras['fileTypes'][] = 'Sound';
		}
		$extras['fileTypes'] = implode(', ', $extras['fileTypes']);
		$extras['id'] = strval($sight->id);
	}
	
	protected function shouldBeSkipped($externalObject) {
		return false;
	}
	
	protected function generateChaosQuery($externalObject) {
		if($externalObject == null) {
			throw new RuntimeException("Cannot get or create a Chaos object from a null external object.");
		}
		$id = strval($externalObject->id);
		
		$folderId = $this->_ChaosFolderID;
		$objectTypeId = $this->_objectTypeID;
		// Extract the nummeric ID.
		$nummericId = explode('/', $id);
		$nummericId = $nummericId[count($nummericId)-1];
		// Query for a Chaos Object that represents the DFI movie.
		$old = sprintf('(DKA-Organization:"%s" AND ObjectTypeID:%u AND m00000000-0000-0000-0000-000063c30000_da_all:"%s")', 'Kulturarvsstyrelsen', $objectTypeId, $nummericId);
		$new = sprintf('(FolderTree:%u AND ObjectTypeID:%u AND DKA-ExternalIdentifier:"%s")', $folderId, $objectTypeId, $id);
		return sprintf('(%s OR %s)', $new, $old);
	}
	
	protected function getChaosObjectTypeID() {
		return $this->_objectTypeID;
	}
	
	public function getExternalClient() {
		return $this->_toef;
	}
}

// Call the main method of the class.
TOEFHarvester::main($_SERVER['argv']);
