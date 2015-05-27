<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="edit">
		<xsl:if test="$Visitor/@id">
			<div class="divEdit">
				<xsl:if test="$Visitor/role/@name = 'admin' or $Visitor/role/@name = 'superadmin'">
					<xsl:variable name="admin">
						<xsl:choose>
							<xsl:when test="$ST//section[@name = 'secadmin']">secadmin</xsl:when>
							<xsl:otherwise>admin</xsl:otherwise>
						</xsl:choose>
					</xsl:variable>
					<a href="{$prefix}{$admin}/?id={$SCT/@id}">Редактировать секцию</a>
					<xsl:text> | </xsl:text>
				</xsl:if>
				<xsl:text>Логин: </xsl:text>
				<xsl:value-of select="$Visitor/@login"/>
				<xsl:text> (</xsl:text>
				<a href="{$prefix}?writemodule=Authorize&amp;logoff=1">Выход</a>
				<xsl:text>)</xsl:text>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
