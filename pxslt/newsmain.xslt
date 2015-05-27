<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<div class="divNewsMainList">
			<xsl:for-each select="document">
				<div class="divNewsMainListItem">
					<div class="divNewsMainListDate">
						<xsl:value-of select="field[@name = 'pubdate']"/>
					</div>
					<div class="divNewsMainListTitle">
						<a href="{@URL}"><xsl:value-of select="field[@name = 'title']"/></a>
						<xsl:if test="@enabled = 0"> [Отключен]</xsl:if>
						<xsl:call-template name="adminEditDel" />
					</div>
					<div class="divNewsMainListPreview">
						<xsl:value-of select="field[@name = 'preview']" />
					</div>
				</div>
			</xsl:for-each>
		</div>
	</xsl:template>
</xsl:stylesheet>