<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
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
</xsl:stylesheet>