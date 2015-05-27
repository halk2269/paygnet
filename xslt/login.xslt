<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:variable name="authWithOrder">
			<xsl:choose>
				<xsl:when test="$Query/param[@name='authwithorder']">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<!--xsl:call-template name="debugVars" /-->
		<form method="post" action="{$prefix}">
			<input type="hidden" name="writemodule" value="Authorize" />
			<input type="hidden" name="ref" value="{@id}" />
			<input type="hidden" name="authWithOrder" value="{$authWithOrder}" />
			<xsl:choose>
				<xsl:when test="$Query/param[@name = 'retpath']">
					<input type="hidden" name="retpath" value="{$Query/param[@name = 'retpath']}" />
				</xsl:when>
				<xsl:otherwise>
					<input type="hidden" name="retpath" value="{$prefix}" />
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="error/item[@name = 'AuthFail']">
				<p><b>Введены неверные данные</b></p>
				<p>Проверьте раскладку клавиатуры, Caps Lock и попробуйте ещё раз</p>
			</xsl:if>
			<p>Логин<br />
				<input id="selectfirst" type="text" name="login" size="20" maxlenght="50" value="{vars/general/var[@name = 'login']}"/>
				<script type="text/javascript">
					document.getElementById("selectfirst").select();
					document.getElementById("selectfirst").focus();
				</script>
			</p>
			<p>Пароль<br />
				<input type="password" name="pass" size="20" maxlenght="50" value=""/>
			</p>
			<p>
				<input class="submit" type="submit" value="Вход"/>
			</p>
		</form>
	</xsl:template>
</xsl:stylesheet>
