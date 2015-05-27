<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<!-- выбор нового раздела для выделенных документов. должен быть вызван внутри module -->
	<xsl:template name="DocMoveOption">
		<xsl:variable name="curModule" select="@id"/>
		<xsl:variable name="docNumInCurModule" select="count(document)"/>
		<xsl:variable name="numSectWithSameDT" select="count($SC/module[@id = $curModule]/refEdit/allowedRef)"/>
		<xsl:if test="$SC/module[@id = $curModule]/refEdit and $docNumInCurModule > 0 and $numSectWithSameDT > 0">
			<xsl:variable name="pathLevelBeginFrom" select="$SC/module[@id = $curModule]/refEdit/@pathLevelBeginFrom"/>
			<noscript>
				<span style="color: #FF0000;">
					<xsl:text>В вашем браузере отключен или не поддерживается JavaScript. Вы не можете переносить документы в другие разделы.</xsl:text>
				</span>
			</noscript>
			<div class="divMoveForm">
				<form name="moveDocForm{$curModule}" id="moveDocForm{$curModule}" action="{$prefix}" method="POST">
					<input type="hidden" name="origRef" value="{$curModule}"/>
					<input type="hidden" name="elemToMove" value=""/>
					<input type="hidden" name="writemodule" value="Move"/>
					<select name="newRef" onchange="moveElements(this,{$curModule})" style="vertical-align: middle;">
						<option value="no">Для переноса документов выберите целевой раздел</option>
						<xsl:for-each select="$SC/module[@id = $curModule]/refEdit/allowedRef">
							<xsl:sort select="count($ST//section[@id = current()/@secId]/preceding::section)" data-type="number"/>
							<xsl:variable name="secId" select="@secId"/>
							<xsl:if test="$ST//section[@id = $secId]">
								<option value="{@id}">
									<xsl:for-each select="$ST//section[@id = $secId]/ancestor-or-self::section">
										<xsl:if test="position() &gt;= $pathLevelBeginFrom">
											<xsl:value-of select="@title"/>
											<xsl:if test="position() != last()">
												<xsl:text> / </xsl:text>
											</xsl:if>
										</xsl:if>
									</xsl:for-each>
								</option>
							</xsl:if>
						</xsl:for-each>
					</select>
					<div style="padding-top: 10px">
						<span>
							<input style="vertical-align: middle;" type="checkbox" name="goToNewRef" checked="true" id="toNew_{$curModule}"/>
							<label style="padding-left: 5px;" for="toNew_{$curModule}">перейти в целевой раздел</label>
						</span>
						<span style="padding-left: 30px">
							<input style="vertical-align: middle;" type="checkbox" name="leaveCope" id="leaveCope_{$curModule}"/>
							<label style="padding-left:5px;" for="leaveCope_{$curModule}">оставить копию в этом разделе</label>
						</span>
					</div>
				</form>
			</div>
		</xsl:if>
	</xsl:template>
	<xsl:template name="docMoveChecker">
		<xsl:param name="curModule" select="parent::module/@id"/>
		<xsl:param name="numSectWithSameDT" select="count($SC/module[@id = $curModule]/refEdit/allowedRef)"/>
		<xsl:if test="$SC/module[@id = $curModule]/refEdit and $numSectWithSameDT > 0">
			<input style="margin-right: 5px; vertical-align: middle;" type="checkbox" name="el_ref_{$curModule}_{@id}" onclick="changeElement(this)"/>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>