<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="DisplayHeader">
		<div class="header">
			<div class="header_resize">
				<div class="authFrom"> 
					<xsl:call-template name="authorizationBox"/>
				</div>
				<div class="logo">
					<h1>
						<xsl:choose>
							<xsl:when test="$isMain">
								<xsl:value-of select="$SC/module[@name = 'sloganOnTop']/document/field[@name = 'text']" disable-output-escaping="yes" />
							</xsl:when>
							<xsl:otherwise>
								<a href="{$prefix}">
									<xsl:value-of select="$SC/module[@name = 'sloganOnTop']/document/field[@name = 'text']" disable-output-escaping="yes" />
								</a>
							</xsl:otherwise>
						</xsl:choose>
					</h1>
				</div>
				<div class="clr"></div>
			    <div class="htext">
			    	<h2>Read me first...</h2>
			      	<xsl:value-of select="$SC/module[@name = 'headerText']/document/field[@name = 'text']" disable-output-escaping="yes" />
			    </div>
				<div class="clr"></div>
			    <!--div class="menu_nav"-->
			    <div>
			    	<xsl:call-template name="ShowMenu" />
				</div>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template name="ShowMenu">
		<ul id="main-menu">
			<xsl:for-each select="$ST//section[meta[@name = 'menu_header'] = 1]">
				<li>
					<xsl:if test="$isMain">	
						<xsl:attribute name="style">
							<xsl:text>font-size:14px;</xsl:text>	
						</xsl:attribute>
					</xsl:if>
					<a href="{@URL}" alt="{@title}">
						<xsl:value-of select="@title" />
					</a>
					<xsl:if test="count(./section[@content = 1])">
						<div class="dropdown">
				       		<div class="arrow"></div>
				       		<ul>
								<xsl:for-each select="section">
									<li>
										<a href="{@URL}" alt="{@title}">
											<xsl:value-of select="@title" />
										</a>
									</li>
								</xsl:for-each>
							</ul>
			 			</div>	
					</xsl:if>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>
	
	<xsl:template name="HeaderMenu">
		<ul>
   			<li>
   				<xsl:if test="not($isMain)">
					<xsl:attribute name="class">active</xsl:attribute>
				</xsl:if>
   				<a href="{$prefix}">Home</a>
   			</li>
   			<xsl:for-each select="$ST//section[meta[@name = 'menu_header'] = 1]">
   				<xsl:variable name="isActive" select="$SR/@id = @id"/>
   				<li>
   					<xsl:if test="not($isActive)">
   						<xsl:attribute name="class">active</xsl:attribute>
   					</xsl:if>
   					<a href="{@URL}" alt="{@title}">
						<xsl:value-of select="@title" />
					</a>
   				</li>
   			</xsl:for-each>
   		</ul>
	</xsl:template>
</xsl:stylesheet>