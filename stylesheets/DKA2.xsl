<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/2002/08/xhtml/xhtml1-transitional.xsd">
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
		<DKA xmlns="http://www.danskkulturarv.dk/DKA2.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.danskkulturarv.dk/DKA2.xsd ../../Base/schemas/DKA2.xsd ">
			<Title><xsl:value-of select="title"/></Title>
			<Abstract /><!-- Consider if this is correct? -->
			<Description>
				<div xmlns="http://www.w3.org/1999/xhtml">
					<h4><xsl:value-of select="subtitle"/></h4>
					<p><xsl:value-of select="description"/></p>
					<xsl:for-each select="stories/story">
						<div>
							<h4><xsl:value-of select="title" /></h4>
							<xsl:value-of select="body" />
							<p>
								Skrevet af <a target="_blank">
									<xsl:attribute name="href"><xsl:value-of select="author/uri" /></xsl:attribute>
									<xsl:value-of select="author/name" />
								</a>
							</p>
						</div>
					</xsl:for-each>
				</div>
			</Description>
			<Organization>1001 fortællinger om Danmark - Kulturstyrelsen</Organization>
			<ExternalURL><xsl:value-of select="uri"/></ExternalURL>
			<ExternalIdentifier><xsl:value-of select="id"/></ExternalIdentifier>
			<Type><xsl:value-of select="$fileTypes"/></Type>
			<Contributors>
				<!-- xsl:for-each select=""></xsl:for-each -->
			</Contributors>
			<Creators>
				<xsl:for-each select="administrator">
					<Creator>
						<xsl:attribute name="Role">Administrator</xsl:attribute>
						<xsl:value-of select="name" />
					</Creator>
				</xsl:for-each>
				<xsl:for-each select="images/image[count(. | key('image-by-credit', credit)[1]) = 1 and credit != '']">
					<xsl:sort select="credit" />
					<Creator>
						<xsl:attribute name="Role">Fotograf</xsl:attribute>
						<xsl:value-of select="credit" />
					</Creator>
				</xsl:for-each>
				<xsl:for-each select="stories/story[count(. | key('story-by-author_name', author/name)[1]) = 1 and author/name != '']">
					<xsl:sort select="author/name" />
					<Creator>
						<xsl:attribute name="Role">Forfatter</xsl:attribute>
						<xsl:value-of select="author/name" />
					</Creator>
				</xsl:for-each>
			</Creators>
			<TechnicalComment />
			<Location><xsl:value-of select="geography/municipality"/></Location>
			<RightsDescription>Copyright © Kulturstyrelsen (<xsl:value-of select="@license"/>)</RightsDescription>
			<Categories/>
			<Tags>
				<xsl:for-each select="themes/theme">
					<Tag><xsl:value-of select="title"/></Tag>
				</xsl:for-each>
				<xsl:for-each select="tags/tag">
					<Tag><xsl:value-of select="value"/></Tag>
				</xsl:for-each>
			</Tags>
		</DKA>
	</xsl:template>
</xsl:stylesheet>