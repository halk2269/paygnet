<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="TmbGetWidth">
		<xsl:param name="width" />
		<xsl:param name="height" />
		<xsl:param name="squareSize" />
		<xsl:param name="suffix" select="'px'" />
		<xsl:choose>
			<xsl:when test="$width &lt;= $squareSize and $height &lt;= $squareSize">
				<xsl:value-of select="$width" />
			</xsl:when>
			<xsl:when test="$width &gt;= $height">
				<xsl:value-of select="$squareSize" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$width * $squareSize div $height" />
			</xsl:otherwise>
		</xsl:choose>
		<xsl:value-of select="$suffix" />
	</xsl:template>
	<xsl:template name="TmbGetHeight">
		<xsl:param name="width" />
		<xsl:param name="height" />
		<xsl:param name="squareSize" />
		<xsl:param name="suffix" select="'px'" />
		<xsl:choose>
			<xsl:when test="$width &lt;= $squareSize and $height &lt;= $squareSize">
				<xsl:value-of select="$height" />
			</xsl:when>
			<xsl:when test="$height &gt;= $width">
				<xsl:value-of select="$squareSize" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$height * $squareSize div $width" />
			</xsl:otherwise>
		</xsl:choose>
		<xsl:value-of select="$suffix" />
	</xsl:template>
</xsl:stylesheet>