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
	<xsl:variable name="SR" select="$ST//section[@id=$section/@id]/ancestor-or-self::section"/>
	<xsl:variable name="homeName">ru</xsl:variable>
	<xsl:variable name="home" select="$ST//section[@name = $homeName]"/>
	
	<xsl:template name="debugVars">
		<h3>[Info]</h3>
		<xsl:for-each select="info/item">
			<strong><xsl:value-of select="@name" /></strong>: <xsl:value-of select="." /> = <xsl:value-of select="@description" /><br />
		</xsl:for-each>
		
		<h3>[Errors]</h3>
		<xsl:for-each select="error/item">
			<strong><xsl:value-of select="@name" /></strong>: <xsl:value-of select="." /> = <xsl:value-of select="@description" /><br />
		</xsl:for-each>
		
		<h3>[Vars]</h3>
		<h3>- Own</h3>
		<xsl:for-each select="vars/own/var">
			<strong><xsl:value-of select="@name" /></strong>: <xsl:value-of select="." /> = <xsl:value-of select="@description" /><br />
		</xsl:for-each>
		
		<h3>- General</h3>
		<xsl:for-each select="vars/general/var">
			<strong><xsl:value-of select="@name" /></strong>: <xsl:value-of select="." /> = <xsl:value-of select="@description" /><br />
		</xsl:for-each>
		
		<h3>- User</h3>
		<xsl:for-each select="vars/user/var">
			<strong><xsl:value-of select="@name" /></strong>: <xsl:value-of select="." /> = <xsl:value-of select="@description" /><br />
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="/root">
		<html>
			<head>
				<title>Администраторский интерфейс - Некоммерческая ассоциация «Культурно-деловой центр молодежи»</title>
				<base href="http://{$Query/@host}{$prefix}"/>
				<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
				<link rel="stylesheet" content-type="text/css" href="{$prefix}css/admin.css"/>
			</head>
			<body>
				<xsl:apply-templates />
			</body>
		</html>
	</xsl:template>
	
</xsl:stylesheet>
