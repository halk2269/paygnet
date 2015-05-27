<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:variable name="gvars" select="vars/general"/>
		<xsl:variable name="doctype" select="doctype"/>
		<xsl:variable name="isnew">
			<xsl:choose>
				<xsl:when test="$SC//vars">2</xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="isrealnew">1</xsl:variable>
		<xsl:choose>
			<xsl:when test="info/item[@name = 'DocWasSaved']">
				<div class="divFormSubmited">
					<xsl:value-of select="messageNode/@successText"/>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div class="divForm">
					<xsl:if test="error/item">
						<div class="divListErrors">
							<div class="divListErrorsHead">При обработке документа возникли следующие ошибки</div>
							<xsl:call-template name="DTErrors"/>
							<xsl:if test="error/item[@name = 'BadCaptcha']">
								<div class="divError">
									<xsl:text>Защитный код не введен или введен неверно.</xsl:text>
								</div>
							</xsl:if>
							<br/>
						</div>
					</xsl:if>
					<form enctype="multipart/form-data" action="{$prefix}" method="post">
						<input type="hidden" name="id" value="0"/>
						<input type="hidden" name="ref" value="{@id}"/>
						<input type="hidden" name="qref">
							<xsl:attribute name="value">
								<xsl:choose>
									<xsl:when test="$SC/module/feedbackNode">
										<xsl:value-of select="$SC/module/feedbackNode/@id"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="@id"/>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
						</input>
						<input type="hidden" name="writemodule" value="DocWriting"/>
						<!--input type="hidden" name="retpath" value="{concat('http://', $Query/@host, $Query/@url, '?ser=xml')}"/>
						<input type="hidden" name="errpath" value="{concat('http://', $Query/@host, $Query/@query, '&amp;ser=xml')}"/-->
						<div class="divFormStar">
							<xsl:if test="messageNode/@tosendText != ''">
								<xsl:value-of select="messageNode/@tosendText"/>
								<br/>
								<br/>
							</xsl:if>
							<span class="star">*</span>
							<xsl:text> &#8212; поля, обязательные для заполнения</xsl:text>
							<br/>
						</div>
						<xsl:call-template name="ListFields">
							<xsl:with-param name="doctype" select="$doctype"/>
							<xsl:with-param name="gvars" select="$gvars"/>
							<xsl:with-param name="isnew" select="$isnew"/>
							<xsl:with-param name="isrealnew" select="$isrealnew"/>
							<xsl:with-param name="doc" select="nothing"/>
						</xsl:call-template>
						<div class="formField">
							<xsl:text>Введите код, указанный на картинке:</xsl:text><br/>
							<img src="{$prefix}captcha/?time={generate-id()}" width="120" height="60" class="captchaImg"/>
							<br/>
							<input type="text" name="captcha" size="20" value="" />
						</div>
						<div class="divFormButton">
							<input type="submit" value="{messageNode/@buttonText}"/>
						</div>
					</form>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>