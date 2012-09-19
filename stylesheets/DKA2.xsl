<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:key name="image-by-credit" match="image" use="credit" />
	<xsl:key name="story-by-author_name" match="story" use="author/name" />
	
	<xsl:template match="/sights">
		<xsl:for-each select="sight[id=$id]">
			<xsl:call-template name="sight" />
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="/sight">
		<xsl:call-template name="sight" />
	</xsl:template>
	
	<xsl:template name="sight">
		<dka:DKA xmlns:dka="http://www.danskkulturarv.dk/DKA2.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.danskkulturarv.dk/DKA2.xsd ../schemas/DKA2.xsd ">
			<dka:Title><xsl:value-of select="title"/></dka:Title>
			<dka:Abstract /><!-- Consider if this is correct? -->
			<dka:Description>
				<h4><xsl:value-of select="subtitle"/></h4>
				<div>
					<xsl:value-of select="description"/>
				</div>
				<xsl:for-each select="stories/story">
					<h4><xsl:value-of select="title" /></h4>
					<div>
						<xsl:value-of select="body" />
						<p>
							Skrevet af <a target="_blank">
								<xsl:attribute name="href">
									<xsl:value-of select="author/uri" />
								</xsl:attribute>
								<xsl:value-of select="author/name" />
							</a>
						</p>
					</div>
				</xsl:for-each>
			</dka:Description>
			<dka:Organization>1001 fortællinger om Danmark - Kulturstyrelsen</dka:Organization>
			<dka:ExternalURL><xsl:value-of select="uri"/></dka:ExternalURL>
			<dka:ExternalIdentifier><xsl:value-of select="id"/></dka:ExternalIdentifier>
			<dka:Type><xsl:value-of select="$fileTypes"/></dka:Type>
			<dka:Contributors>
				<!-- xsl:for-each select=""></xsl:for-each -->
			</dka:Contributors>
			<dka:Creators>
				<xsl:for-each select="administrator">
					<dka:Creator>
						<dka:Name><xsl:value-of select="name" /></dka:Name>
						<dka:Role>Administrator</dka:Role>
					</dka:Creator>
				</xsl:for-each>
				<xsl:for-each select="images/image[count(. | key('image-by-credit', credit)[1]) = 1 and credit != '']">
					<xsl:sort select="credit" />
					<dka:Creator>
						<dka:Name><xsl:value-of select="credit"/></dka:Name>
						<dka:Role>Fotograf</dka:Role>
					</dka:Creator>
				</xsl:for-each>
				<xsl:for-each select="stories/story[count(. | key('story-by-author_name', author/name)[1]) = 1 and author/name != '']">
					<xsl:sort select="author/name" />
					<dka:Creator>
						<dka:Name><xsl:value-of select="author/name"/></dka:Name>
						<dka:Role>Forfatter</dka:Role>
					</dka:Creator>
				</xsl:for-each>
			</dka:Creators>
			<dka:Categories/>
			<dka:Tags>
				<xsl:for-each select="themes/theme">
					<dka:Tag><xsl:value-of select="title"/></dka:Tag>
				</xsl:for-each>
				<xsl:for-each select="tags/tag">
					<dka:Tag><xsl:value-of select="value"/></dka:Tag>
				</xsl:for-each>
			</dka:Tags>
		</dka:DKA>
	</xsl:template>
</xsl:stylesheet>