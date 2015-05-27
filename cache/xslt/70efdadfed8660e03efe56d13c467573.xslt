

	<xsl:template match="document[@docTypeName = 'text']">
		<p class="leftalign">
			<xsl:call-template name="adminEditDelText" />
		</p>
		<xsl:value-of select="field[@name = 'text']" disable-output-escaping="yes" />
	</xsl:template>
