<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<form method="get" action="{$Query/@url}">
			<input type="text" name="s" size="40" maxlength="200" value="{$Query/param[@name = 's']}" class=""/>&#160;<input type="submit" value="Искать"/>
		</form>
		<xsl:choose>
			<xsl:when test="(search = 'NoResult')">
				<div class="searchResultsHead">Ничего не найдено</div>
			</xsl:when>
			<xsl:when test="(search = 'SearchCompleted')">
				<div class="searchResultsHead">Результаты поиска</div>
				<div class="searchResultsCount">
					<xsl:variable name="LastChar" select="substring(pages/@docCount, string-length(pages/@docCount))" />
					<xsl:choose>
						<xsl:when test="pages/@docCount = 1">
							<xsl:text>Найден один документ</xsl:text>
						</xsl:when>
						<xsl:when test="$LastChar = 1 or $LastChar = 1">
							<xsl:text>Найден </xsl:text>
							<b>
								<xsl:value-of select="pages/@docCount" />
							</b>
							<xsl:text> документ</xsl:text>
						</xsl:when>
						<xsl:when test="$LastChar = 2 or $LastChar = 3 or $LastChar = 4">
							<xsl:text>Найдено </xsl:text>
							<b>
								<xsl:value-of select="pages/@docCount" />
							</b>
							<xsl:text> документа</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>Найдено </xsl:text>
							<b>
								<xsl:value-of select="pages/@docCount" />
							</b>
							<xsl:text> документов</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</div>
				<xsl:for-each select="row">
					<xsl:choose>
						<xsl:when test="aux[@name = 'doc_type'] = 'text' or aux[@name = 'doc_type'] = ''">
							<p>
								<xsl:if test="$ST//section[@id = current()/aux[@name = 'sec_id']]/../../@title">
									<xsl:value-of select="$ST//section[@id = current()/aux[@name = 'sec_id']]/../../@title" />&#160;/
								</xsl:if>
								<xsl:if test="$ST//section[@id = current()/aux[@name = 'sec_id']]/../@title">
									<xsl:value-of select="$ST//section[@id = current()/aux[@name = 'sec_id']]/../@title" />&#160;/
								</xsl:if>
								<a href="{$prefix}{aux[@name = 'url']}">
									<xsl:value-of select="aux[@name = 'sec_title']"/>
				    			</a>
							</p>
						</xsl:when>
						<xsl:otherwise>	
							<p>
								<xsl:if test="$ST//section[@id = current()/aux[@name = 'sec_id']]/../@title">
									<xsl:value-of select="$ST//section[@id = current()/aux[@name = 'sec_id']]/../@title" />&#160;/
								</xsl:if>
								<xsl:value-of select="aux[@name = 'sec_title']"/>&#160;/
				    			<a href="{$prefix}{aux[@name = 'url']}">
									<xsl:value-of select="aux[@name = 'doc_title']"/>
								</a>
							</p>
						</xsl:otherwise>
					</xsl:choose>	
				</xsl:for-each>
				<xsl:call-template name="Paging">
					<xsl:with-param name="className" select="'searchPages'" />
				</xsl:call-template>
			</xsl:when>
		</xsl:choose> 
	</xsl:template>
</xsl:stylesheet>