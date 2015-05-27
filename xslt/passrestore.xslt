<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<xsl:template>
		<xsl:for-each select="error/item">
			<xsl:choose>
				<xsl:when test="@name = 'NoUser'">
					<p>
						<xsl:text>Пользователя с логином </xsl:text>
						<strong>
							<xsl:value-of select="text()"/>
						</strong>
						<xsl:text> не существует</xsl:text>
					</p>
				</xsl:when>
				<xsl:when test="@name = 'NoData'">
					<p><xsl:text>Вы не заполнили поле «Логин»</xsl:text></p>
				</xsl:when>
				<xsl:when test="@name = 'NoSuchRequest'">
					<p>
						<xsl:text>Этот запрос на восстановление пароля не может быть выполнен. Возможные причины ошибки:</xsl:text>
						<ul>
							<li>Истёк срок действия ссылки, указанной в письме</li>
							<li>Вы уже переходили по указанной в письме ссылке</li>
						</ul>
						<xsl:text>Запросите восстановление пароля ещё раз.</xsl:text>
					</p>
				</xsl:when>
				<xsl:when test="@name = 'BadKey'">
					<p><xsl:text>Вы допустили ошибку в адресе восстановления пароля</xsl:text></p>
				</xsl:when>
				<xsl:when test="@name = 'UpdateFails'">
					<p><xsl:text>К сожалению, произошла техническая ошибка. Запросите восстановление пароля ещё раз. Приносим вам извинения за причиненные неудобства.</xsl:text></p>
				</xsl:when>
			</xsl:choose>
		</xsl:for-each>
		<xsl:for-each select="info/item">
			<xsl:choose>
				<xsl:when test="@name = 'LinkSent'">
					<xsl:text>Уважаемый пользователь </xsl:text>
					<strong>
						<xsl:value-of select="text()"/>
					</strong>
					<xsl:text>. На email, указанный в вашем профиле, выслано письмо со ссылкой на подтверждение восстановления пароля. Данная ссылка активна в течении 14 дней с момента запроса.</xsl:text>
				</xsl:when>
				<xsl:when test="@name = 'PassSent'">
					<xsl:text>Уважаемый пользователь </xsl:text>
					<strong>
						<xsl:value-of select="text()"/>
					</strong>
					<xsl:text>. На email, указанный в вашем профиле, выслан новый пароль.</xsl:text>
				</xsl:when>
			</xsl:choose>
		</xsl:for-each>
		<xsl:if test="not(info) and not(error)">
			<p><xsl:text>Если вы хотите изменить пароль или вы его забыли, введите свой логин и нажмите «Отправить». На e-mail, указанный вами при регистрации, будет выслано письмо со ссылкой на подтверждение восстановления пароля.</xsl:text></p>
		</xsl:if>
		<xsl:if test="not(info)">
			<xsl:call-template name="restoreForm"/>
		</xsl:if>
		
	</xsl:template>
	<xsl:template name="restoreForm">
		<form method="post" action="{$prefix}">
			<input type="hidden" name="writemodule" value="PassRestore"/>
			<input type="hidden" name="ref" value="{@id}"/>
			<input type="hidden" name="qref" value="{@id}"/>
			<p>Логин<br/>
				<input id="selectfirst" type="text" name="login" size="20" maxlenght="50" value=""/>
				<script type="text/javascript">
					document.getElementById("selectfirst").select();
					document.getElementById("selectfirst").focus();
				</script>
			</p>
			<p>
				<input class="submit" type="submit" value="Отправить"/>
			</p>
		</form>
	</xsl:template>
</xsl:stylesheet>
