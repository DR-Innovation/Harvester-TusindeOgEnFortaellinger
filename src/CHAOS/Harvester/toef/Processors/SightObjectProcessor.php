<?php
namespace CHAOS\Harvester\toef\Processors;
use CHAOS\Harvester\Shadows\ObjectShadow;

class SightObjectProcessor extends \CHAOS\Harvester\Processors\ObjectProcessor {

	public function __construct($harvester, $name, $parameter = null) {
		$this->_harvester = $harvester;
		$this->_harvester->debug("A ".__CLASS__." named '$name' was constructing.");
	}

	public function process($externalObject, $shadow = null) {
		$this->_harvester->debug(__CLASS__." is processing.");
		var_dump($externalObject);
		$this->_harvester->info("Processing '%s' #%s", strval($externalObject->title), strval($externalObject->id));
		
		// Extract the nummeric ID.
		$nummericId = explode('/', strval($externalObject->id));
		$nummericId = $nummericId[count($nummericId)-1];
		
		$legacyQuery = sprintf('(DKA-Organization:"%s" AND ObjectTypeID:%u AND m00000000-0000-0000-0000-000063c30000_da_all:"%s")', 'Kulturarvsstyrelsen', $this->_objectTypeId, $nummericId);
		$newQuery = sprintf('(FolderTree:%u AND ObjectTypeID:%u AND DKA-ExternalIdentifier:"%s")', $this->_folderId, $this->_objectTypeId, strval($externalObject->id));

		$shadow = new ObjectShadow();
		$shadow->query = sprintf("(%s OR %s)", $legacyQuery, $newQuery);
		$shadow->objectTypeId = $this->_objectTypeId;
		$shadow->folderId = $this->_folderId;
		$shadow = $this->_harvester->process('sight_metadata_dka', $externalObject, $shadow);
		$shadow = $this->_harvester->process('sight_metadata_dka2', $externalObject, $shadow);
		/*
		$shadow = $this->_harvester->process('movie_file_video', $externalObject, $shadow);
		$shadow = $this->_harvester->process('movie_file_images', $externalObject, $shadow);
		$shadow = $this->_harvester->process('movie_file_lowres_images', $externalObject, $shadow);
		$shadow = $this->_harvester->process('movie_file_main_image', $externalObject, $shadow);
		*/

		// TODO: Implement the file relations.
		/*
		 if($this->_harvester->hasOption('debug')) {
		var_dump($shadow);
		}
		*/
		return $shadow;
	}
}