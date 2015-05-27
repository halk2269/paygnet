<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<xsl:template>
		<xsl:call-template name="adminCreate"/><br />
		<xsl:for-each select="document">
			<strong><xsl:value-of select="field[@name = 'ttitle']" /></strong>
			<xsl:call-template name="adminEditDel"/>
			<br />
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
