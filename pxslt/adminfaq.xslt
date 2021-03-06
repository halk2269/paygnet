<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:for-each select="document">
			<xsl:if test="$Visitor/role/@name = 'superadmin'">
				<p class="leftalign">
					<xsl:call-template name="adminEditDelText" />
				</p>
			</xsl:if>
			<xsl:value-of select="field[@name = 'text']" disable-output-escaping="yes" />
		</xsl:for-each>
		<div>
			<button onclick="window.close();">Закрыть окно</button>
		</div>	
	</xsl:template>
</xsl:stylesheet>