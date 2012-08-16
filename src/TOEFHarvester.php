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
	 * Constructor for the DFI Harvester
	 * @throws RuntimeException if the Chaos services are unreachable or
	 * if the Chaos credentials provided fails to authenticate the session.
	 */
	public function __construct($args) {
		// Adding configuration parameters
		$this->_CONFIGURATION_PARAMETERS["TOEF_BASE_URL"] = "_TOEFBaseUrl";
		$this->_CONFIGURATION_PARAMETERS["TOEF_KEY"] = "_TOEFKey";
		// Adding xml generators.
		/*
		$this->_metadataGenerators[] = dfi\dka\DKAMetadataGenerator::instance();
		$this->_metadataGenerators[] = dfi\dka\DKA2MetadataGenerator::instance();
		$this->_metadataGenerators[] = dfi\dka\DFIMetadataGenerator::instance();
		*/
		// Adding file extractors.
		/*
		$this->_fileExtractors[] = dfi\DFIImageExtractor::instance();
		$this->_fileExtractors[] = dfi\DFIVideoExtractor::instance();
		*/
		
		parent::__construct($args);
		$this->TOEF_initialize();
		$this->testXMLGenerator();
	}
	
	function TOEF_initialize() {
		$this->_toef = new TOEFClient($this->_TOEFBaseUrl, $this->_TOEFKey);
	}
	
	protected function fetchRange($start, $count) {
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
				} elseif ($offset < $count) {
					$result[] = $sight;
					$offset++;
				} else {
					// All done
					break 2;
				}
			}
			$page++;
		} while(false);
		
		return $result;
	}
	
	protected function fetchSingle($reference) {
		$this->_toef->sight($reference);
	}
	
	protected function externalObjectToString($externalObject) {
		return strval($externalObject->title);
	}
	
	protected function getOrCreateObject($externalObject) {
		throw new RuntimeException(__METHOD__. " not implemented.");
	}
	
	protected function initializeExtras($externalObject, &$extras) {
		var_dump($externalObject);
	}
	
	protected function shouldBeSkipped($externalObject) {
		return false;
	}
	
	protected function generateChaosQuery($externalObject) {
		return "";
	}
	
	protected function getChaosObjectTypeID() {
		return false;
	}
	
	public function getExternalClient() {
		return $this->_toef;
	}
	
	public function testXMLGenerator() {
		print("Testing the XML XSLT Generator.\n");
		
		print("Fetch an external object.\n");
		$sight = $this->_toef->sight(1230);
		if($sight === false) {
			print("Couldn't fetch a sigle sight.");
			exit;
		}
		
		$extras = array('fileTypes' => array());
		if(count($sight->images) > 0) {
			$extras['fileTypes'][] = 'Picture';
		}
		if(strval($sight->speak) != '') {
			$extras['fileTypes'][] = 'Sound';
		}
		$extras['fileTypes'] = implode(', ', $extras['fileTypes']);
		
		print("Initializing the generators.\n");
		$generator1 = new XSLTMetadataGenerator('../stylesheets/DKA.1001Fortællinger.Sight.xsl');
		$generator2 = new XSLTMetadataGenerator('../stylesheets/DKA2.xsl');
		
		print("Generating metadata #1.\n");
		$result = $generator1->generateXML($sight, $extras);
		
		$result->formatOutput = true;
		echo $result->saveXML();
		
		print("Generating metadata #2.\n");
		$result = $generator2->generateXML($sight, $extras);
		
		$result->formatOutput = true;
		echo $result->saveXML();
		
		exit;
	}
	
	static public function generateObjectType($images, $speak) {
		return "OK";
	}
}

// Call the main method of the class.
TOEFHarvester::main($_SERVER['argv']);
