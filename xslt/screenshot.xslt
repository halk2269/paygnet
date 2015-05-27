<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:msxsl="urn:schemas-microsoft-com:xslt" xmlns:idm="urn:http://www.infodesign.ru">
	<xsl:decimal-format name="rus" decimal-separator="," grouping-separator=" "/>
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
	<xsl:template match="text()"/>
	<xsl:variable name="prefix" select="/root/QueryParams/@prefix" />
	<xsl:template>
		<html>
			<head>
				<title>Некоммерческая ассоциация «Культурно-деловой центр молодежи». Нажмите на изображение для закрытия окна.</title>
				<style type="text/css">
          body, td {
            font-family: tahoma, arial, helvetica, geneva, serif;
            color: #002531;
            font-size: 11px;
          }
          body {
            background: #FFF;
            padding: 0;
            margin: 0;
            text-align: center;
          }
          td {
            text-align: center;
            vertical-align: middle;
          }
          img {
            border: 1px solid #F0F0F0;
          }
        </style>
			</head>
			<body>
				<table cellpadding="0" cellspacing="0" height="100%" width="100%">
					<tr>
						<td>
							<a href="javascript:window.close()">
								<img src="{/root/QueryParams/param[@name = 'url']}" border="0" hspace="0" vspace="0" />
							</a>
						</td>
					</tr>
				</table>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
