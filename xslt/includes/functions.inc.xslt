<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<!-- Дата в формате 07.11.2009 -->
	<xsl:template name="GetDate">
		<xsl:param name="date" select="/root/@date"/>
		<xsl:value-of select="substring($date, 9, 2)"/>.<xsl:value-of select="substring($date, 6, 2)"/>.<xsl:value-of select="substring($date, 1, 4)"/>
	</xsl:template>
	<!-- Дата и время в формате 07.11.2009 23:22 -->
	<xsl:template name="DateTimeFromDateTime">
		<xsl:param name="datetime" select="concat(/root/@date, ' ', /root/@time)"/>
		<xsl:value-of select="substring($datetime, 9, 2)"/>.<xsl:value-of select="substring($datetime, 6, 2)"/>.<xsl:value-of select="substring($datetime, 1, 4)"/>
		<xsl:text> </xsl:text>
		<xsl:value-of select="substring($datetime, 12, 2)"/>:<xsl:value-of select="substring($datetime, 15, 2)"/>
	</xsl:template>
	<!-- Дата и время в формате 07.11.2009 23:22 (из TIMESTAMP, после перехода на MySQL 4.1 не используется, 
	так как TIMESTAMP выдаётся из базы в том же формате, что и DATETIME -->
	<xsl:template name="DateTimeFromTimeStamp">
		<xsl:param name="timestamp"/>
		<xsl:value-of select="substring($timestamp, 7, 2)"/>.<xsl:value-of select="substring($timestamp, 5, 2)"/>.<xsl:value-of select="substring($timestamp, 1, 4)"/>
		<xsl:text> </xsl:text>
		<xsl:value-of select="substring($timestamp, 9, 2)"/>:<xsl:value-of select="substring($timestamp, 11, 2)"/>
	</xsl:template>
	<!-- Время в формате 23:22 -->
	<xsl:template name="TimeFromDateTime">
		<xsl:param name="datetime" select="concat(/root/@date, ' ', /root/@time)"/>
		<xsl:value-of select="substring($datetime, 12, 2)"/>:<xsl:value-of select="substring($datetime, 15, 2)"/>
	</xsl:template>
	<!-- Дата и время в формате 07.11.2009 23:22, вместо чисел для соотв. дат вставляется "Сегодня в " и "Вчера в " -->
	<xsl:template name="AdvancedForumDate">
		<xsl:param name="datetime" select="concat(/root/@date, ' ', /root/@time)"/>
		<xsl:choose>
			<xsl:when test="/root/@date = substring($datetime, 1, 10)">
				<xsl:text>Сегодня в </xsl:text>
				<xsl:call-template name="TimeFromDateTime">
					<xsl:with-param name="datetime" select="$datetime"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="../yesterday/text() = substring($datetime, 1, 10)">
						<xsl:text>Вчера в </xsl:text>
						<xsl:call-template name="TimeFromDateTime">
							<xsl:with-param name="datetime" select="$datetime"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="DateTimeFromDateTime">
							<xsl:with-param name="datetime" select="$datetime"/>
						</xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!-- Отладка - информация из нод info и error -->
	<xsl:template name="debugVars">
		<xsl:param name="path" select="."/>
		<xsl:if test="$path/info">
			<h5>[Info]</h5>
			<xsl:for-each select="$path/info/item">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="$path/vars//var">
			<h5>[Vars]</h5>
			<xsl:if test="$path/vars/own/var">
				<h6>- Own</h6>
				<xsl:for-each select="$path/vars/own/var">
					<strong>
						<xsl:value-of select="@name"/>
					</strong>: <xsl:value-of select="."/>
					<br/>
				</xsl:for-each>
			</xsl:if>
			<xsl:if test="$path/vars/general/var">
				<h6>- General</h6>
				<xsl:for-each select="$path/vars/general/var">
					<strong>
						<xsl:value-of select="@name"/>
					</strong>: <xsl:value-of select="."/>
					<br/>
				</xsl:for-each>
			</xsl:if>
			<xsl:if test="$path/vars/user/var">
				<h6>- User</h6>
				<xsl:for-each select="$path/vars/user/var">
					<strong>
						<xsl:value-of select="@name"/>
					</strong>: <xsl:value-of select="."/>
					<br/>
				</xsl:for-each>
			</xsl:if>
		</xsl:if>
		<xsl:if test="$path/error/item">
			<h5>[Errors]</h5>
			<xsl:for-each select="$path/error/item">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	<!-- Отладка - информация из нод rInfo и rError -->
	<xsl:template name="debugrVars">
		<xsl:param name="path" select="."/>
		<xsl:if test="$path/rInfo">
			<h5>[Info]</h5>
			<xsl:for-each select="$path/rInfo/item">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="$path/rVars/var">
			<h5>[Vars]</h5>
			<xsl:for-each select="$path/rVars/var">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="$path/rError/item">
			<h5>[Errors]</h5>
			<xsl:for-each select="$path/rError/item">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	<xsl:template name="ReplaceInQuery">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:param name="paramValue"/>
		<xsl:choose>
			<xsl:when test="$Query/@staticURL = 1">
				<xsl:call-template name="ReplaceInStaticQuery">
					<xsl:with-param name="paramPrefix" select="$paramPrefix"/>
					<xsl:with-param name="paramName" select="$paramName"/>
					<xsl:with-param name="paramValue" select="$paramValue"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="ReplaceInDynamicQuery">
					<xsl:with-param name="paramPrefix" select="$paramPrefix"/>
					<xsl:with-param name="paramName" select="$paramName"/>
					<xsl:with-param name="paramValue" select="$paramValue"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!-- Замена значения параметра paramName в строке запроса на новое paramValue -->
	<xsl:template name="ReplaceInDynamicQuery">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:param name="paramValue"/>
		<xsl:variable name="tmp" select="concat($paramPrefix, $paramName)"/>
		<xsl:value-of select="$Query/@url"/>
		<xsl:text>?</xsl:text>
		<xsl:for-each select="$Query/param">
			<xsl:if test="position() != 1">
				<xsl:text>&amp;</xsl:text>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="@name = $tmp">
					<xsl:if test="$paramValue != ''">
						<xsl:value-of select="$tmp"/>=<xsl:value-of select="$paramValue"/>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@name"/>=<xsl:value-of select="@escaped"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
		<xsl:if test="not($Query/param[@name = $tmp]) and $paramValue != ''">
			<xsl:if test="$Query/param">
				<xsl:text>&amp;</xsl:text>
			</xsl:if>
			<xsl:value-of select="$tmp"/>=<xsl:value-of select="$paramValue"/>
		</xsl:if>
	</xsl:template>
	<!-- Замена значения параметра paramName в строке запроса на новое paramValue для статичных урлов -->
	<xsl:template name="ReplaceInStaticQuery">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:param name="paramValue"/>
		<xsl:variable name="tmp" select="concat($paramPrefix, $paramName)"/>
		<xsl:value-of select="$Query/@url"/>
		<xsl:for-each select="$Query/param">
			<xsl:choose>
				<xsl:when test="@name = $tmp">
					<xsl:if test="$paramValue != ''">
						<xsl:value-of select="$tmp"/>
						<xsl:text>/</xsl:text>
						<xsl:value-of select="$paramValue"/>
						<xsl:text>/</xsl:text>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@name"/>
					<xsl:text>/</xsl:text>
					<xsl:value-of select="@escaped"/>
					<xsl:text>/</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
		<xsl:if test="not ($Query/param[@name = $tmp]) and $paramValue != ''">
			<xsl:value-of select="$tmp"/>
			<xsl:text>/</xsl:text>
			<xsl:value-of select="$paramValue"/>
			<xsl:text>/</xsl:text>
		</xsl:if>
	</xsl:template>
	<!-- Удаление параметра paramName в строке запроса -->
	<xsl:template name="DeleteInQuery">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:choose>
			<xsl:when test="$Query/@staticURL = '1'">
				<xsl:call-template name="DeleteInQueryStatic">
					<xsl:with-param name="paramPrefix" select="$paramPrefix"/>
					<xsl:with-param name="paramName" select="$paramName"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="DeleteInQueryDynamic">
					<xsl:with-param name="paramPrefix" select="$paramPrefix"/>
					<xsl:with-param name="paramName" select="$paramName"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="DeleteInQueryDynamic">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:variable name="tmp" select="concat($paramPrefix, $paramName)"/>
		<xsl:value-of select="$Query/@url"/>
		<xsl:if test="$Query/param[1]/@name != $tmp or $Query/param[2]">
			<xsl:text>?</xsl:text>
		</xsl:if>
		<xsl:for-each select="$Query/param[@name != $tmp]">
			<xsl:if test="position() != 1">
				<xsl:text>&amp;</xsl:text>
			</xsl:if>
			<xsl:value-of select="@name"/>=<xsl:value-of select="@escaped"/>
		</xsl:for-each>
	</xsl:template>
	<xsl:template name="DeleteInQueryStatic">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:variable name="tmp" select="concat($paramPrefix, $paramName)"/>
		<xsl:value-of select="$Query/@url"/>
		<xsl:for-each select="$Query/param[@name != $tmp]">
			<xsl:value-of select="@name"/>
			<xsl:text>/</xsl:text>
			<xsl:value-of select="@escaped"/>
			<xsl:text>/</xsl:text>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
