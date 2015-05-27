<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:for-each select="document">
			<xsl:call-template name="adminEditDelText" />
			Part Alias &#8212; <a href="{$prefix}{@targetSection}"><xsl:value-of select="@targetSection" /></a>
			<br/>
			<div class="marg-t-15">
				<xsl:value-of select="field[@name = 'text']" disable-output-escaping="yes" />
			</div>
			<br/>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>