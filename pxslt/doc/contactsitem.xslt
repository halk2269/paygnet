<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<div class="divContacts"> 
			<xsl:variable name="return">
				<xsl:choose>
					<xsl:when test="$SCT/meta[@name = 'back']">
						<xsl:value-of select="$SCT/meta[@name = 'back']"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="'Вернуться к списку писем'"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<div class="divContactsDate">
				<xsl:call-template name="DateTimeFromDateTime">
					<xsl:with-param name="datetime" select="@addTime"/>
				</xsl:call-template>
			</div>
			<br/>
			<table cellpadding="0" cellspacing="0" border="0" class="tableContacts" width="100%">
				<tr>
					<td>E-mail адрес</td>
					<td>
						<xsl:value-of select="field[@name = 'email']"/>
					</td>
				</tr>
				<xsl:for-each select="field[@name != 'email']">
					<tr>
						<td><xsl:value-of select="@description"/></td>
						<td><xsl:value-of select="."/></td>
					</tr>
				</xsl:for-each>
			</table>
			<div class="divContactsBack">
				<a href="{$SCT/@URL}">
					<xsl:value-of select="$return"/>
				</a>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
