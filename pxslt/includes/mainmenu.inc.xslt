<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<xsl:template name="GenMenu">
		<xsl:param name="root"/>
		<ul class="sb_menu">
			<xsl:for-each select="$root/section[@hidden = 0]">
				<xsl:variable name="isActive" select="$SR/@id = @id"/>
				<li>
					<xsl:if test="$isActive">
                        <xsl:attribute name="class">open</xsl:attribute>
                    </xsl:if>
                    <a>
                        <xsl:attribute name="href">
                            <xsl:choose>
                                <xsl:when test="@content = 0 and ./section[@content = 1]">
                                    <xsl:value-of select="./section[@content = 1]/@URL"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="@URL"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>
                        <xsl:value-of select="@title"/>
                    </a>
                    <xsl:if test="$isActive">
                        <xsl:call-template name="GenSubMenu" />
                    </xsl:if>    
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>
	
	<!-- Генерация 2-го уровня меню -->
	<xsl:template name="GenSubMenu">
		<xsl:if test="section[@hidden = 0]">
			<xsl:for-each select="section[@hidden = 0]">
				<div class="sb_menu_second">
					<xsl:variable name="isActiveSubMenu" select="$SR/@id = @id"/>
					<a>
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="@content = 0 and ./section[@content = 1]">
									<xsl:value-of select="./section[@content = 1]/@URL"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@URL"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:value-of select="@title"/>
					</a>
					<!--xsl:if test="$isActiveSubMenu">
						<xsl:call-template name="GenThirdLevelMenu"/>
					</xsl:if-->	
				</div>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<!-- Генерация 3-го уровня меню -->
	<xsl:template name="GenThirdLevelMenu">
		<xsl:if test="section[@hidden = 0]">
			<xsl:for-each select="section[@hidden = 0]">
				<xsl:variable name="isActiveThirdLevelMenu" select="$SR/@id = @id"/>
				<div class="submenu3">
					<a>
						<xsl:if test="$isActiveThirdLevelMenu">
							<xsl:attribute name="style">
								<xsl:text>text-decoration:underline;</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="@content = 0 and ./section[@content = 1]">
									<xsl:value-of select="./section[@content = 1]/@URL"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@URL"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:value-of select="@title"/>
					</a>
					<xsl:if test="$isActiveThirdLevelMenu">
						<xsl:call-template name="GenLevelMenu"/>
					</xsl:if>	
				</div>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<!-- Генерация последующих уровней меню -->
	<xsl:template name="GenLevelMenu">
		<xsl:if test="section[@hidden = 0]">
			<xsl:for-each select="section[@hidden = 0]">
				<xsl:variable name="isActiveLevelMenu" select="$SR/@id = @id"/>
				<div>	
					<a>
						<xsl:if test="$isActiveLevelMenu">
							<xsl:attribute name="style">text-decoration:underline;</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="@content = 0 and ./section[@content = 1]">
									<xsl:value-of select="./section[@content = 1]/@URL"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@URL"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:value-of select="@title"/>
					</a>
					<xsl:if test="$isActiveLevelMenu">
						<xsl:call-template name="GenLevelMenu"/>
					</xsl:if>
				</div>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
