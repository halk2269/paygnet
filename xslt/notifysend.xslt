<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:variable name="Query" select="/root/QueryParams"/>
	<xsl:variable name="Doc" select="/root/document"/>
	<xsl:output encoding="UTF-8" method="html" indent="yes" />
	<xsl:template match="/">
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			</head>
			<body style="font-size: 12px; font-family: Verdana, Tahoma, Arial, Sans-serif;">
				<xsl:choose>
					<xsl:when test="$Query/@action = 'subscribe'">
						<p>
							Здравствуйте!<br /><br />
							Вы подписались на рассылку сайта <xsl:value-of select="$Query/@host" />.<br />
							Чтобы активировать Вашу подписку, необходимо ее подтвердить. Для подтверждения
							подписки и получения рассылок, пожалуйста, перейдите по ссылке:
							<br /><br />
							<xsl:variable name="url" select="concat('http://', $Query/@host, $Query/@prefix, '?writemodule=', $Query/@writeModuleName, '&amp;uid=', $Doc/field[@name = 'uid'], '&amp;ref=', $Query/@ref, '&amp;qref=', $Query/@qref, '&amp;id=0')" />
							<a href="{$url}"><xsl:value-of select="$url" disable-output-escaping="yes" /></a>
							<br /><br />
							Если Вы считаете, что получили это письмо по ошибке - просто удалите его.
						</p>
					</xsl:when>
					<xsl:when test="$Query/@action = 'unsubscribe'">
						<p>
							Здравствуйте!<br /><br />
							Вы хотите отказаться от подписки рассылки сайта <xsl:value-of select="$Query/@host" />.<br />
							Чтобы подтвердить ваш отказ, Вам необходимо его подтвердить. Для подтверждения
							отказа от получения рассылок, пожалуйста, перейдите по ссылке:
							<br /><br />
							<xsl:variable name="url" select="concat('http://', $Query/@host, $Query/@prefix, '?writemodule=', $Query/@writeModuleName, '&amp;uid=', $Doc/field[@name = 'uid'], '&amp;ref=', $Query/@ref, '&amp;qref=', $Query/@qref, '&amp;id=0')" />
							<a href="{$url}"><xsl:value-of select="$url" disable-output-escaping="yes" /></a>
							<br /><br />
							Если Вы считаете, что получили это письмо по ошибке - просто удалите его.
						</p>
					</xsl:when>
				</xsl:choose>
				<p>
				С уважением, <br />
				администрация <xsl:value-of select="$Query/@host" />.
				</p>
			</body>
		</html>		
	</xsl:template>
</xsl:stylesheet>