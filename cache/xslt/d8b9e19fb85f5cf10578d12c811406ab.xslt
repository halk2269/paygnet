

	<xsl:template match="module[@id = 6]">
		<xsl:call-template name="writemap">
			<xsl:with-param name="root" select="$home" />
		</xsl:call-template>
	</xsl:template>
	<xsl:template name="writemap">
		<xsl:param name="root" />
		<ul>
			<xsl:for-each select="$root/section[@onMap = 1]">
				<li>
					<a href="{@URL}">
						<xsl:value-of select="@title" />
					</a>
					<xsl:if test="section[@onMap = 1]">
						<xsl:call-template name="writemap">
							<xsl:with-param name="root" select="." />
						</xsl:call-template>
					</xsl:if>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>
