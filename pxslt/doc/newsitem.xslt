<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<div class="divNews">
			<xsl:variable name="return">
				<xsl:choose>
					<xsl:when test="$SCT/meta[@name = 'back']">
						<xsl:value-of select="$SCT/meta[@name = 'back']"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="'Вернуться к списку новостей'"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<div class="leftalign">
				<xsl:call-template name="adminEditDelText"/>
			</div>
			<div class="divNewsDate">
				<xsl:value-of select="field[@name = 'pubdate']"/>
			</div>
			<div class="divNewsTitle">
				<xsl:value-of select="field[@name = 'title']"/>
			</div>
			<div class="divNewsText">
				<xsl:value-of select="field[@name = 'text']" disable-output-escaping="yes"/>
			</div>
			<div class="divNewsBack">
				<a href="{$SCT/@URL}">
					<xsl:value-of select="$return"/>
				</a>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
