

	<xsl:template match="document[@docTypeName = 'school']">
		<div class="divSchool">
			<xsl:variable name="return">
				<xsl:choose>
					<xsl:when test="$SCT/meta[@name = 'back']">
						<xsl:value-of select="$SCT/meta[@name = 'back']"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="'Back to list'"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<div class="leftalign">
				<xsl:call-template name="adminEditDelText"/>
			</div>
			<div class="divSchoolTitle">
				<xsl:value-of select="field[@name = 'title']"/>
			</div>
			<div class="divSchoolRegion">
				State:&#160;<xsl:value-of select="field[@name = 'state']"/>
				<br/>
				City:&#160;<xsl:value-of select="field[@name = 'city']" />
			</div>
			<div class="divSchoolText">
				<xsl:value-of select="field[@name = 'description']" disable-output-escaping="yes"/>
			</div>
			<div class="divSchoolBack">
				<a href="{$SCT/@URL}">
					<xsl:value-of select="$return"/>
				</a>
			</div>
		</div>
	</xsl:template>

