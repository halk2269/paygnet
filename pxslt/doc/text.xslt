<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<div class="leftalign">
			<xsl:call-template name="adminEditDelText" />
		</div>
		<xsl:value-of select="field[@name = 'text']" disable-output-escaping="yes" />
	</xsl:template>
</xsl:stylesheet>