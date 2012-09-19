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
		<DKA xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../schemas/DKA.xsd">
			<Title><xsl:value-of select="title"/></Title>
			<Abstract />
			<Description>
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
			</Description>
			<Organization>1001 fortællinger om Danmark - Kulturstyrelsen</Organization>
			<Type><xsl:value-of select="$fileTypes"/></Type>
			<CreatedDate />
			<FirstPublishedDate />
			<Identifier><xsl:value-of select="id"/></Identifier>
			<Contributor>
				<!-- xsl:for-each select=""></xsl:for-each -->
			</Contributor>
			<Creator>
				<xsl:for-each select="administrator">
					<Person Role="Administrator">
						<xsl:attribute name="Name">
							<Name><xsl:value-of select="name"/></Name>
						</xsl:attribute>
					</Person>
				</xsl:for-each>
				<xsl:for-each select="images/image[count(. | key('image-by-credit', credit)[1]) = 1 and credit != '']">
					<xsl:sort select="credit" />
					<Person Role="Fotograf">
						<xsl:attribute name="Name">
							<Name><xsl:value-of select="credit"/></Name>
						</xsl:attribute>
					</Person>
				</xsl:for-each>
				<xsl:for-each select="stories/story[count(. | key('story-by-author_name', author/name)[1]) = 1 and author/name != '']">
					<xsl:sort select="author/name" />
					<Person Role="Forfatter">
						<xsl:attribute name="Name">
							<Name><xsl:value-of select="author/name"/></Name>
						</xsl:attribute>
					</Person>
				</xsl:for-each>
			</Creator>
			<TechnicalComment />
			<Location><xsl:value-of select="geography/municipality"/></Location>
			<RightsDescription>Copyright © Kulturstyrelsen</RightsDescription>
			<GeoData>
				<Latitude><xsl:value-of select="geography/latitude"/></Latitude>
				<Longitude><xsl:value-of select="geography/longitude"/></Longitude>
			</GeoData>
			<Categories/>
			<Tags>
				<xsl:for-each select="themes/theme">
					<Tag><xsl:value-of select="title"/></Tag>
				</xsl:for-each>
				<xsl:for-each select="tags/tag">
					<Tag><xsl:value-of select="value"/></Tag>
				</xsl:for-each>
			</Tags>
			<ProductionID/>
			<StreamDuration/>
		</DKA>
	</xsl:template>
</xsl:stylesheet>