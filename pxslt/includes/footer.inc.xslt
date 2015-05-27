<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="DisplayFooter">
		<div class="footer">
			<div class="footer_resize">
		   		<p class="lf">&#169; 2014</p>
		   		<xsl:call-template name="FooterMenu" />
		   		<div class="clr"></div>
			</div>
		</div>	
	</xsl:template>
	
	<xsl:template name="FooterMenu">
		<ul class="fmenu">
   			<li>
   				<xsl:choose>
   					<xsl:when test="$isMain">
   						<xsl:text>Home</xsl:text>
   					</xsl:when>
   					<xsl:otherwise>
   						<xsl:attribute name="class">active</xsl:attribute>
   						<a href="{$prefix}">Home</a>
   					</xsl:otherwise>
   				</xsl:choose>
   			</li>
   			<xsl:for-each select="$ST//section[meta[@name = 'menu_footer'] = 1]">
   				<xsl:variable name="isActive" select="$SR/@id = @id"/>
   				<li>
   					<xsl:choose>
	   					<xsl:when test="$isActive">
	   						<xsl:value-of select="@title" />
	   					</xsl:when>
	   					<xsl:otherwise>
	   						<xsl:attribute name="class">active</xsl:attribute>
	   						<a href="{@URL}">
	   							<xsl:value-of select="@title" />
	   						</a>
	   					</xsl:otherwise>
	   				</xsl:choose>
   				</li>
   			</xsl:for-each>
   		</ul>
	</xsl:template>
</xsl:stylesheet>		