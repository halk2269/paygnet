<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="adminCreate">
		<xsl:if test="@createURL">
			<div class="adminDivCreate">
				<a href="{@createURL}&amp;retpath={$Query/@queryEscaped}">Создать&#160;новый&#160;документ</a> (<a onclick="return openEditWindow('{@createURL}', '{@docTypeName}', '0')" href="javascript:void(0)">Во&#160;всплывающем&#160;окне</a>)<br/>
			</div>
		</xsl:if>
	</xsl:template>
	<xsl:template name="adminEditDel">
		<xsl:param name="nopadding" select="0"/>
		<xsl:if test="@editURL or @deleteURL">
			<nobr>
				<span>
					<xsl:choose>
						<xsl:when test="$nopadding = 0">
							<xsl:attribute name="class">spanEditDel</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">spanEditDel np</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
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
			</nobr>
		</xsl:if>
	</xsl:template>
	<xsl:template name="adminEdit">
		<xsl:if test="@editURL">
			<nobr>
				<span class="spanEditDel">
					<xsl:if test="@editURL">
						<a href="{@editURL}&amp;retpath={$Query/@queryEscaped}">
							<img src="{$prefix}adminimg/edit.gif" alt="Edit" title="Редактировать документ"/>
						</a>
						<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">
							<img src="{$prefix}adminimg/editnewwin.gif" alt="Edit Popup" title="Редактировать документ во всплывающем окне"/>
						</a>
					</xsl:if>
				</span>
			</nobr>
		</xsl:if>
	</xsl:template>
	<xsl:template name="adminEditDelText">
		<xsl:if test="@editURL">
			<div class="adminEditDelText">
				<a href="{@editURL}&amp;retpath={$Query/@queryEscaped}">Редактировать&#160;документ</a>
				<xsl:text> | </xsl:text>
				<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">Редактировать&#160;во&#160;всплывающем&#160;окне</a>
				<br/>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
