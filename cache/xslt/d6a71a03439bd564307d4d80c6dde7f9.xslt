

	<xsl:template match="document[@docTypeName = 'event']">
		<div class="divArt">
			<xsl:variable name="return">
				<xsl:choose>
					<xsl:when test="$SCT/meta[@name = 'back']">
						<xsl:value-of select="$SCT/meta[@name = 'back']"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="'Go to event list'"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<div class="leftalign">
				<xsl:call-template name="adminEditDelText"/>
			</div>
			<div class="divArtTitle">
				<xsl:value-of select="field[@name = 'title']"/>
			</div>
			<div class="divArtDate">
				Published:&#160;<xsl:value-of select="field[@name = 'pubdate']"/>
			</div>
			<div class="divArtText">
				<xsl:value-of select="field[@name = 'text']" disable-output-escaping="yes"/>
			</div>
			<div class="divArtBack">
				<a href="{$SCT/@URL}">
					<xsl:value-of select="$return"/>
				</a>
			</div>
		</div>
	</xsl:template>

