<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<!-- Шаблон генерации блока новостей на главной странице -->
	<xsl:template name="TeamBlock">
		<xsl:param name="path" select="$SC/module" />
		<xsl:variable name="current" select="$path/document" />
		<h2>
			<span>
				<xsl:value-of select="$current/field[@name = 'title']" /> 
			</span>
		</h2>
		<xsl:value-of select="$current/field[@name = 'description']" />
		<br/>
		<a href="{$current/@URL}">Read more...</a>
	</xsl:template>
</xsl:stylesheet>
