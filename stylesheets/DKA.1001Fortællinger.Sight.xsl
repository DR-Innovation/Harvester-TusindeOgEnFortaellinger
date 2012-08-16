<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="sight">
		<p:Sight xmlns:p="http://www.kulturarv.dk/1001fortaellinger/sights.simple.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.kulturarv.dk/1001fortaellinger/sights.simple.xsd ../schemas/DKA.1001Fortællinger.Sight.xsd ">
		  <p:ID><xsl:value-of select="id"/></p:ID>
		</p:Sight>
	</xsl:template>
</xsl:stylesheet>