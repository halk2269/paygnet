<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
<!-- Шаблон генерации строки навигации -->
	<xsl:template name="GenPath">
		<xsl:param name="ST"/>
		<xsl:param name="SC"/>
		<xsl:param name="sid"/>
		<xsl:variable name="CS" select="$ST//section[@id = $sid]"/>
		<ul class="breadcrumbs">
           	<xsl:for-each select="$CS/ancestor-or-self::section">
           		<li>
					<xsl:choose>
						<xsl:when test="position() != last()">
							<a href="{@URL}#main-content">
								<xsl:value-of select="@title"/>
							</a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="$SC//module/@documentID">
									<a href="{@URL}#main-content">
										<xsl:value-of select="@title"/>
									</a>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@title"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>
</xsl:stylesheet>