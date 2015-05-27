<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<!-- Шаблон генерации блока новостей на главной странице -->
	<xsl:template name="EventBlock">
		<xsl:param name="path" select="$SC/module" />
		<h2 class="star"><span>Events</span></h2>
		<ul class="ex_menu">
			<xsl:for-each select="$path/document">
				<li>
					<xsl:value-of select="field[@name = 'pubdate']" />
					<br/>
					<a href="{@URL}" title="{field[@name = 'title']}">
						<xsl:value-of select="field[@name = 'title']" />
					</a>
					<div class="eventBlock">
						<xsl:value-of select="field[@name = 'preview']" />
					</div>
				</li>
			</xsl:for-each>
 		</ul>
	</xsl:template>
</xsl:stylesheet>
