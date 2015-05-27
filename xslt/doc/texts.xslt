<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:value-of select="field[@name = 'text']" disable-output-escaping="yes" />
		<xsl:call-template name="adminEditDel" />
	</xsl:template>
</xsl:stylesheet>