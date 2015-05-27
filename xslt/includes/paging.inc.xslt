<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="Paging">
		<xsl:param name="className" select="''" />
		<xsl:if test="pages/page[2]">
			<div class="{$className}">
				<xsl:text>Страницы: </xsl:text>
				<xsl:for-each select="pages/page">
					<xsl:if test="position() = 1">
						<xsl:choose>
							<xsl:when test="not(@isCurrent)">
								<a title="Назад" style="text-decoration: none" href="{../page[@isCurrent]/preceding-sibling::page[1]/@URL}">&#8592;</a>
								<xsl:text> </xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<span class="disabledArrow">&#8592; </span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					<xsl:choose>
						<xsl:when test="@isCurrent">
							<b>
								<xsl:value-of select="@num" />
							</b>
						</xsl:when>
						<xsl:otherwise>
							<a href="{@URL}"><xsl:value-of select="@num" /></a>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="position() != last()">
						<xsl:text> | </xsl:text>
					</xsl:if>
					<xsl:if test="position() = last()">
						<xsl:choose>
							<xsl:when test="not(@isCurrent)">
								<xsl:text> </xsl:text>
								<a title="Вперёд" style="text-decoration: none" href="{../page[@isCurrent]/following-sibling::page[1]/@URL}">&#8594;</a>
							</xsl:when>
							<xsl:otherwise>
								<span class="disabledArrow"> &#8594;</span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>