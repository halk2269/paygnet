<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:variable name="doc" select="document"/>
		<xsl:variable name="gvars" select="vars/general"/>
		<xsl:variable name="doctype" select="doctype"/>
		<xsl:variable name="isnew">
			<xsl:choose>
				<xsl:when test="$SC//vars">2</xsl:when>
				<xsl:when test="not($Visitor/@id)">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="isrealnew">
			<xsl:choose>
				<xsl:when test="not($Visitor/@id)">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="not($Visitor/@id)">
				<p><b>Для регистрации заполните нижеследующие поля</b></p>
			</xsl:when>
		</xsl:choose>
		<xsl:if test="info/item[@name = 'DocWasSaved']">
			<div>Ваш профиль сохранён!</div>
		</xsl:if>
		<xsl:if test="error/item">
			<div class="divListErrors">
				<h2>При сохранении документа возникли следующие ошибки</h2>
				<xsl:call-template name="DTErrors" />
			</div>
		</xsl:if>

		<xsl:if test="not(info/item[@name = 'DocWasSaved'])">
			<form enctype="multipart/form-data" action="{$prefix}" method="post">
				<input type="hidden" name="writemodule" value="Profile"/>
				<input type="hidden" name="ref" value="{@id}"/>
				<xsl:if test="$Query/param[@name = 'retpath']">
					<input type="hidden" name="retpath" value="{$Query/param[@name = 'retpath']}" />
				</xsl:if>
				<div>
					<span class="star">*</span> - поля, обязательные для заполнения<br/>
				</div>
				<xsl:call-template name="ListFields">
					<xsl:with-param name="doctype" select="$doctype" />
					<xsl:with-param name="doc" select="$doc" />
					<xsl:with-param name="gvars" select="$gvars" />
					<xsl:with-param name="isnew" select="$isnew" />
					<xsl:with-param name="isrealnew" select="$isrealnew" />
				</xsl:call-template>
				<p class="center">
					<xsl:choose>
						<xsl:when test="$Visitor/@id">
							<input type="submit" class="submit" value="Сохранить изменения"/>
						</xsl:when>
						<xsl:otherwise>
							<input type="submit" class="submit" value="Зарегистрироваться"/>
						</xsl:otherwise>
					</xsl:choose>
				</p>
			</form>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>