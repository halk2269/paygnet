<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<xsl:output indent="yes" method="html"/>
	<xsl:decimal-format decimal-separator="." grouping-separator=" "/>
	<xsl:variable name="ST" select="/root/SectionTree"/>
	<xsl:variable name="SC" select="/root/SectionCurrent"/>
	<xsl:variable name="Query" select="/root/QueryParams"/>
	<xsl:variable name="Visitor" select="/root/Visitor"/>
	<xsl:variable name="prefix" select="$Query/@prefix"/>
	<xsl:variable name="section" select="$SC/section"/>
	<xsl:variable name="SCT" select="$ST//section[@id = $section/@id]"/>
	<xsl:variable name="SR" select="$ST//section[@id = $section/@id]/ancestor-or-self::section"/>
	<xsl:variable name="homeName">ru</xsl:variable>
	<xsl:variable name="home" select="$ST//section[@name = $homeName]"/>
	<xsl:include href="includes/functions.inc.xslt"/>
	
	<xsl:template match="/root">
		<html>
			<head>
				<title>Система управления сайтом</title>
				<base href="http://{$Query/@host}{$prefix}"/>
				<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
				<link rel="stylesheet" content-type="text/css" href="{$Query/@css}admin.css"/>
			</head>
			<body>
				<xsl:apply-templates />
			</body>
		</html>
	</xsl:template>
	
</xsl:stylesheet>