<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="no" doctype-system="http://www.w3.org/TR/REC-html40/strict.dtd" doctype-public="-//W3C//DTD HTML 4.0//EN"/>
	<xsl:include href="includes/init.inc.xslt" />
	<xsl:template>
		<html>
			<head>
				<title>Система управления сайтом. Доступ запрещён. Пожалуйста, авторизуйтесь</title>
				<link content-type="text/css" rel="stylesheet" href="{$Query/@css}admin.css"/>
			</head>
			<body>
				<div id="divFormCont" class="center">
					<form method="post" action="{$prefix}">
						<input type="hidden" name="writemodule" value="Authorize" />
						<input type="hidden" name="ref" value="{@id}" />
						<table id="tableAuth" cellpadding="0" cellspacing="0" border="0">
							<xsl:choose>
								<xsl:when test="var[@name = 'AuthType'] = 'CantModifyRef'">
									<tr>
										<td colspan="2" class="center autherr">Извините, у Вас нет прав на редактирование данного документа</td>
									</tr>
								</xsl:when>
								<xsl:when test="var[@name = 'AuthType'] = 'CantCreateDoc'">
									<tr>
										<td colspan="2" class="center autherr">Извините, у Вас нет прав на создание документа в данном разделе</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:if test="error/item[@name = 'AuthFail']">
								<tr>
									<td colspan="2" class="center autherr"><b>Введены неверные данные</b><br />Проверьте раскладку клавиатуры, Caps Lock и попробуйте ещё раз</td>
								</tr>
							</xsl:if>
							<tr>
								<td class="right">
									<h1>Логин</h1>
								</td>
								<td>
									<input id="selectfirst" class="huge" type="text" name="login" size="20" maxlenght="50" value="{vars/general/var[@name = 'login']}"/>
									<script type="text/javascript">
										document.getElementById("selectfirst").select	();
										document.getElementById("selectfirst").focus();
									</script>
								</td>
							</tr>
							<tr>
								<td class="right">
									<h1>Пароль</h1>
								</td>
								<td>
									<input class="huge" type="password" name="pass" size="20" maxlenght="50" value=""/>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="center">
									<input class="submit" type="submit" value="Вход"/>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="center">
									<a class="toMainPage" href="{$prefix}">Перейти к главной странице сайта</a>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</body>
		</html>
	</xsl:template>

</xsl:stylesheet>
