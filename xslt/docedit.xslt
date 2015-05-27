<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:if test="info/item[@name = 'DocWasSaved']">
			<script type="text/javascript">
				openerURLNew = escape((window.opener) ? window.opener.location.href : "NoPopup");
				if ((openerURLNew == "<xsl:value-of select="info/item[@name = 'opener']"/>") &amp;&amp; (openerURLNew != "NoPopup")) {
					window.opener.location.href = unescape("<xsl:value-of select="info/item[@name = 'opener']"/>");
				}
				window.close();
			</script>
		</xsl:if>
		<xsl:variable name="doc" select="document"/>
		<xsl:variable name="gvars" select="vars/general"/>
		<xsl:variable name="doctype" select="doctype"/>
		<xsl:variable name="isnew">
			<xsl:choose>
				<!-- Возникли ошибки при редактировании -->
				<xsl:when test="$SC//vars">2</xsl:when>
				<!-- Создание нового документа -->
				<xsl:when test="$Query/param[@name = 'id'] = 0 or $Query/param[@name = 'subid'] = 0">1</xsl:when>
				<!-- Редактирование документа -->
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="isrealnew">
			<xsl:choose>
				<xsl:when test="$Query/param[@name = 'id'] = 0 or $Query/param[@name = 'subid'] = 0">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="$isrealnew = 1">
				<h1>Создание документа (<xsl:value-of select="$doctype/@title"/>)</h1>
			</xsl:when>
			<xsl:otherwise>
				<h1>Редактирование документа (<xsl:value-of select="$doctype/@title"/>)</h1>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="error/item">
			<div class="divListErrors">
				<div class="divListErrorsHead">При сохранении документа возникли следующие ошибки</div>
				<xsl:call-template name="DTErrors"/>
			</div>
		</xsl:if>
		<xsl:if test="$doctype/field[@type = 'table']">
			<script type="text/javascript" charset="UTF-8" src="{$Query/@jscore}exceltables.js"/>
		</xsl:if>
		<form enctype="multipart/form-data" action="{$prefix}" method="post">
			<!--input type="hidden" name="errpath" value="{concat('http://', $Query/@host, $Query/@query, '&amp;ser=xml')}"/-->
			<input type="hidden" name="id" value="{$Query/param[@name = 'id']}"/>
			<input type="hidden" name="ref" value="{@id}"/>
			<input type="hidden" name="qref" value="{$Query/param[@name = 'qref']}"/>
			<xsl:if test="$Query/param[@name = 'subname'] and $Query/param[@name = 'subid']">
				<input type="hidden" name="subname" value="{$Query/param[@name = 'subname']}"/>
				<input type="hidden" name="subid" value="{$Query/param[@name = 'subid']}"/>
			</xsl:if>
			<input type="hidden" name="writemodule">
				<xsl:choose>
					<xsl:when test="@writeModule != ''">
						<xsl:attribute name="value"><xsl:value-of select="@writeModule"/></xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="value">DocWriting</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>
			</input>
			<input type="hidden" id="opener" name="opener" value="NoPopup"/>
			<script type="text/javascript">
				document.getElementById("opener").value = escape((window.opener) ? window.opener.location.href : "NoPopup");
			</script>
			<xsl:if test="$Query/param[@name = 'retpath']">
				<input type="hidden" name="retpath" value="{$Query/param[@name = 'retpath']}"/>
			</xsl:if>
			<div class="divFormStar">
				<span class="star">*</span> - поля, обязательные для заполнения<br/>
			</div>
			<div class="formField">
				<xsl:if test="$doctype/@enabledIsHidden = 0 or $role/@dtSuperAccess = 1">
					<input type="checkbox" name="enabled" id="enabled" value="1" class="form cb">
						<xsl:choose>
							<xsl:when test="$isnew = 2">
								<xsl:if test="$gvars/var[@name = 'enabled'] = 1">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</xsl:when>
							<xsl:when test="$isnew = 0">
								<xsl:if test="$doc/@enabled = 1">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</xsl:when>
							<xsl:when test="$isnew = 1">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:when>
						</xsl:choose>
					</input>
					<label for="enabled">Активен</label>
					<br/>
				</xsl:if>
			</div>
			<xsl:call-template name="ListFields">
				<xsl:with-param name="doctype" select="$doctype"/>
				<xsl:with-param name="doc" select="$doc"/>
				<xsl:with-param name="gvars" select="$gvars"/>
				<xsl:with-param name="isnew" select="$isnew"/>
				<xsl:with-param name="isrealnew" select="$isrealnew"/>
			</xsl:call-template>
			<xsl:choose>
				<xsl:when test="$isrealnew = 1">
					<xsl:call-template name="showLinks">
						<xsl:with-param name="document" select="."/>
					</xsl:call-template>
					<xsl:call-template name="showMultiRefs">
						<xsl:with-param name="refs" select="//refsNode"/>
						<xsl:with-param name="isrealnew" select="$isrealnew"/>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="showLinks">
						<xsl:with-param name="document" select="document"/>
					</xsl:call-template>
					<xsl:call-template name="showMultiRefs">
						<xsl:with-param name="refs" select="//refsNode"/>
						<xsl:with-param name="isrealnew" select="$isrealnew"/>
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
			<div class="divFormButton">
				<input type="submit" value="Сохранить"/>
			</div>
		</form>
		<xsl:for-each select="$doctype/field[@type = 'table']">
			<form id="tbl_upload_form_{@name}" enctype="multipart/form-data" action="{$prefix}excel-tables/" target="tbl_upload_buffer_{@name}" method="post" class="hide"/>
			<iframe id="tbl_upload_buffer_{@name}" name="tbl_upload_buffer_{@name}" src="about:blank" class="hide"/>
			<!--xsl:attribute name="style">visibility: visible; width: 300px; height: 300px;</xsl:attribute-->
		</xsl:for-each>
	</xsl:template>
	<xsl:template name="showLinks">
		<xsl:param name="document" />
		<div class="linkedDocs">
			<script type="text/javascript" src="{$Query/@jscore}linkeddocs.js"/>
			<xsl:if test="$document/link">
				<h4>Связанные документы</h4>
				<xsl:for-each select="$document/link">
					<xsl:variable name="linkedDocsObject" select="concat('links_', @docTypeName, '_object')"/>
					<!-- поле, где хранятся id выбранных элементов-->
					<input id="links_{@docTypeName}_selectedids" name="links_{@docTypeName}_selectedids" type="hidden"/>
					<!-- поле, где хранится идентификатор "зафиксированны ли изменения" -->
					<input name="links_{@docTypeName}_ismodified" id="links_{@docTypeName}_ismodified" type="hidden" value="0"/>
					<div class="linkedDoc">
						<span style="font-weight:bold; margin-right:5px;">
							<xsl:value-of select="@description"/>
						</span>
						<a id="links_{@docTypeName}_modechanger" href="javascript:void(0)" onclick="{$linkedDocsObject}.ChangeActive(); return false;" class="aModeChanger">
							<xsl:text>[редактировать]</xsl:text>
						</a>
						<!-- div со списком выбранных документов"-->
						<div id="links_{@docTypeName}_div_selected" name="links_{@docTypeName}_div_selected" class="divSelected"/>
						<!-- div со списком всех документов прикрепляемого ТД"-->
						<div id="links_{@docTypeName}_div_all" class="divLinkHidden">
							<div class="divContainer">
								<xsl:for-each select="document">
									<xsl:sort select="field[@name = 'title']" order="ascending"/>
									<div>
										<input id="links_{@docTypeName}_{@id}" type="checkbox" onchange="{$linkedDocsObject}.ModifyElement({@id}, this.checked);">
											<xsl:if test="aux[@name = 'selected'] = 1">
												<xsl:attribute name="checked"><xsl:text>checked</xsl:text></xsl:attribute>
											</xsl:if>
										</input>
										<label id="links_{@docTypeName}_{@id}_label" for="links_{@docTypeName}_{@id}">
											<xsl:value-of select="field[@name ='title']"/>
										</label>
										<a href="{@URL}" id="links_{@docTypeName}_{@id}_href" target="_blank" class="about" title="Открыть документ в новом окне">
											<xsl:text>[?]</xsl:text>
										</a>
									</div>
								</xsl:for-each>
							</div>
							<div class="divButtons">
								<input type="button" value="Изменить" onclick="{$linkedDocsObject}.ApplyChanges(); return false;"/>
								<input type="button" value="Отменить" onclick="{$linkedDocsObject}.CancelChanges(); return false;"/>
							</div>
						</div>
					</div>
					<script type="text/javascript">
						var <xsl:value-of select="$linkedDocsObject"/> = new LinksArray();
						<xsl:value-of select="$linkedDocsObject"/>.construct('<xsl:value-of select="@docTypeName"/>');
						<xsl:for-each select="document">
							<xsl:value-of select="$linkedDocsObject"/>.AddElement(<xsl:value-of select="@id"/>, <xsl:value-of select="aux[@name = 'selected']"/>);
						</xsl:for-each>
						<xsl:value-of select="$linkedDocsObject"/>.ShowSelectedDiv();
					</script>
				</xsl:for-each>
			</xsl:if>
		</div>
	</xsl:template>
	<xsl:template name="showMultiRefs">
		<xsl:param name="refs"/>
		<xsl:param name="isrealnew"/>
		<div class="refs">
			<script type="text/javascript" src="{$Query/@jscore}multiref.js"/>
			<xsl:if test="$refs/ref">
				<xsl:variable name="linkedDocsObject" select="'ref_gallery_object'"/>
				<!-- поле, где хранятся id выбранных элементов-->
				<input id="ref_selectedids" name="ref_selectedids" type="hidden">
					<xsl:if test="$isrealnew = 1">
						<xsl:attribute name="value"><xsl:value-of select="$Query/param[@name = 'qref']"/></xsl:attribute>
					</xsl:if>
				</input>
				<!-- поле, где хранится идентификатор "зафиксированны ли изменения" -->
				<input name="ref_ismodified" id="ref_ismodified" type="hidden" value="0"/>
				<div class="linkedDocs">
					<span style="font-weight:bold; margin-right:5px; font-size: 15px;">
						<xsl:text>Отображать в разделах</xsl:text>
					</span>
					<a id="ref_modechanger" href="javascript:void(0)" onclick="{$linkedDocsObject}.ChangeActive(); return false;" class="aModeChanger">
						<xsl:text>[редактировать]</xsl:text>
					</a>
					<!-- div со списком выбранных документов"-->
					<div id="ref_div_selected" name="ref_div_selected" class="divSelected"/>
					<!-- div со списком всех документов прикрепляемого ТД"-->
					<div id="ref_div_all" class="divLinkHidden">
						<div class="divContainer">
							<xsl:for-each select="$refs/ref">
								<xsl:sort select="text()" order="ascending"/>
								<div>
									<input id="ref_{@id}" type="checkbox" onchange="{$linkedDocsObject}.ModifyElement({@id}, this.checked);">
										<xsl:if test="(@selected = 1) or ($isrealnew = 1 and $Query/param[@name = 'qref'] = @id)">
											<xsl:attribute name="checked"><xsl:text>checked</xsl:text></xsl:attribute>
										</xsl:if>
									</input>
									<label id="ref_{@id}_label" for="ref_{@id}">
										<xsl:value-of select="text()"/>
									</label>
									<a href="{@URL}" id="ref_{@id}_href" target="_blank" class="about" title="Открыть документ в новом окне">
										<xsl:text>[?]</xsl:text>
									</a>
								</div>
							</xsl:for-each>
						</div>
						<div class="divButtons">
							<input type="button" value="Изменить" onclick="{$linkedDocsObject}.ApplyChanges(); return false;"/>
							<input type="button" value="Отменить" onclick="{$linkedDocsObject}.CancelChanges(); return false;"/>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					var <xsl:value-of select="$linkedDocsObject"/> = new MultiRefClass();
					<xsl:value-of select="$linkedDocsObject"/>.construct();
					<xsl:for-each select="$refs/ref">
						<xsl:variable name="refSelected">
							<xsl:choose>
								<xsl:when test="$isrealnew = 1">
									<xsl:choose>
										<xsl:when test="$Query/param[@name = 'qref'] = @id">1</xsl:when>
										<xsl:otherwise>0</xsl:otherwise>
									</xsl:choose>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@selected"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<xsl:value-of select="$linkedDocsObject"/>.AddElement(<xsl:value-of select="@id"/>, <xsl:value-of select="$refSelected"/>);
					</xsl:for-each>
					<xsl:value-of select="$linkedDocsObject"/>.ShowSelectedDiv();
				</script>
			</xsl:if>
		</div>
	</xsl:template>
</xsl:stylesheet>
