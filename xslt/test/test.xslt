<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<xsl:output indent="yes" method="html" doctype-system="http://www.w3.org/TR/html4/loose.dtd" doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN"/>
	<xsl:decimal-format decimal-separator="." grouping-separator=" "/>
	<xsl:variable name="ST" select="/root/SectionTree"/>
	<xsl:variable name="SC" select="/root/SectionCurrent"/>
	<xsl:variable name="Query" select="/root/QueryParams"/>
	<xsl:variable name="Visitor" select="/root/Visitor"/>
	<xsl:variable name="prefix" select="$Query/@prefix"/>
	<xsl:variable name="section" select="$SC/section"/>
	<xsl:variable name="SCT" select="$ST//section[@id = $section/@id]"/>
	<xsl:variable name="SR" select="$ST//section[@id=$section/@id]/ancestor-or-self::section"/>
	<xsl:variable name="homeName">ru</xsl:variable>
	<xsl:variable name="home" select="$ST//section[@name = $homeName]"/>
	<xsl:variable name="isMain" select="$homeName = $section/@name"/>
	<xsl:variable name="userName" select="'необходимо вывести в xml фио'" />
	<xsl:variable name="userLogin" select="$Visitor/@login" />
	<xsl:template name="CreateURLLevel1">
		<xsl:param name="sec"/>
		<xsl:choose>
			<xsl:when test="$sec/@Content = 1 or not($sec/section[@hidden = 0])">
				<xsl:value-of select="$sec/@URL"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$sec/section[@hidden = 0][1]/@URL"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="GetDate">
		<xsl:param name="date" select="/root/@date"/>
		<xsl:value-of select="substring($date, 9, 2)"/>.<xsl:value-of select="substring($date, 6, 2)"/>.<xsl:value-of select="substring($date, 1, 4)"/>
	</xsl:template>
	<xsl:template name="DateTimeFromDateTime">
		<xsl:param name="datetime"/>
		<xsl:value-of select="substring($datetime, 9, 2)"/>.<xsl:value-of select="substring($datetime, 6, 2)"/>.<xsl:value-of select="substring($datetime, 1, 4)"/>
		<xsl:text> </xsl:text>
		<xsl:value-of select="substring($datetime, 12, 2)"/>:<xsl:value-of select="substring($datetime, 15, 2)"/>
	</xsl:template>
	<xsl:template name="DateTimeFromTimeStamp">
		<xsl:param name="timestamp"/>
		<xsl:value-of select="substring($timestamp, 7, 2)"/>.<xsl:value-of select="substring($timestamp, 5, 2)"/>.<xsl:value-of select="substring($timestamp, 1, 4)"/>
		<xsl:text> </xsl:text>
		<xsl:value-of select="substring($timestamp, 9, 2)"/>:<xsl:value-of select="substring($timestamp, 11, 2)"/>
	</xsl:template>
	<xsl:template name="adminCreate">
		<xsl:if test="@createURL">
			<div class="adminDivCreate">
				<a href="{@createURL}&amp;retpath={$Query/@queryEscaped}">Создать&#160;новый&#160;документ</a> (<a onclick="return openEditWindow('{@createURL}', '{@docTypeName}', '0')" href="javascript:void(0)">Во&#160;всплывающем&#160;окне</a>)<br/>
			</div>
		</xsl:if>
	</xsl:template>
	<xsl:template name="adminEditDel">
		<span class="spanEditDel">
			<xsl:if test="@editURL">
				<a href="{@editURL}&amp;retpath={$Query/@queryEscaped}">
					<img src="{$prefix}adminimg/edit.gif" alt="Edit" title="Редактировать документ"/>
				</a>
				<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">
					<img src="{$prefix}adminimg/editnewwin.gif" alt="Edit Popup" title="Редактировать документ во всплывающем окне"/>
				</a>
			</xsl:if>
			<xsl:if test="@deleteURL">
				<a onclick="return confirm('Вы действительно хотите удалить этот документ?')" href="{@deleteURL}">
					<img src="{$prefix}adminimg/delete.gif" alt="Delete" title="Удалить документ"/>
				</a>
			</xsl:if>
		</span>
	</xsl:template>
	<xsl:template name="adminEditDelText">
		<xsl:if test="@editURL">
			<a href="{@editURL}&amp;retpath={$Query/@queryEscaped}">Редактировать&#160;документ</a>
			<xsl:text> | </xsl:text>
			<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">Редактировать&#160;во&#160;всплывающем&#160;окне</a>
		</xsl:if>
		<!--
		<xsl:if test="@deleteURL">
			<xsl:if test="@editURL">
				<xsl:text> | </xsl:text>
			</xsl:if>
			<a onclick="return confirm('Вы действительно хотите удалить этот документ?')" href="{@deleteURL}">Удалить&#160;документ</a>
		</xsl:if>
		-->
	</xsl:template>
	<xsl:template name="debugVars">
		<h3>[Info]</h3>
		<xsl:for-each select="info/item">
			<strong>
				<xsl:value-of select="@name"/>
			</strong>: <xsl:value-of select="."/>
			<br/>
		</xsl:for-each>
		<h3>[Vars]</h3>
		<h3>- Own</h3>
		<xsl:for-each select="vars/own/var">
			<strong>
				<xsl:value-of select="@name"/>
			</strong>: <xsl:value-of select="."/>
			<br/>
		</xsl:for-each>
		<h3>- General</h3>
		<xsl:for-each select="vars/general/var">
			<strong>
				<xsl:value-of select="@name"/>
			</strong>: <xsl:value-of select="."/>
			<br/>
		</xsl:for-each>
		<h3>- User</h3>
		<xsl:for-each select="vars/user/var">
			<strong>
				<xsl:value-of select="@name"/>
			</strong>: <xsl:value-of select="."/>
			<br/>
		</xsl:for-each>
	</xsl:template>
	<xsl:template match="text()" />
	<xsl:template>
		<html>
			<head>
				<title>ТЕСТОВАЯ СЕКЦИЯ</title>
				<base href="http://{$Query/@host}{$prefix}"/>
				<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
				<link rel="stylesheet" href="{$prefix}css.css" type="text/css"/>
				<script type="text/javascript">
					function openEditWindow(url, dt, id) {
						$pos = (window.opera) ? ", left=100, top=100" : "";
						nw = window.open(url, "kdcm_" + dt + id, "status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, width=800, height=600" + $pos);
						nw.focus();
						return false; // Блокирование перехода на ссылку. Ф-ция должна вызываться как "return openEditWindow(...)"
					}
				</script>
			</head>
			<body>
				<div style="padding: 10px">
					<b>Тестовая секция</b>
				</div>
				<div style="padding: 10px">
					<xsl:apply-templates />
				</div>
			</body>
		</html>
	</xsl:template>
	
	<xsl:template name="ReplaceInQuery">
		<xsl:param name="paramName" />
		<xsl:param name="paramValue" />
		<xsl:param name="paramPrefix" />
		<xsl:variable name="tmp" select="concat($paramPrefix, $paramName)" />
		<xsl:text>?</xsl:text>
		<xsl:for-each select="$Query/param">
			<xsl:if test="position() != 1">
				<xsl:text>&amp;</xsl:text>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="@name = $tmp">
					<xsl:if test="$paramValue != ''">
						<xsl:value-of select="$tmp" />=<xsl:value-of select="$paramValue" />
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@name" />=<xsl:value-of select="." />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
		<xsl:if test="not ($Query/param[@name = $tmp]) and $paramValue != ''">
			<xsl:if test="$Query/param">&amp;</xsl:if>
			<xsl:value-of select="$tmp" />=<xsl:value-of select="$paramValue" />
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="GetPath">
		<xsl:param name="ST" />
		<xsl:param name="sid" />
		<xsl:variable name="CS" select="$ST//section[@id = $sid]" />
		<xsl:value-of select="$CS/parent::section/@title" /> &gt; <xsl:value-of select="$CS/@title" />
	</xsl:template>
		
</xsl:stylesheet>
