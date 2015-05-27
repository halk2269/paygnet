<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="moduleRightsEdit">
		<xsl:if test="$Visitor/role/@name = 'superadmin' and $SC/module[@name = 'moduleRightsEdit']">
			<script type="text/javascript" language="JavaScript">
					function changeVisibility(alink) {
						var moduleRightsContainer = document.getElementById('moduleRightsContainer') || false;
						if (!moduleRightsContainer) return;
						if (!moduleRightsContainer.style.display) moduleRightsContainer.style.display = "none";
						moduleRightsContainer.style.display = ("none" == moduleRightsContainer.style.display) ? "block" : "none";
						
						alink.innerHTML = ("none" == moduleRightsContainer.style.display) 
						? "Редактирование прав на модули (развернуть)"
						: "Редактирование прав на модули (свернуть)";
					}
				</script>
			<div class="divModuleRights" id="divModuleRights">
				<a onclick="changeVisibility(this)">
					<xsl:text>Редактирование прав на модули (развернуть)</xsl:text>
				</a>
				<div id="moduleRightsContainer" class="moduleRightsContainer">
					<xsl:call-template name="drawModuleRightsForm"/>
				</div>
			</div>
			<script type="text/javascript" language="JavaScript">
			var moduleRightsContainer = document.getElementById('moduleRightsContainer').style.display = "none";
			</script>
		</xsl:if>
	</xsl:template>
	<xsl:template name="drawModuleRightsForm">
		<form action="{$prefix}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="writemodule" value="ModuleRights"/>
			<input type="hidden" name="ref" value="{$SC/module[@name = 'moduleRightsEdit']/@id}"/>
			<input type="hidden" name="qref" value="{$SC/module[@name = 'moduleRightsEdit']/@id}"/>
			<input type="hidden" name="section_id" value="{$SCT/@id}"/>
			<input type="hidden" name="retpath" value="{$Query/@query}"/>
			<input type="hidden" name="errpath" value="{$Query/@query}"/>
			<xsl:for-each select="$SC/module[@name = 'moduleRightsEdit']/module">
				<div class="module">
					<strong>
						<xsl:text>Модуль </xsl:text>
						<xsl:value-of select="@id"/>
						<xsl:if test="@docTypeName">
							<xsl:text> (тип документа "</xsl:text>
							<xsl:value-of select="@docTypeName"/>
							<xsl:text>")</xsl:text>
						</xsl:if>
					</strong>
					<xsl:for-each select="right">
						<div class="role">
							<xsl:value-of select="@roleTitle"/>
						</div>
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'read'"/>
									<xsl:with-param name="value" select="@read"/>
								</xsl:call-template>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'create'"/>
									<xsl:with-param name="value" select="@create"/>
								</xsl:call-template>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'createEnabled'"/>
									<xsl:with-param name="value" select="@createEnabled"/>
								</xsl:call-template>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'edit'"/>
									<xsl:with-param name="value" select="@edit"/>
								</xsl:call-template>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'delete'"/>
									<xsl:with-param name="value" select="@delete"/>
								</xsl:call-template>
							</tr>
						</table>
					</xsl:for-each>
				</div>
			</xsl:for-each>
			<div style="text-align: center;">
				<input type="submit" value="Изменить"/>
			</div>
		</form>
	</xsl:template>
	<xsl:template name="drawModuleRightCheckbox">
		<xsl:param name="moduleId"/>
		<xsl:param name="rolePrefix"/>
		<xsl:param name="right"/>
		<xsl:param name="value"/>
		<td style="padding:0 10px 0 0px; white-space:nowrap;">
			<input type="checkbox" id="{$moduleId}_{$rolePrefix}_{$right}" name="{$moduleId}_{$rolePrefix}_{$right}" style="vertical-align:middle;">
				<xsl:if test="$value = 1">
					<xsl:attribute name="checked"><xsl:text>checked</xsl:text></xsl:attribute>
				</xsl:if>
			</input>
			<label for="{$moduleId}_{$rolePrefix}_{$right}">
				<xsl:value-of select="$right"/>
			</label>
		</td>
	</xsl:template>
</xsl:stylesheet>
