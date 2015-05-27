<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="adminCreate">
		<xsl:if test="@createURL">
			<div class="adminDivCreate">
				<a href="{@createURL}&amp;retpath={$Query/@queryEscaped}">Create&#160;new&#160;</a> (<a onclick="return openEditWindow('{@createURL}', '{@docTypeName}', '0')" href="javascript:void(0)">In&#160;opened&#160;window</a>)<br/>
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
							<img src="{$prefix}adminimg/edit.gif" alt="Edit" title="Edit"/>
						</a>
						<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">
							<img src="{$prefix}adminimg/editnewwin.gif" alt="Edit Popup" title="Edit in opened window"/>
						</a>
					</xsl:if>
					<xsl:if test="@deleteURL">
						<a onclick="return confirm('Are you sure?')" href="{@deleteURL}">
							<img src="{$prefix}adminimg/delete.gif" alt="Delete" title="Delete"/>
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
							<img src="{$prefix}adminimg/edit.gif" alt="Edit" title="Edit"/>
						</a>
						<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">
							<img src="{$prefix}adminimg/editnewwin.gif" alt="Edit Popup" title="Edit in opened window"/>
						</a>
					</xsl:if>
				</span>
			</nobr>
		</xsl:if>
	</xsl:template>
	<xsl:template name="adminEditDelText">
		<xsl:if test="@editURL">
			<div class="adminEditDelText">
				<a href="{@editURL}&amp;retpath={$Query/@queryEscaped}">Edit</a>
				<xsl:text> | </xsl:text>
				<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">Edit&#160;in opened&#160;window</a>
				<br/>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
