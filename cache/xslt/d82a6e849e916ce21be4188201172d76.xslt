

	<xsl:template match="module[@id = 58]">
		
		<div class="divClasses">
			<xsl:for-each select="document">
				<div>
					<xsl:call-template name="adminEdit" />
				</div>
				<xsl:variable name="width">
					<xsl:choose>
						<xsl:when test="field[@name = 'width'] &gt; 0">
							<xsl:value-of select="field[@name = 'width']" />
						</xsl:when>
						<xsl:otherwise>600</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<xsl:variable name="height">
					<xsl:choose>
						<xsl:when test="field[@name = 'height'] &gt; 0">
							<xsl:value-of select="field[@name = 'height']" />
						</xsl:when>
						<xsl:otherwise>400</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<div class="divClassesDesc">
					<xsl:value-of select="field[@name = 'description']" disable-output-escaping="yes" />
				</div>
				<div class="divClassesFrame">
					<iframe src="http://{field[@name = 'source']}" width="{$width}" height="{$height}"></iframe>
				</div>
			</xsl:for-each>
		</div>
	</xsl:template>
