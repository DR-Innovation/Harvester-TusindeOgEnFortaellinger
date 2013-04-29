<?php
namespace CHAOS\Harvester\toef\Processors;
use CHAOS\Harvester\Shadows\ObjectShadow;

class SightObjectProcessor extends \CHAOS\Harvester\Processors\ObjectProcessor {
	
	protected function generateQuery($externalObject) {
		// Extract the nummeric ID.
		$nummericId = explode('/', strval($externalObject->id));
		$nummericId = $nummericId[count($nummericId)-1];
		$legacyQuery = sprintf('((DKA-Organization:"%s" OR DKA-Organization:"%s") AND ObjectTypeID:%u AND m00000000-0000-0000-0000-000063c30000_da_all:"%s")', 'Kulturarvsstyrelsen', '1001 fortællinger om Danmark - Kulturstyrelsen', $this->_objectTypeId, $nummericId);
		$newQuery = sprintf('(FolderID:%u AND ObjectTypeID:%u AND DKA-ExternalIdentifier:"%s")', $this->_folderId, $this->_objectTypeId, strval($externalObject->id));
		return sprintf("(%s OR %s)", $legacyQuery, $newQuery);
	}

	public function process(&$externalObject, &$shadow = null) {
		$this->_harvester->info("Processing '%s' #%s", strval($externalObject->title), strval($externalObject->id));
		
		$shadow = new ObjectShadow();
		$shadow->extras["fileTypes"] = array();
		$shadow->extras["id"] = strval($externalObject->id);
		$shadow = $this->initializeShadow($externalObject, $shadow);
		// First process the files.
		$this->_harvester->process('sight_file_image', $externalObject, $shadow);
		$this->_harvester->process('sight_file_lowres_image', $externalObject, $shadow);
		$this->_harvester->process('sight_file_main_image', $externalObject, $shadow);
		$this->_harvester->process('sight_file_speak', $externalObject, $shadow);
		if(is_array($shadow->extras["fileTypes"])) {
			$shadow->extras["fileTypes"] = implode(', ', $shadow->extras["fileTypes"]);
		}
		// Then the metadata.
		$this->_harvester->process('sight_metadata_dka', $externalObject, $shadow);
		$this->_harvester->process('sight_metadata_dka2', $externalObject, $shadow);
		
		$shadow->commit($this->_harvester);
		
		return $shadow;
	}
	
	/*
	function skip($externalObject, &$shadow = null) {
		$shadow = new ObjectShadow();
		$shadow->skipped = true;
		$shadow = $this->initializeShadow($externalObject, $shadow);
		$shadow->query = $this->generateQuery($externalObject);
		
		$shadow->commit($this->_harvester);
		
		return $shadow;
	}
	*/
}