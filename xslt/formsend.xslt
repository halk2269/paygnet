<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output encoding="UTF-8" method="html" indent="yes"/>
	<xsl:template match="root">
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
				<style type="text/css">
					body, html {
					padding: 0;
					margin: 0;
					background: #FFF;
					}
					body, td {
					font: 10pt Verdana, sans-serif;
					color: #000;
					}
					a {
					color: #0D46A6;
					text-decoration: underline;
					}
					a:visited {
					color: #555;
					}
					img {
					border: 0;
					}
					ul, ol, li {
					margin: 0;
					padding: 0;
					}
					li {
					margin-left: 0px;
					}
					h1, h2, h3 {
					padding: 0;
					margin: 0;
					}
					h1 {
					font-size: 12pt;
					font-weight: bold;
					}
					h2 {
					font-size: 11pt;
					font-weight: bold;
					}
					h3 {
					font-size: 10pt;
					text-decoration: underline;
					}
					div.base {
					padding: 10px;
					}
					table.tab {
					margin-top: 7px;
					}
					table.tab td {
					padding: 1px 7px 1px 0px;
					}
				</style>
			</head>
			<body style="font-size: 16px; font-family: Verdana, Tahoma, Arial, Sans-serif;">
				<div class="base">
					<h1>
						<xsl:value-of select="//document/@docTypeTitle"/>
					</h1>
					<table cellpadding="0" cellspacing="2" border="0" class="tab">
						<xsl:for-each select="/root/document/field">
							<xsl:call-template name="WriteFields"/>
						</xsl:for-each>
					</table>
				</div>
			</body>
		</html>
	</xsl:template>
	<xsl:template name="WriteFields">
		<tr valign="top">
			<xsl:variable name="fieldname" select="@name"/>
			<xsl:choose>
				<xsl:when test="@type = 'file' or @type = 'image'">
					<xsl:text disable-output-escaping="yes">&lt;!--%%file%</xsl:text>
					<xsl:value-of select="$fieldname"/>
					<xsl:text disable-output-escaping="yes">%%--&gt;</xsl:text>
					<!-- IMAGE -->
					<!--
					<img src="%%image%{$fieldname}%%" alt="" />
					<xsl:text disable-output-escaping="yes">&lt;img src="%%image%</xsl:text>
					<xsl:value-of select="$fieldname"/>
					<xsl:text disable-output-escaping="yes">%%"&gt;</xsl:text>
					-->
				</xsl:when>
				<xsl:otherwise>
					<td>
						<strong>
							<xsl:value-of select="@description"/>
						</strong>
					</td>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<!--xsl:when test="@type = 'file' or @type = 'image'" /-->
				<xsl:when test="@type = 'bool'">
					<td>
						<xsl:choose>
							<xsl:when test=". = 1">Да</xsl:when>
							<xsl:otherwise>Нет</xsl:otherwise>
						</xsl:choose>
					</td>
				</xsl:when>
				<xsl:when test="@type = 'strlist'">
					<td align="justify">
						<xsl:for-each select="line">
							<xsl:value-of select="." disable-output-escaping="yes"/>
							<br/>
						</xsl:for-each>
					</td>
				</xsl:when>
				<xsl:when test="@type = 'text'">
					<td>
						<xsl:choose>
							<xsl:when test="../../doctype/field[@name = current()/@name]/@mode = 'simple'">
								<xsl:value-of select="."/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="." disable-output-escaping="yes"/>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</xsl:when>
				<xsl:when test="@type = 'link'">
					<td>
						<xsl:variable name="fieldName" select="@targetDTTitle"/>
						<xsl:value-of select="./document/field[@name = $fieldName]"/>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td>
						<xsl:value-of select="."/>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		</tr>
	</xsl:template>
</xsl:stylesheet>
