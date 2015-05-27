<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:msxsl="urn:schemas-microsoft-com:xslt" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm msxsl">
	<xsl:output indent="yes" method="html" doctype-public="-//W3C//DTD HTML 4.0//EN" doctype-system="http://www.w3.org/TR/REC-html40/strict.dtd"/>
	<xsl:include href="includes/init.inc.xslt"/>
	<xsl:include href="includes/dtshow.inc.xslt"/>
	<xsl:include href="includes/dterrors.inc.xslt"/>
	<xsl:template>
		<html>
			<head>
				<title>Создание/Редактирование документа</title>
				<base href="http://{$Query/@host}{$prefix}"/>
				<link content-type="text/css" rel="stylesheet" href="{$Query/@css}docedit.css"/>
				
				<script type="text/javascript" src="{$Query/@corePath}FCKeditor/fckeditor.js"/>
				<script type="text/javascript">
					window.onload = function() {
						<xsl:for-each select="$SC/module/doctype/field[@type = 'text' and (@mode = 'wyswyg')]">
							<xsl:variable name="id" select="position()"/>
							var oFCKeditor<xsl:value-of select="$id"/> = new FCKeditor('<xsl:value-of select="@name"/>');
							oFCKeditor<xsl:value-of select="$id"/>.BasePath = "<xsl:value-of select="$Query/@corePath"/>FCKeditor/";
							oFCKeditor<xsl:value-of select="$id"/>.Height = "300";
							oFCKeditor<xsl:value-of select="$id"/>.Prefix = "<xsl:value-of select="$prefix"/>";
							oFCKeditor<xsl:value-of select="$id"/>.SID = "<xsl:value-of select="$Query/@SID"/>";
							oFCKeditor<xsl:value-of select="$id"/>.ToolbarSet = "CMSToolbar";
							oFCKeditor<xsl:value-of select="$id"/>.ReplaceTextarea();
						</xsl:for-each>
					}
				</script>
			</head>
			<body>
				<div class="divForm">
					<xsl:apply-templates/>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>