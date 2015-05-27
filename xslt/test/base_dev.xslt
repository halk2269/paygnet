<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:msxsl="urn:schemas-microsoft-com:xslt" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm msxsl">

<xsl:variable name="ST" select="/root/SectionTree" />
<xsl:variable name="SC" select="/root/SectionCurrent" />
<xsl:variable name="Query" select="/root/QueryParams" />

<xsl:template>
	<html>
		<head>
			<title>Test Test Test</title>
			<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
		</head>
		
		<body>
			<p align="center">
				<h2>Visitor id: <xsl:value-of select="/root/Visitor/@id" /></h2>
				<h3><xsl:value-of select="'1'" /></h3>
				<a href="{module[@class = 'DocReadClass']/@createURL}">Создание нового документа</a><br /><br /><br />
				<xsl:apply-templates />
			</p>
		</body>
	</html>
</xsl:template>

</xsl:stylesheet>