<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:variable name="gvars" select="../vars/general"/>
		<xsl:variable name="doc" select="../document[@docTypeName = 'user']"/>
		<xsl:variable name="rInfo" select="../rInfo"/>
		<xsl:variable name="isnew">
			<xsl:choose>
				<xsl:when test="$SC//vars">2</xsl:when>
				<xsl:when test="not(../document)">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="isDocSaved" select="../info/item[@name = 'DocWasSaved'] and not(../error/item)"/>
		<xsl:if test="$isDocSaved">
			<strong>
				<xsl:text>Данные пользователя сохранены</xsl:text>
			</strong>
		</xsl:if>
		<xsl:if test="../error/item">
			<div id="divPopupErrors">
				<div class="divListErrorsHead">При обработке документа возникли следующие ошибки</div>
				<xsl:call-template name="DTErrors">
					<xsl:with-param name="doctype" select="../doctype"/>
					<xsl:with-param name="erritems" select="../error/item"/>
				</xsl:call-template>
			</div>
		</xsl:if>
		<xsl:if test="not($isDocSaved)">
			<form enctype="multipart/form-data" action="{$prefix}" method="post">
				<input type="hidden" name="writemodule" value="User"/>
				<input type="hidden" name="ref" value="{../@id}"/>
				<input type="hidden" name="qref" value="{../@id}"/>
				<input type="hidden" name="id">
					<xsl:attribute name="value">
						<xsl:choose>
							<xsl:when test="@id">
								<xsl:value-of select="@id"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:text>0</xsl:text>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
				</input>
				<input type="hidden" name="rolename" value="{$Query/param[@name = 'rolename']}"/>
				<input type="hidden" name="retpath" value="{$SCT/@URL}"/>
				<div>
					<span class="star">
						<xsl:text>*</xsl:text>
					</span>
					<xsl:text> &#8212; поля, обязательные для заполнения</xsl:text>
					<br/>
				</div>
				<input type="checkbox" name="enabled" id="enabled" value="1" class="form cb">
					<xsl:choose>
						<xsl:when test="$isnew = 2">
							<xsl:if test="$gvars/var[@name = 'enabled'] = 1">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
						</xsl:when>
						<xsl:when test="$isnew = 0">
							<xsl:if test="$doc/@enabled = 1">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
						</xsl:when>
						<xsl:when test="$isnew = 1">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
					</xsl:choose>
				</input>
				<label for="enabled">Активен</label>
				<br/>
				<xsl:call-template name="ListFields">
					<xsl:with-param name="doctype" select="../doctype[@name = 'user']"/>
					<xsl:with-param name="doc" select="../document[@docTypeName = 'user']"/>
					<xsl:with-param name="gvars" select="$gvars"/>
					<xsl:with-param name="isnew" select="$isnew"/>
					<xsl:with-param name="isrealnew" select="0"/>
				</xsl:call-template>
				<xsl:call-template name="ListFields">
					<xsl:with-param name="doctype" select="../doctype[@name != 'user']"/>
					<xsl:with-param name="doc" select="../document[@docTypeName != 'user']"/>
					<xsl:with-param name="gvars" select="$gvars"/>
					<xsl:with-param name="isnew" select="$isnew"/>
					<xsl:with-param name="isrealnew" select="0"/>
				</xsl:call-template>
				<p class="center">
					<input type="submit" class="submit" value="Сохранить изменения"/>
				</p>
			</form>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
