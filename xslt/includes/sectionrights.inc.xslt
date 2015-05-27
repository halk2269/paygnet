<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="sectionRightsEdit">
		<xsl:if test="$Visitor/role/@name = 'superadmin'">
			<xsl:if test="$SC/module[@name = 'sectionRightsEdit']/right">
				<script type="text/javascript" language="JavaScript">
					function changeVisibility() {
						var divFormContainer = document.getElementById('formContainer') || false;
						if ("none" == divFormContainer.style.display) {
							divFormContainer.style.display = "block";
						} else {
							divFormContainer.style.display = "none";
						}
					}
				</script>
				<script type="text/javascript">
					<![CDATA[
					document.write("<div style='padding-bottom: 7px;'><a onclick='changeVisibility()' style='cursor: pointer;'>Редактирование прав на секции »</a></div>");
					]]>
				</script>
				<noscript>
					<div style="padding-bottom: 7px; font-size: 12px;">Права на секции</div>
					<xsl:call-template name="drawRightsForm"/>
				</noscript>
				<div style="display: none;" id="formContainer">
					<xsl:call-template name="drawRightsForm"/>
				</div>
			</xsl:if>
		</xsl:if>
	</xsl:template>
	<xsl:template name="drawRightsForm">
		<form action="{$prefix}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="writemodule" value="SectionRights"/>
			<input type="hidden" name="ref" value="{$SC/module[@name = 'sectionRightsEdit']/@id}"/>
			<input type="hidden" name="qref" value="{$SC/module[@name = 'sectionRightsEdit']/@id}"/>
			<input type="hidden" name="section_id" value="{$Query/param[@name = 'id']}"/>
			<input type="hidden" name="retpath" value="{$Query/@query}"/>
			<input type="hidden" name="errpath" value="{$Query/@query}"/>
			<xsl:for-each select="$SC/module[@name = 'sectionRightsEdit']/right">
				<div style="padding-bottom: 5px;">
					<h3>
						<xsl:value-of select="@roleTitle"/>
						<xsl:text>:</xsl:text>
					</h3>
					<xsl:call-template name="drawCheckbox">
						<xsl:with-param name="rolePrefix" select="@roleId"/>
						<xsl:with-param name="right" select="'read'"/>
						<xsl:with-param name="value" select="@read"/>
					</xsl:call-template>
					<xsl:call-template name="drawCheckbox">
						<xsl:with-param name="rolePrefix" select="@roleId"/>
						<xsl:with-param name="right" select="'create'"/>
						<xsl:with-param name="value" select="@create"/>
					</xsl:call-template>
					<xsl:call-template name="drawCheckbox">
						<xsl:with-param name="rolePrefix" select="@roleId"/>
						<xsl:with-param name="right" select="'edit'"/>
						<xsl:with-param name="value" select="@edit"/>
					</xsl:call-template>
					<xsl:call-template name="drawCheckbox">
						<xsl:with-param name="rolePrefix" select="@roleId"/>
						<xsl:with-param name="right" select="'delete'"/>
						<xsl:with-param name="value" select="@delete"/>
					</xsl:call-template>
					<xsl:call-template name="drawCheckbox">
						<xsl:with-param name="rolePrefix" select="@roleId"/>
						<xsl:with-param name="right" select="'editName'"/>
						<xsl:with-param name="value" select="@editName"/>
					</xsl:call-template>
					<xsl:call-template name="drawCheckbox">
						<xsl:with-param name="rolePrefix" select="@roleId"/>
						<xsl:with-param name="right" select="'editEnabled'"/>
						<xsl:with-param name="value" select="@editEnabled"/>
					</xsl:call-template>
				</div>
			</xsl:for-each>
			<input type="submit" value="Изменить"/>
		</form>
	</xsl:template>
	<xsl:template name="drawCheckbox">
		<xsl:param name="rolePrefix"/>
		<xsl:param name="right"/>
		<xsl:param name="value"/>
		<div style="padding:0 10px 0 0px; width:100px; white-space:nowrap;">
			<input type="checkbox" id="{$rolePrefix}_{$right}" name="{$rolePrefix}_{$right}" style="vertical-align:middle;">
				<xsl:if test="$value = 1">
					<xsl:attribute name="checked"><xsl:text>checked</xsl:text></xsl:attribute>
				</xsl:if>
			</input>
			<label for="{$rolePrefix}_{$right}">
				<xsl:call-template name="printSectionRightInRussian">
					<xsl:with-param name="name" select="$right"/>
				</xsl:call-template>
				<!--xsl:value-of select="$right"/-->
			</label>
		</div>
	</xsl:template>
	<xsl:template name="printSectionRightInRussian">
		<xsl:param name="name"/>
		<xsl:choose>
			<xsl:when test="$name = 'read'">
				<xsl:text>Просмотр</xsl:text>
			</xsl:when>
			<xsl:when test="$name = 'create'">
				<xsl:text>Создание подразделов</xsl:text>
			</xsl:when>
			<xsl:when test="$name = 'edit'">
				<xsl:text>Редактирование</xsl:text>
			</xsl:when>
			<xsl:when test="$name = 'delete'">
				<xsl:text>Удаление</xsl:text>
			</xsl:when>
			<xsl:when test="$name = 'editName'">
				<xsl:text>Редактирование псевдонима</xsl:text>
			</xsl:when>
			<xsl:when test="$name = 'editEnabled'">
				<xsl:text>Редактирование «Включена/Выключена»</xsl:text>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
