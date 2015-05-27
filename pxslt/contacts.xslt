<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:choose>
			<xsl:when test="@documentID">
				<xsl:apply-templates/>
			</xsl:when>
			<xsl:otherwise>
				<div class="divContactsList">
					<xsl:for-each select="document">
						<div class="divContactsListItem">
							<span class="divContactsListDate">
								<xsl:call-template name="DateTimeFromDateTime">
									<xsl:with-param name="datetime" select="@addTime"/>
								</xsl:call-template>
							<xsl:text>&#160;</xsl:text>
							</span>
							<span class="divContactsListTitle">
								<a href="{@URL}">
									<xsl:text>Электронная почта контактного лица: </xsl:text>
									<xsl:value-of select="field[@name = 'email']"/>
								</a>
							</span>
						</div>
					</xsl:for-each>
					<!-- Pages -->
					<xsl:call-template name="Paging">
						<xsl:with-param name="className" select="'divContactsPages'"/>
					</xsl:call-template>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
