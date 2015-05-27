<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output encoding="UTF-8" method="html" indent="yes"/>
	<xsl:template match="root">
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
				<style type="text/css">
					body, html {
					padding: 0;
					margin: 0;
					background: #FFF;
					}
					body, td, p {
					font-size: 10pt;
					font-family: Verdana, sans-serif;
					color: #000;
					}
					a {
					color: #0D46A6;
					text-decoration: underline;
					}
					a:visited {
					color: #555;
					}
					img {
					border: 0;
					}
					ul, ol, li {
					margin: 0;
					padding: 0;
					}
					li {
					margin-left: 0px;
					}
					h1, h2, h3 {
					padding: 0;
					margin: 0;
					}
					h1 {
					margin: 10px 0px;
					font-size: 13pt;
					font-weight: bold;
					}
					h2 {
					margin: 10px 0px;
					font-size: 11pt;
					font-weight: bold;
					}
					h3 {
					font-size: 10pt;
					text-decoration: underline;
					}
					div.base {
					padding: 10px;
					}
					table.tab td {
					padding: 1px 15px 1px 0px;
					}
				</style>
			</head>
			<body style="font-size: 16px; font-family: Verdana, Tahoma, Arial, Sans-serif;">
				<div class="base">
					<xsl:choose>
						<xsl:when test="/root/restoreData/@mode = 'Link'">
							<h2>Здравствуйте, <xsl:value-of select="/root/restoreData/@login"/>!</h2>
							<p>
								<xsl:text>Вы запросили восстановление пароля на сайте </xsl:text>
								<a href="http://{/root/QueryParams/@host}{/root/QueryParams/@prefix}">
									<xsl:value-of select="/root/QueryParams/@host"/>
								</a>
								<xsl:text/>
							</p>
							<p>Для получения нового пароля перейдите, пожалуйста, по ссылке</p>
							<p>
								<a>
									<xsl:attribute name="href"><xsl:value-of select="/root/restoreData/@link"/></xsl:attribute>
									<xsl:value-of select="/root/restoreData/@link"/>
								</a>
								<p>
									<xsl:text>Эта ссылка активна до </xsl:text>
									<strong>
										<xsl:value-of select="/root/restoreData/@date"/>
									</strong>
								</p>
							</p>
						</xsl:when>
						<xsl:otherwise>
							<h2>Здравствуйте, <xsl:value-of select="/root/restoreData/@login"/>!</h2>
							<p>
								<xsl:text>Вы запросили восстановление пароля на сайте </xsl:text>
								<a href="http://{/root/QueryParams/@host}{/root/QueryParams/@prefix}">
									<xsl:value-of select="/root/QueryParams/@host"/>
								</a>
								<xsl:text/>
							</p>
							<p>
								<xsl:text>Ваш новый пароль: </xsl:text>
								<xsl:value-of select="/root/restoreData/@pass"/>
							</p>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
