<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="sight">
		<dka:DKA xmlns:dka="http://www.danskkulturarv.dk/DKA2.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.danskkulturarv.dk/DKA2.xsd ../schemas/DKA2.xsd ">
			<dka:Title><xsl:value-of select="title"/></dka:Title>
			<dka:Abstract /><!-- Consider if this is correct? -->
			<dka:Description><xsl:value-of select="description"/></dka:Description>
			<dka:Organization>Kulturstyrelsen</dka:Organization>
			<dka:ExternalURL>dka:ExternalURL</dka:ExternalURL>
			<dka:Type><xsl:value-of select="/extras/fileTypes"/></dka:Type><!-- This is not working ... -->
			<dka:Contributors/>
			<dka:Creators/>
			<dka:Categories/>
			<dka:Tags/>
		</dka:DKA>
	</xsl:template>
</xsl:stylesheet>