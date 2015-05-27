<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:decimal-format decimal-separator="." grouping-separator=" "/>
	<xsl:variable name="ST" select="/root/SectionTree"/>
	<xsl:variable name="SC" select="/root/SectionCurrent"/>
	<xsl:variable name="Query" select="/root/QueryParams"/>
	<xsl:variable name="Visitor" select="/root/Visitor"/>
	<xsl:variable name="role" select="$Visitor/role"/>
	<xsl:variable name="prefix" select="$Query/@prefix"/>
	<xsl:variable name="section" select="$SC/section"/>
	<xsl:variable name="curSec" select="$section/@id"/>
	<xsl:variable name="SCT" select="$ST//section[@id = $section/@id]"/>
	<xsl:variable name="SR" select="$ST//section[@id = $section/@id]/ancestor-or-self::section"/>
</xsl:stylesheet>