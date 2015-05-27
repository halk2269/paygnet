<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:choose>
			<xsl:when test="@documentID">
				<xsl:apply-templates/>
			</xsl:when>
			<xsl:otherwise>
				<div>
					<xsl:call-template name="adminCreate"/>
				</div>
				<br />Подсказка: Если включено несколько опросов, они будут чередоваться на сайте в случайном порядке. Ниже представлен полный список опросов (включенные вверху)
				<br />
				<br />
				<xsl:for-each select="document">
					<xsl:value-of select="field[@name = 'question']"/>
					<xsl:if test="@enabled = 0"> [Отключен]</xsl:if>
					<xsl:call-template name="adminEditDel" />
					<br />
					<br />
				</xsl:for-each>
				<xsl:if test="pages/page[2]">
					<div class="divBannerPages">Страницы: 
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
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
