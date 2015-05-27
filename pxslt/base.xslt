<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://global-card.ru" exclude-result-prefixes="idm">
	<!-- Это надо переопределить для проекта -->
	<xsl:output method="html" media-type="text/html" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" omit-xml-declaration="yes" encoding="UTF-8" indent="yes" extension-element-prefixes="exsl" />
	<xsl:variable name="projectTitle" select="'Math olympiads'"/> <!-- Заголовок по умолчанию -->
	<xsl:variable name="homeName" select="'main'"/> <!-- Имя стартовой секции -->
	<xsl:variable name="projectName" select="'Math olympiads'"/> <!-- Английскими буквами без пробелов -->
	
	<!-- А вот это трогать не надо -->
	<xsl:include href="includes/init.inc.xslt"/>
	<xsl:variable name="home" select="$ST//section[@name = $homeName]"/>
	<xsl:variable name="isMain" select="$homeName = $section/@name"/>
	<xsl:include href="#includes/admin.inc.xslt"/>
	<xsl:include href="includes/paging.inc.xslt"/>
	<xsl:include href="includes/dterrors.inc.xslt"/>
	<xsl:include href="includes/dtmove.inc.xslt"/>
	<xsl:include href="includes/dtshow.inc.xslt"/>
	<xsl:include href="includes/head.inc.xslt"/>
	<xsl:include href="includes/functions.inc.xslt"/>
	<xsl:include href="includes/edit.inc.xslt"/> 
	<xsl:include href="includes/modulerights.inc.xslt"/> 
	<xsl:include href="#includes/authorization.inc.xslt"/>
	<xsl:include href="#includes/header.inc.xslt"/>
	<xsl:include href="#includes/content.inc.xslt"/>
	<xsl:include href="#includes/footer.inc.xslt"/>
	<xsl:include href="#includes/newsblock.inc.xslt"/>
	<xsl:include href="#includes/teamblock.inc.xslt"/>
	<xsl:include href="#includes/eventblock.inc.xslt"/>
	<xsl:include href="#includes/mainmenu.inc.xslt"/>
	<xsl:template match="text()"/>
	
	<!-- Основной шаблон -->
	<xsl:template>
		<html>
			<head>
				<xsl:call-template name="htmlHead" />
				<!-- CuFon: Enables smooth pretty custom font rendering. To disable, remove this section -->
				<script type="text/javascript" src="{prefix}js/cufon-yui.js"></script>
				<script type="text/javascript" src="{prefix}js/georgia.js"></script>
				<script type="text/javascript" src="{prefix}js/cuf_run.js"></script>
				<!-- CuFon ends -->
				
				<script type="text/javascript" src="{$Query/@jscore}jquery-1.11.1.min.js" />
				<script type="text/javascript" src="{$Query/@jscore}jquery-ui.js" />
				
				<script type="text/javascript">
				    $(document).ready(function(){
					    $('#main-menu .dropdown').each(function(){
					        var parentW = $(this).parent().outerWidth();
					        $(this).css('left', (-$(this).outerWidth() / 2) + parentW / 2);
					    });
					});	    
				</script>		    
			</head>
			<body>
				<xsl:choose>
					<!-- Стандартное отображение данных -->
					<xsl:when test="not($Query/param[@name = 'print' or @name = 'showtable'])">
						<div class="main">
							<xsl:call-template name="DisplayHeader" />
							<xsl:call-template name="DisplayContent" />
							<xsl:call-template name="DisplayFooter" />
							<xsl:call-template name="moduleRightsEdit"/>
						</div>	
					</xsl:when>
					<!-- -->
					<xsl:when test="$Query/param[@name = 'showtable']">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="100%" style="padding: 20px;">
									<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 5px;">
										<tr>
											<td>
												<div id="printButton">
													<a href="javascript:void(0);" onclick='printButton.style.display="none";window.print();printButton.style.display=""'>Print page</a>
												</div>
											</td>
											<td>
												<div align="right">
													<a href="javascript:void(0);" onclick="window.close()">Close window</a>
												</div>
											</td>
										</tr>
									</table>
									<xsl:apply-templates />
									<div align="right">
										<a href="javascript:void(0);" onclick="window.close()">Close window</a>
									</div>
								</td>
							</tr>
						</table>
					</xsl:when>
					<!-- -->
					<xsl:otherwise>
						<table align="center" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
							<tr>
								<td class="printTopTable">
									<div class="printurl rightalign">
										<xsl:variable name="URL">
											<xsl:call-template name="DeleteInQuery">
												<xsl:with-param name="paramName" select="'print'"/>
											</xsl:call-template>
										</xsl:variable>
										<xsl:text>Web page: </xsl:text>
										<a href="{$URL}">http://<xsl:value-of select="$Query/@host"/>
											<xsl:value-of select="$URL"/>
										</a>
									</div>
									<h1 class="printVersion">
										<xsl:choose>
											<xsl:when test="not($SCT/meta[@name = 'title'])">
												<xsl:text>Mathematics competitions :: </xsl:text>
												<xsl:value-of select="$section/@title"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="$SCT/meta[@name = 'title']"/>
											</xsl:otherwise>
										</xsl:choose>
									</h1>
									<xsl:apply-templates />
								</td>
							</tr>
						</table>
					</xsl:otherwise>
				</xsl:choose>
					
				<!--xsl:choose>
					<xsl:when test="$SC/section[@name = 'adminfaq']" />
					<xsl:otherwise>
						<div class="m">
							<xsl:call-template name="edit" />
							<xsl:call-template name="authorizationBox"/>
							<a href="{$prefix}">On main</a>
							<br />
							<xsl:for-each select="$ST/section[@name = 'main']/section">
								<a href="{@URL}">
									<xsl:value-of select="@title"/>
								</a>
								<br/>
							</xsl:for-each>
						</div>
					</xsl:otherwise>
				</xsl:choose>
				<div>
					<xsl:attribute name="class">
						<xsl:choose>
							<xsl:when test="$SC/section[@name = 'adminfaq']">faq</xsl:when>
							<xsl:otherwise>m</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
					<xsl:apply-templates/>
				</div>
				<xsl:call-template name="moduleRightsEdit"/-->
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>

