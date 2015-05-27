<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:choose>
			<xsl:when test="@documentID">
				<xsl:apply-templates />
			</xsl:when>
			<xsl:otherwise>
				<div>
					<xsl:call-template name="adminCreate"/>
					<br/>
				</div>
				<div class="divNews">
					<table cellpadding="0" cellspacing="0">
						<xsl:for-each select="document">
							<tr>
								<td class="tdDate">
									<xsl:value-of select="field[@name = 'pubdate']"/>
								</td>
								<td class="tdNewsTitlePreview">
									<div class="divNewsTitle">
										<xsl:value-of select="@id"/>: <a href="{@URL}"><xsl:value-of select="field[@name = 'tstring1']"/></a>
										<xsl:text> </xsl:text>
										[<xsl:value-of select="count(field[@name = 'tarray']/subdoc)" />]
										<xsl:if test="@enabled = 0"> [Отключен]</xsl:if>
										<xsl:call-template name="adminEditDel" />
									</div>
									<div class="divNewsPreview">
										<xsl:value-of select="field[@name = 'preview']" />
									</div>
								</td>
							</tr>
						</xsl:for-each>
					</table>
					<xsl:if test="pages/page[2]">
						<div class="divNewsPages">
							Страницы: 
							<xsl:for-each select="pages/page">
								<xsl:choose>
									<xsl:when test="@isCurrent">
										<xsl:value-of select="@num" />
									</xsl:when>
									<xsl:otherwise>
										<a href="{@URL}"><xsl:value-of select="@num" /></a>
									</xsl:otherwise>
								</xsl:choose>
								<xsl:if test="position() != last()">
									<xsl:text> | </xsl:text>
								</xsl:if>
							</xsl:for-each>
						</div>
					</xsl:if>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
