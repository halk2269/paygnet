<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="no" doctype-system="http://www.w3.org/TR/REC-html40/strict.dtd" doctype-public="-//W3C//DTD HTML 4.0//EN"/>
	<xsl:decimal-format decimal-separator="." grouping-separator=" "/>
	<xsl:variable name="ST" select="/root/SectionTree"/>
	<xsl:variable name="SC" select="/root/SectionCurrent"/>
	<xsl:variable name="Query" select="/root/QueryParams"/>
	<xsl:variable name="Visitor" select="/root/Visitor"/>
	<xsl:variable name="prefix" select="$Query/@prefix"/>
	<xsl:variable name="section" select="$SC/section"/>
	<xsl:variable name="SCT" select="$ST//section[@id = $section/@id]"/>
	<xsl:variable name="SR" select="$ST//section[@id=$section/@id]/ancestor-or-self::section"/>
	<xsl:template match="/root/SectionTree" />
<xsl:template match="/root/QueryParams" />
<xsl:template match="/root/Visitor" />
<xsl:template match="/root/SectionCurrent">
		<html>
			<head>
				<title>Ошибка 404 &#8212; запрашиваемый документ не найден</title>
				<base href="http://{$Query/@host}{$prefix}"/>
				<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
			</head>
			<body>
				<xsl:apply-templates/>
			</body>
		</html>
	</xsl:template>

