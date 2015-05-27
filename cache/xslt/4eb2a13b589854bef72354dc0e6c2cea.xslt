<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://global-card.ru" exclude-result-prefixes="idm">
	<!-- Это надо переопределить для проекта -->
	<xsl:output method="html" media-type="text/html" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" omit-xml-declaration="yes" encoding="UTF-8" indent="yes" extension-element-prefixes="exsl" />
	<xsl:variable name="projectTitle" select="'Math olympiads'"/> <!-- Заголовок по умолчанию -->
	<xsl:variable name="homeName" select="'main'"/> <!-- Имя стартовой секции -->
	<xsl:variable name="projectName" select="'Math olympiads'"/> <!-- Английскими буквами без пробелов -->
	
	<!-- А вот это трогать не надо -->
	

	<xsl:decimal-format decimal-separator="." grouping-separator=" "/>
	<xsl:variable name="ST" select="/root/SectionTree"/>
	<xsl:variable name="SC" select="/root/SectionCurrent"/>
	<xsl:variable name="Query" select="/root/QueryParams"/>
	<xsl:variable name="Visitor" select="/root/Visitor"/>
	<xsl:variable name="role" select="$Visitor/role"/>
	<xsl:variable name="prefix" select="$Query/@prefix"/>
	<xsl:variable name="section" select="$SC/section"/>
	<xsl:variable name="curSec" select="$section/@id"/>
	<xsl:variable name="SCT" select="$ST//section[@id = $section/@id]"/>
	<xsl:variable name="SR" select="$ST//section[@id = $section/@id]/ancestor-or-self::section"/>

	<xsl:variable name="home" select="$ST//section[@name = $homeName]"/>
	<xsl:variable name="isMain" select="$homeName = $section/@name"/>
	

	<xsl:template match="/root/SectionTree" />
<xsl:template match="/root/QueryParams" />
<xsl:template match="/root/Visitor" />
<xsl:template name="adminCreate">
		<xsl:if test="@createURL">
			<div class="adminDivCreate">
				<a href="{@createURL}&amp;retpath={$Query/@queryEscaped}">Create&#160;new&#160;</a> (<a onclick="return openEditWindow('{@createURL}', '{@docTypeName}', '0')" href="javascript:void(0)">In&#160;opened&#160;window</a>)<br/>
			</div>
		</xsl:if>
	</xsl:template>
	<xsl:template name="adminEditDel">
		<xsl:param name="nopadding" select="0"/>
		<xsl:if test="@editURL or @deleteURL">
			<nobr>
				<span>
					<xsl:choose>
						<xsl:when test="$nopadding = 0">
							<xsl:attribute name="class">spanEditDel</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">spanEditDel np</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="@editURL">
						<a href="{@editURL}&amp;retpath={$Query/@queryEscaped}">
							<img src="{$prefix}adminimg/edit.gif" alt="Edit" title="Edit"/>
						</a>
						<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">
							<img src="{$prefix}adminimg/editnewwin.gif" alt="Edit Popup" title="Edit in opened window"/>
						</a>
					</xsl:if>
					<xsl:if test="@deleteURL">
						<a onclick="return confirm('Are you sure?')" href="{@deleteURL}">
							<img src="{$prefix}adminimg/delete.gif" alt="Delete" title="Delete"/>
						</a>
					</xsl:if>
				</span>
			</nobr>
		</xsl:if>
	</xsl:template>
	<xsl:template name="adminEdit">
		<xsl:if test="@editURL">
			<nobr>
				<span class="spanEditDel">
					<xsl:if test="@editURL">
						<a href="{@editURL}&amp;retpath={$Query/@queryEscaped}">
							<img src="{$prefix}adminimg/edit.gif" alt="Edit" title="Edit"/>
						</a>
						<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">
							<img src="{$prefix}adminimg/editnewwin.gif" alt="Edit Popup" title="Edit in opened window"/>
						</a>
					</xsl:if>
				</span>
			</nobr>
		</xsl:if>
	</xsl:template>
	<xsl:template name="adminEditDelText">
		<xsl:if test="@editURL">
			<div class="adminEditDelText">
				<a href="{@editURL}&amp;retpath={$Query/@queryEscaped}">Edit</a>
				<xsl:text> | </xsl:text>
				<a onclick="return openEditWindow('{@editURL}', '{@docTypeName}', '{@id}')" href="javascript:void(0)">Edit&#160;in opened&#160;window</a>
				<br/>
			</div>
		</xsl:if>
	</xsl:template>


	

	<xsl:template name="Paging">
		<xsl:param name="className" select="''" />
		<xsl:if test="pages/page[2]">
			<div class="{$className}">
				<xsl:text>Страницы: </xsl:text>
				<xsl:for-each select="pages/page">
					<xsl:if test="position() = 1">
						<xsl:choose>
							<xsl:when test="not(@isCurrent)">
								<a title="Назад" style="text-decoration: none" href="{../page[@isCurrent]/preceding-sibling::page[1]/@URL}">&#8592;</a>
								<xsl:text> </xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<span class="disabledArrow">&#8592; </span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					<xsl:choose>
						<xsl:when test="@isCurrent">
							<b>
								<xsl:value-of select="@num" />
							</b>
						</xsl:when>
						<xsl:otherwise>
							<a href="{@URL}"><xsl:value-of select="@num" /></a>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="position() != last()">
						<xsl:text> | </xsl:text>
					</xsl:if>
					<xsl:if test="position() = last()">
						<xsl:choose>
							<xsl:when test="not(@isCurrent)">
								<xsl:text> </xsl:text>
								<a title="Вперёд" style="text-decoration: none" href="{../page[@isCurrent]/following-sibling::page[1]/@URL}">&#8594;</a>
							</xsl:when>
							<xsl:otherwise>
								<span class="disabledArrow"> &#8594;</span>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>

	

	<xsl:template name="DTErrors">
		<xsl:param name="doctype" select="doctype"/>
		<xsl:param name="erritems" select="error/item"/>
		<xsl:for-each select="$erritems">
			<xsl:variable name="description" select="$doctype/field[@name = current()]/@description"/>
			<xsl:choose>
				<xsl:when test="@name = 'BlankField'">
					<div class="divError">
						Не заполнено обязательное поле <strong>
							<xsl:value-of select="$description"/>
						</strong>
						<br/>
						<span class="desc">(Все обязательные поля подлежат заполнению)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'TooLong'">
					<div class="divError">
						Превышена максимальная длина для поля <strong>
							<xsl:value-of select="$description"/>
						</strong>
						<br/>
						<span class="desc">(Попробуйте уменьшить количество символов)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadInt'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> - неверное значение для целого типа<br/>
						<span class="desc">(Значения целого типа могут состоять только из цифр от &#171;0&#187; до &#171;9&#187;, и их размерность не может превышать 11 разрядов. Для отрицательных чисел первым символом может быть минус.)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadFloat'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> - число введено некорректно<br/>
						<span class="desc"/>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'NumberTooBig'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> - число слишком большое<br/>
						<span class="desc">(<xsl:if test="$doctype/field[@name = current()]/@max">Максимально допустимое значение: 
								<xsl:value-of select="$doctype/field[@name = current()]/@max"/>
							</xsl:if>
							<xsl:if test="$doctype/field[@name = current()]/@min">
								<xsl:choose>
									<xsl:when test="$doctype/field[@name = current()]/@max">, минимально допустимое значение: </xsl:when>
									<xsl:otherwise>Минимально допустимое значение: </xsl:otherwise>
								</xsl:choose>
								<xsl:value-of select="$doctype/field[@name = current()]/@min"/>
							</xsl:if>)
						</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'NumberTooSmall'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> - число слишком маленькое<br/>
						<span class="desc">(<xsl:if test="$doctype/field[@name = current()]/@max">Максимально допустимое значение: 
								<xsl:value-of select="$doctype/field[@name = current()]/@max"/>
							</xsl:if>
							<xsl:if test="$doctype/field[@name = current()]/@min">
								<xsl:choose>
									<xsl:when test="$doctype/field[@name = current()]/@max">, минимально допустимое значение: </xsl:when>
									<xsl:otherwise>Минимально допустимое значение: </xsl:otherwise>
								</xsl:choose>
								<xsl:value-of select="$doctype/field[@name = current()]/@min"/>
							</xsl:if>)
						</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BlankPassword'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; задан пустой пароль<br/>
						<span class="desc">(В целях безопасности пароль не может быть пустым)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadSelectID'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; выбрано неверное значение выпадающего списка<br/>
						<span class="desc">(Если ты хакер &#8212; тебе хана. ФСБ уже в пути. Дергай, пока не приехали)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadFileExt'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; неразрешённый тип файла<br/>
						<span class="desc">(Список разрешённых типов: <xsl:value-of select="$doctype/field[@name = current()]/@extensions"/>)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'TooLargeFile'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; файл слишком большой<br/>
						<span class="desc">(Максимально разрешённый размер: <xsl:value-of select="$doctype/field[@name = current()]/@maxSize"/>)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'ThisIsNotImage'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; файл не является файлом изображения<br/>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'PasswordsAreNotIdentical'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; введённый пароль и его подтверждение не совпадают.<br/>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadRegexp'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; введено некорректное значение.<br/>
						<span class="desc">(<xsl:value-of select="$doctype/field[@name = current()]/@regexpDescription"/>)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadImageSizes'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; файл изображения не соответствует разрешённым размерам.<br/>
						<span class="desc">(Изображение должно быть 
						<xsl:text> </xsl:text>
							<xsl:if test="$doctype/field[@name = current()]/@minWidth &gt; 0 and $doctype/field[@name = current()]/@minHeight &gt; 0">
							не меньше <xsl:value-of select="$doctype/field[@name = current()]/@minWidth"/>x<xsl:value-of select="$doctype/field[@name = current()]/@minHeight"/>
							</xsl:if>
							<xsl:text> </xsl:text>
							<xsl:if test="$doctype/field[@name = current()]/@maxWidth &gt; 0 and $doctype/field[@name = current()]/@maxHeight &gt; 0">
								<xsl:if test="$doctype/field[@name = current()]/@minWidth &gt; 0 and $doctype/field[@name = current()]/@minHeight &gt; 0">
									<xsl:text> и </xsl:text>
								</xsl:if>
							не больше <xsl:value-of select="$doctype/field[@name = current()]/@maxWidth"/>x<xsl:value-of select="$doctype/field[@name = current()]/@maxHeight"/>
							</xsl:if>
							<xsl:text> </xsl:text>
						пикселей)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'UserExists'">
					<div class="divError">
						<strong>
							<xsl:value-of select="."/>
						</strong> &#8212; данный логин уже существует. Выберите, пожалуйста, другой.<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'DuplicateEmail'">
					<div class="divError">
						<strong>
							<xsl:value-of select="."/>
						</strong> &#8212; данный e-mail уже существует в базе данных пользователей. Ввведите, пожалуйста, другой e-mail.<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'noRefSelected'">
					<div class="divError">
						<xsl:text>Вы не выбрали ни одного раздела</xsl:text>
						<br/>
						<span class="desc">(Документ должен быть привязан хотя бы к одному разделу)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'noProperRef'">
					<div class="divError">
						<xsl:text>Выбранная категория неверна</xsl:text>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'ImageMagickFailure'">
					<div class="divError">
						<xsl:text>Ошибка сервера (невозможно осуществить масштабирование изображения)</xsl:text>
						<br/>
						<span class="desc">(Обратитесь в службу технической поддержки вашего сайта)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'MainImageIsNotLoaded'">
					<div class="divError">
						<xsl:text>Основное изображение не было загружено</xsl:text>
						<br/>
						<span class="desc">(Если ты хакер &#8212; тебе хана. ФСБ уже в пути. Дергай, пока не приехали)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'LinkToSelf'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong>
						<xsl:text> &#8212; документ не может ссылаться сам на себя</xsl:text>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadDate'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong>
						<xsl:text> &#8212; введена некорректная дата</xsl:text>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'InvalidFile'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong>
						<xsl:text> &#8212; файл для формирования таблицы содержит ошибки</xsl:text>
					</div>
				</xsl:when>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>


	

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

	

	<xsl:template name="ListFields">
		<xsl:param name="doctype"/>
		<xsl:param name="doc" select="''"/>
		<xsl:param name="gvars"/>
		<xsl:param name="isnew"/>
		<xsl:param name="isrealnew"/>
		<xsl:param name="hidefield" select="''"/>
		<xsl:param name="present" select="'vertical'" />
		<xsl:param name="block" />
		<xsl:param name="blockName" />
		<xsl:choose>
			<xsl:when test="$block > 0 and $blockName != ''">
				<fieldset title="{$blockName}" class="formFieldset">
					<legend>
						<xsl:value-of select="$blockName" />
					</legend>
					<xsl:for-each select="$doctype/field[@block = $block]">
						<xsl:variable name="tmpType" select="@type"/>
						<xsl:variable name="tmpAlias" select="@name"/>
						<xsl:choose>
							<xsl:when test="(@cantedit and not($Visitor/role/@name = 'admin' or $Visitor/role/@name = 'superadmin')) or (@hiddenfromall = 1)"/>
							<xsl:when test="@name = $hidefield and $Visitor/@id"/>
							<xsl:when test="$tmpType = 'int'">
								<xsl:call-template name="tpl_int">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'float'">
								<xsl:call-template name="tpl_float">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'text'">
								<xsl:call-template name="tpl_text">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'select'">
								<xsl:call-template name="tpl_select">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'radio'">
								<xsl:call-template name="tpl_radio">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'multibox'">
								<xsl:call-template name="tpl_multibox">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var"/>
									<xsl:with-param name="isnew" select="$isnew"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'string'">
								<xsl:call-template name="tpl_string">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'bool'">
								<xsl:call-template name="tpl_bool">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'strlist'">
								<xsl:call-template name="tpl_strlist">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'file'">
								<xsl:call-template name="tpl_file">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'password'">
								<xsl:call-template name="tpl_password">
									<xsl:with-param name="view" select="$present" />
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="isrealnew" select="$isrealnew"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'date'">
								<xsl:call-template name="tpl_date">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'datetime'">
								<xsl:call-template name="tpl_datetime">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'image'">
								<xsl:call-template name="tpl_image">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="view" select="$present" />
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'table'">
								<xsl:call-template name="tpl_table">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'link'">
								<xsl:call-template name="tpl_link">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</fieldset>
			</xsl:when>
			<xsl:when test="$block = 0">
				<xsl:for-each select="$doctype/field[@block = 0]">
					<xsl:variable name="tmpType" select="@type"/>
					<xsl:variable name="tmpAlias" select="@name"/>
					<xsl:choose>
						<xsl:when test="(@cantedit and not($Visitor/role/@name = 'admin' or $Visitor/role/@name = 'superadmin')) or (@hiddenfromall = 1)"/>
						<xsl:when test="@name = $hidefield and $Visitor/@id"/>
						<xsl:when test="$tmpType = 'int'">
							<xsl:call-template name="tpl_int">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'float'">
							<xsl:call-template name="tpl_float">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'text'">
							<xsl:call-template name="tpl_text">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'select'">
							<xsl:call-template name="tpl_select">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'radio'">
							<xsl:call-template name="tpl_radio">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'multibox'">
							<xsl:call-template name="tpl_multibox">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'string'">
							<xsl:call-template name="tpl_string">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'bool'">
							<xsl:call-template name="tpl_bool">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'strlist'">
							<xsl:call-template name="tpl_strlist">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'file'">
							<xsl:call-template name="tpl_file">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'password'">
							<xsl:call-template name="tpl_password">
								<xsl:with-param name="view" select="$present" />
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isrealnew" select="$isrealnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'date'">
							<xsl:call-template name="tpl_date">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'datetime'">
							<xsl:call-template name="tpl_datetime">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'image'">
							<xsl:call-template name="tpl_image">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="view" select="$present" />
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'table'">
							<xsl:call-template name="tpl_table">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'link'">
							<xsl:call-template name="tpl_link">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<xsl:for-each select="$doctype/field">
					<xsl:variable name="tmpType" select="@type"/>
					<xsl:variable name="tmpAlias" select="@name"/>
					<xsl:choose>
						<xsl:when test="(@cantedit and not($Visitor/role/@name = 'admin' or $Visitor/role/@name = 'superadmin')) or (@hiddenfromall = 1)"/>
						<xsl:when test="@name = $hidefield and $Visitor/@id"/>
						<xsl:when test="$tmpType = 'int'">
							<xsl:call-template name="tpl_int">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'float'">
							<xsl:call-template name="tpl_float">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'text'">
							<xsl:call-template name="tpl_text">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'select'">
							<xsl:call-template name="tpl_select">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'radio'">
							<xsl:call-template name="tpl_radio">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'multibox'">
							<xsl:call-template name="tpl_multibox">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'string'">
							<xsl:call-template name="tpl_string">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'bool'">
							<xsl:call-template name="tpl_bool">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'strlist'">
							<xsl:call-template name="tpl_strlist">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'file'">
							<xsl:call-template name="tpl_file">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'password'">
							<xsl:call-template name="tpl_password">
								<xsl:with-param name="view" select="$present" />
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isrealnew" select="$isrealnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'date'">
							<xsl:call-template name="tpl_date">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'datetime'">
							<xsl:call-template name="tpl_datetime">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'image'">
							<xsl:call-template name="tpl_image">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="view" select="$present" />
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'table'">
							<xsl:call-template name="tpl_table">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'link'">
							<xsl:call-template name="tpl_link">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</xsl:otherwise>	
		</xsl:choose>
	</xsl:template>
	<!-- Отображение целых числовых значений -->
	<xsl:template name="tpl_int">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<xsl:choose>
				<!-- если не создание нового документа, то записываем значения по умолчанию -->
				<xsl:when test="$isnew = 1">
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$type}"/>
				</xsl:when>
				<xsl:when test="$isnew = 2">
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$var}"/>
				</xsl:when>
				<xsl:otherwise>
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$field/.}"/>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	<!-- Отображение дробных числовых значений -->
	<xsl:template name="tpl_float">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<xsl:choose>
				<!-- если не создание нового документа, то записываем значения по умолчанию -->
				<xsl:when test="$isnew = 1">
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$type}"/>
				</xsl:when>
				<xsl:when test="$isnew = 2">
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$var}"/>
				</xsl:when>
				<xsl:otherwise>
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$field/.}"/>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	<!-- Отображение текста (тип "text") -->
	<xsl:template name="tpl_text">
		<xsl:param name="type" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:variable name="withoutSpaces">
				<xsl:if test="$type/@noSpacesLimit = 1">
					<xsl:value-of select="' знаков без пробелов'"/>
				</xsl:if>
			</xsl:variable>
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@length and not(ancestor::*[2]/disableStringConstr = '1')">
				(максимальная длина: <xsl:value-of select="$type/@length"/>
				<xsl:value-of select="$withoutSpaces"/>)
			</xsl:if>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<br/>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<textarea cols="80" rows="18" id="{$type/@name}" name="{$type/@name}">
							<xsl:attribute name="rows">
								<xsl:choose>
									<xsl:when test="$type/@mode = 'simple'">6</xsl:when>
									<xsl:when test="$type/@mode = 'nl2br'">8</xsl:when>
									<xsl:otherwise>18</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
							<xsl:choose>
								<!-- если не создание нового документа, то записываем значения по умолчанию -->
								<xsl:when test="$isnew = 1">
									<xsl:value-of select="$type" disable-output-escaping="no"/>
								</xsl:when>
								<xsl:when test="$isnew = 2">
									<xsl:value-of select="$var" disable-output-escaping="no"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="$field/." disable-output-escaping="no"/>
								</xsl:otherwise>
							</xsl:choose>
						</textarea>
						<br/>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>
	<!-- Отображение значений типа "select" -->
	<xsl:template name="tpl_select">
		<xsl:param name="type" />	
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<select name="{$type/@name}">
				<xsl:for-each select="$type/item">
					<option value="{@id}">
						<xsl:choose>
							<xsl:when test="@id = $field/@item_id and $isnew = 0">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:when>
							<xsl:when test="../@defaultSelected = @id and $isnew = 1">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:when>
							<xsl:when test="@id = $var and $isnew = 2">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:when>
						</xsl:choose>
						<xsl:value-of select="."/>
					</option>
				</xsl:for-each>
			</select>
			<br/>
		</div>
	</xsl:template>
	<!-- Отображение значений типа "radio" -->
	<xsl:template name="tpl_radio">
		<xsl:param name="type" />	
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<br/><br/>
			<xsl:for-each select="$type/item">
				<input type="radio" name="{$type/@name}" id="{@id}" value="{@id}">
					<xsl:choose>
						<xsl:when test="$isnew = 0 and $field/@item_id = @id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
						<xsl:when test="$isnew = 1 and $type/@defaultChecked = @id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
						<xsl:when test="$isnew = 2 and $var = @id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
					</xsl:choose>
				</input>	
				<label for="{@id}">
					<xsl:value-of select="text()" />
				</label>
				<br/>
			</xsl:for-each>
			<br/>
		</div>
	</xsl:template>
	<!-- Отображение значений типа "multibox" -->
	<xsl:template name="tpl_multibox">
		<xsl:param name="type" />	
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<br/><br/>
			<xsl:for-each select="$type/item">
				<input type="checkbox" name="{$type/@name}_{@item_id}" id="{@item_id}" value="{@item_id}">
					<xsl:choose>
						<xsl:when test="$isnew = 0 and $field/item/@item_id = @item_id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
						<xsl:when test="$isnew = 2 and $var[@name = concat($type/@name, '_', @item_id)] = @item_id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
					</xsl:choose>
				</input>
				<label for="{@id}">
					<xsl:value-of select="text()" />
				</label>
				<br/>
			</xsl:for-each>
			<br/>
		</div>
	</xsl:template>	
	<!-- Отображение значений строкового типа ("string") -->
	<xsl:template name="tpl_string">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<!-- Родитель родителя для ноды field, 1-ый родитель — нода <doctype>, 2-ой — нода <module> -->
			<xsl:if test="not(ancestor::*[2]/disableStringConstr = '1')">
			(максимальная длина: <xsl:value-of select="$type/@length"/>)
			</xsl:if>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<span class="formFieldString">
				<xsl:choose>
					<!-- если не создание нового документа, то записываем значения по умолчанию -->
					<xsl:when test="$isnew = 1">
						<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$type}"/>
					</xsl:when>
					<xsl:when test="$isnew = 2">
						<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$var}"/>
					</xsl:when>
					<xsl:otherwise>
						<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$field/.}"/>
					</xsl:otherwise>
				</xsl:choose>
			</span>
		</div>
	</xsl:template>
	<!-- Отображение значений булевского типа -->
	<xsl:template name="tpl_bool">
		<xsl:param name="type" />
		<xsl:param name="isnew" />
		<xsl:param name="field" />
		<xsl:param name="var" />
		<div class="formField">
			<xsl:choose>
				<!-- если не создание нового документа, то записываем значения по умолчанию -->
				<xsl:when test="$isnew = 1">
					<input type="checkbox" name="{$type/@name}" id="{$type/@name}" value="1" class="checkbox">
						<xsl:if test="$type = 1">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</xsl:when>
				<xsl:when test="$isnew = 2">
					<input type="checkbox" name="{$type/@name}" id="{$type/@name}" value="1" class="checkbox">
						<xsl:if test="$var = 1">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</xsl:when>
				<xsl:otherwise>
					<input type="checkbox" name="{$type/@name}" id="{$type/@name}" value="1" class="checkbox">
						<xsl:if test="$field = 1">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</xsl:otherwise>
			</xsl:choose>
			<label for="{$type/@name}">
				<xsl:value-of select="$type/@description"/>
			</label>
			<br/>
		</div>
	</xsl:template>
	<!-- Отображение списка строк ("strlist") -->
	<xsl:template name="tpl_strlist">
		<xsl:param name="type" />
		<xsl:param name="isnew" />
		<xsl:param name="field" />
		<xsl:param name="var" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<br/>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<textarea cols="80" rows="6" name="{$type/@name}">
							<xsl:choose>
								<xsl:when test="$isnew = 0">
									<xsl:for-each select="$field/line">
										<xsl:value-of select="."/>
										<xsl:text>&#13;&#10;</xsl:text>
									</xsl:for-each>
								</xsl:when>
								<xsl:when test="$isnew = 2">
									<xsl:value-of select="$var"/>
								</xsl:when>
							</xsl:choose>
						</textarea>
						<br/>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>
	<!-- Отображение поля для загрузки файла -->
	<xsl:template name="tpl_file">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$isnew = 0 and $field/@file_id != 0">
				[Скачать: <a href="{$field/@URL}">
					<xsl:value-of select="$field/@title"/>
				</a> (<xsl:value-of select="$field/@size"/>)]
			</xsl:if>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<input type="file" name="{$type/@name}"/>
			<br/>
			<xsl:if test="$type/@importance != 1 and $isnew = 0 and $field/@file_id != 0">
				<div class="divInnerCheckBox">
					<input type="checkbox" name="{$type/@name}_delete" id="{$type/@name}_delete"/>
					<label for="{$type/@name}_delete">Удалить файл?</label>
					<br/>
				</div>
			</xsl:if>
		</div>
	</xsl:template>
	<!-- Отображение значений типа datetime -->
	<xsl:template name="tpl_datetime">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:choose>
				<xsl:when test="$type/@show = 'selects'">
					<xsl:value-of select="$type/@description"/>
					<br/>
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/dates"/>
						<xsl:with-param name="add" select="$field/@date"/>
						<xsl:with-param name="default" select="$type/date"/>
					</xsl:call-template>&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/months"/>
						<xsl:with-param name="add" select="$field/@month"/>
						<xsl:with-param name="default" select="$type/month"/>
					</xsl:call-template>&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/years"/>
						<xsl:with-param name="add" select="$field/@year"/>
						<xsl:with-param name="default" select="$type/year"/>
					</xsl:call-template>&#160;&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/hours"/>
						<xsl:with-param name="add" select="$field/@hour"/>
						<xsl:with-param name="default" select="$type/hour"/>
					</xsl:call-template>:
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/minutes"/>
						<xsl:with-param name="add" select="$field/@minute"/>
						<xsl:with-param name="default" select="$type/minute"/>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$type/@description"/> (в формате ГГГГ-ММ-ДД ЧЧ:MM:СС)
					<xsl:call-template name="RepresentValue">
						<xsl:with-param name="type" select="$view" />
					</xsl:call-template>
					<!--xsl:if test="$type/@importance = 1"><span class="star">*</span></xsl:if><br/-->
					<xsl:choose>
						<xsl:when test="$isnew = 1">
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$type}"/>
						</xsl:when>
						<xsl:when test="$isnew = 2">
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$var}"/>
						</xsl:when>
						<xsl:otherwise>
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$field/@value}"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	<!-- Отображение значений типа date -->
	<xsl:template name="tpl_date">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:choose>
				<xsl:when test="$type/@show = 'selects'">
					<xsl:value-of select="$type/@description"/>
					<br/>
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/dates"/>
						<xsl:with-param name="add" select="$field/@date"/>
						<xsl:with-param name="default" select="$type/date"/>
					</xsl:call-template>&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/months"/>
						<xsl:with-param name="add" select="$field/@month"/>
						<xsl:with-param name="default" select="$type/month"/>
					</xsl:call-template>&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/years"/>
						<xsl:with-param name="add" select="$field/@year"/>
						<xsl:with-param name="default" select="$type/year"/>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$type/@description"/> (в формате ГГГГ-ММ-ДД)
					<xsl:call-template name="RepresentValue">
						<xsl:with-param name="type" select="$view" />
					</xsl:call-template>
					<!--xsl:if test="$type/@importance = 1"><span class="star">*</span></xsl:if><br /-->
					<xsl:choose>
						<xsl:when test="$isnew = 1">
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{substring($type, 1, 10)}"/>
						</xsl:when>
						<xsl:when test="$isnew = 2">
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$var}"/>
						</xsl:when>
						<xsl:otherwise>
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$field/@value}"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	<!-- Отображение значений типа password -->
	<xsl:template name="tpl_password">
		<xsl:param name="view" />
		<xsl:param name="type" />
		<xsl:param name="isrealnew" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:choose>
				<xsl:when test="$isrealnew = 1">
					<xsl:if test="$type/@importance = 1">
						<span class="star"> *</span>
					</xsl:if>
					<xsl:call-template name="RepresentValue">
						<xsl:with-param name="type" select="$view" />
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text> (если Вы не хотите изменять пароль, оставьте поле незаполненным)</xsl:text>
					<xsl:call-template name="RepresentValue">
						<xsl:with-param name="type" select="$view" />
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
			<input name="{$type/@name}" type="password" size="20" value=""/>
		</div>
		<div class="formField">
			<xsl:text>Confirm password</xsl:text>
			<xsl:if test="$isrealnew = 1 and $type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<input name="{$type/@name}_passconfirm" type="password" size="20" value="" />
		</div>
	</xsl:template>
	<!-- Отображение поля изображения -->
	<xsl:template name="tpl_image">
		<xsl:param name="type" />	
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$isnew = 0 and $field/@file_id != 0">
				<xsl:text>[Скачать: </xsl:text>
				<a href="{$field/@URL}">
					<xsl:value-of select="$field/@title"/>
				</a>
				<xsl:text> (</xsl:text>
				<xsl:value-of select="$field/@size"/>
				<xsl:if test="$field/@width and $isnew = 0 and $field/@file_id != 0">
					<xsl:text>, </xsl:text>
					<xsl:value-of select="$field/@width"/>
					<xsl:text>x</xsl:text>
					<xsl:value-of select="$field/@height"/>
					<xsl:text> пикселей</xsl:text>
				</xsl:if>
				<xsl:text>)]</xsl:text>
			</xsl:if>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<input type="file" name="{$type/@name}"/>
			<br/>
			<xsl:if test="$type/@importance != 1 and $isnew = 0 and $field/@file_id != 0">
				<div class="divInnerCheckBox">
					<input type="checkbox" name="{$type/@name}_delete" id="{$type/@name}_delete"/>
					<label for="{$type/@name}_delete">Удалить файл?</label>
					<br/>
				</div>
			</xsl:if>
		</div>
	</xsl:template>
	
	<xsl:template name="tpl_table">
		<xsl:param name="type" />
		<xsl:param name="field" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<br/>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<textarea id="tbl_textarea_{$type/@name}" cols="80" rows="12" name="{$type/@name}" class="mono">
							<xsl:choose>
								<xsl:when test="$isnew = 0">
									<xsl:for-each select="$field/table">
										<xsl:call-template name="ShowTable">
											<xsl:with-param name="table" select="."/>
										</xsl:call-template>
										<xsl:if test="position() != last()">
											<xsl:text>&#13;&#10;&#13;&#10;&#13;&#10;</xsl:text>
										</xsl:if>
									</xsl:for-each>
								</xsl:when>
								<xsl:when test="$isnew = 2">
									<xsl:value-of select="$var"/>
								</xsl:when>
							</xsl:choose>
						</textarea>
						<br/>
					</td>
				</tr>
				<tr>
					<td>
						<div style="display: none" id="tbl_inputs_container_{$type/@name}">
							<input type="file" name="excelfile"/>
							<xsl:text> </xsl:text>
							<input id="tbl_file_submit_{$type/@name}" type="submit" value="Загрузить таблицу из Excel-файла" onclick="return tbl_upload_excel_file('{$type/@name}');"/>
							<xsl:text> </xsl:text>
							<span style="white-space: nowrap" id="tbl_informer_{$type/@name}"> </span>
						</div>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				document.getElementById('tbl_inputs_container_<xsl:value-of select="$type/@name"/>').style.display = "block";
			</script>
		</div>
	</xsl:template>
	
	<!-- Шаблон вывода содержимого таблицы через табуляцию -->
	<xsl:template name="ShowTable">
		<xsl:param name="table" />
		<xsl:for-each select="$table/tr">
			<xsl:for-each select="td|th">
				<xsl:value-of select="." disable-output-escaping="yes"/>
				<xsl:if test="position() != last()">
					<xsl:text>&#9;</xsl:text>
				</xsl:if>
				<xsl:choose>
					<xsl:when test="@colspan != 0">
						<xsl:call-template name="RepeatTabString">
							<xsl:with-param name="cnt" select="@colspan - 1"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:for-each>
			<xsl:if test="position() != last()">
				<xsl:text>&#13;&#10;</xsl:text>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="RepeatTabString">
		<xsl:param name="cnt" select="0"/>
		<xsl:param name="isLast" select="0"/>
		<xsl:if test="$cnt != 0">
			<xsl:text>&#9;</xsl:text>
			<xsl:call-template name="RepeatTabString">
				<xsl:with-param name="cnt" select="$cnt - 1"/>
				<xsl:with-param name="isLast" select="$isLast"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<!-- Отображение значений типа "link" -->
	<xsl:template name="tpl_link">
		<xsl:param name="type" />	
		<xsl:param name="view" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<select name="{$type/@name}">
				<option value="0">
					<xsl:text>&#160;</xsl:text>
				</option>
				<xsl:for-each select="$type/document">
					<option value="{@id}">
						<xsl:if test="@id = $field/@selectedDocumentID">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="field[@name = $type/@targetDTTitle]"/>
					</option>
				</xsl:for-each>
			</select>
		</div>
	</xsl:template>
	<xsl:template name="DrawSelect">
		<xsl:param name="type"/>
		<xsl:param name="var"/>
		<xsl:param name="isnew"/>
		<xsl:param name="node"/>
		<xsl:param name="add"/>
		<xsl:param name="default"/>
		<xsl:variable name="selectName" select="concat($type/@name, '_', name($node))"/>
		<xsl:variable name="selectVar" select="$SC/module/vars/general/var[@name = $selectName]"/>
		<select name="{$selectName}">
			<xsl:for-each select="$node/item">
				<option value="{@id}">
					<xsl:choose>
						<xsl:when test="@id = $add and $isnew = 0">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:when>
						<xsl:when test="@id = $default and $isnew = 1">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:when>
						<xsl:when test="@id = $selectVar and $isnew = 2">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:when>
					</xsl:choose>
					<xsl:value-of select="."/>
				</option>
			</xsl:for-each>
		</select>
	</xsl:template>
	
	<xsl:template name="RepresentValue">
		<xsl:param name="type" select="'vertical'" />
		<xsl:choose>
			<xsl:when test="$type = 'vertical'">
				<br/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>&#160;&#160;</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<!-- Отображение таблицы ("table") -->
	<!--xsl:template name="tpl_table">
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<br/>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<textarea id="tbl_textarea_{$type/@name}" cols="80" rows="12" name="{$type/@name}" class="mono">
							<xsl:choose>
								<xsl:when test="$isnew = 0">
									<xsl:value-of select="$field"/>
								</xsl:when>
								<xsl:when test="$isnew = 2">
									<xsl:value-of select="$var"/>
								</xsl:when>
							</xsl:choose>
						</textarea>
						<br/>
					</td>
				</tr>
				<tr>
					<td>
						<div style="display: none" id="tbl_inputs_container_{$type/@name}">
							<input type="file" name="excelfile" />
							<xsl:text> </xsl:text>
							<input id="tbl_file_submit_{$type/@name}" type="submit" value="Загрузить таблицу из Excel-файла" onclick="return tbl_upload_excel_file('{$type/@name}');" />
							<xsl:text> </xsl:text>
							<span style="white-space: nowrap" id="tbl_informer_{$type/@name}"> </span>
						</div>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				document.getElementById('tbl_inputs_container_<xsl:value-of select="$type/@name" />').style.display = "block";
			</script>
		</div>
	</xsl:template-->


	

	<xsl:template name="htmlHead">
		<xsl:param name="WithJS" select="true()"/>
		<title>
			<xsl:choose>
				<xsl:when test="$SCT/meta[@name = 'title']">
					<xsl:value-of select="$SCT/meta[@name = 'title']"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$SCT/@title"/>
					<xsl:text> &#8212; </xsl:text>
					<xsl:value-of select="$projectTitle"/>
				</xsl:otherwise>
			</xsl:choose>
		</title>
		<xsl:if test="$SCT/ancestor-or-self::section[meta[@name = 'keywords']]">
			<meta name="keywords" content="{$SCT/ancestor-or-self::section[meta[@name = 'keywords']][1]/meta[@name = 'keywords']}"/>
		</xsl:if>
		<xsl:if test="$SCT/ancestor-or-self::section[meta[@name = 'description']]">
			<meta name="description" content="{$SCT/ancestor-or-self::section[meta[@name = 'description']][1]/meta[@name = 'description']}"/>
		</xsl:if>
		<base href="http://{$Query/@host}{$prefix}"/>
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<link rel="stylesheet" href="{$prefix}css.css" type="text/css"/>
		<xsl:if test="$WithJS">
			<script type="text/javascript">
				function openEditWindow(url, dt, id) {
					$pos = (window.opera) ? ", left=100, top=100" : "";
					nw = window.open(url, "<xsl:value-of select="$projectName"/>_" + dt + id, "status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, width=800, height=600" + $pos);
					nw.focus();
					return false;
				}
				function showImage(url, w, h) {
					href = "<xsl:value-of select="$prefix"/>show/?url=" + url;
	    			w += 20;
	    			h += 10;
	    			sh = screen.height - 80;
	    			if (document.all) sh -= 40;
	    			sw = screen.width;
	    			if (h &gt; sh) h = sh;
	    			if (w &gt; sw) w = sw;
	    			posX = sw/2 - w/2;
	    			posY = sh/2 - h/2;
	    			if (posY &lt; 0) posY = 0;
	    			if (posX &lt; 0) posX = 0;
	    			posCode = (document.all) ? ",left=" + posX + ",top=" + posY : ",screenX=" + posX + ",screenY=" + posY;
	    			//alert("w=" + w + "\nh=" + h + "\nposX=" + posX + "\nposY=" + posY);
	    			moreWin = window.open (href, "<xsl:value-of select="$projectName"/>", "status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, width=" + w + ", height=" + h + posCode);
	    			moreWin.focus();
	  			}
	  			
				function popupWindow(url, w, h) {
	            	href = url;
	            	sh = screen.height - 80;
	            	if (document.all) sh -= 40;
	            	sw = screen.width;
	            	if (h &gt; sh) h = sh;
	            	if (w &gt; sw) w = sw;
	            	posX = sw/2 - w/2;
	            	posY = sh/2 - h/2;
	            	if (posY &lt; 0) posY = 0;
	            	if (posX &lt; 0) posX = 0;
	            	posCode = (document.all) ? ",left=" + posX + ",top=" + posY : ",screenX=" + posX + ",screenY=" + posY;
					moreWin = window.open (href, "idm_popup", "status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, width=" + w + ", height=" + h + posCode);
	            	moreWin.focus();
	          	}  			
	  			
				<xsl:if test="//refEdit">
					var elements = [];
					function changeElement(element) {
						if (element.checked == true) {
							addElement(element.name);
						}
						else {
							deleteElement(element.name);
						}
					}
					function addElement(name) {
						elements[elements.length] = name;
					}
					function deleteElement(name) {
						var check = false;
						var elLength = elements.length;
		
						//search for element
						for (i=0; i &lt; elLength; i++ ) {
							if (elements[i] == name) {
								check = true;
							}
							if (check &amp; (i &lt; (elLength-1))) {
								elements[i] = elements[i+1];
							}
						}
						elements.length = elLength - 1;
					}
					function moveElements(destination, formnumber) {
						var form = document.getElementById('moveDocForm' + formnumber);
						
						var reg = new RegExp('el_ref_([0-9]+)_([0-9]+)');
						var newelem = [];
						var newel, i, j = 0;
						for (i=0; i &lt; elements.length; i++) {
							newel = reg.exec(elements[i]);
							if (newel[1] == formnumber) {
								newelem[j] = newel[2];
								j++;
							}
						}
						
						if (elements.length != 0 &amp; destination.value != 'no') {
							form.elements.elemToMove.value = newelem;	
							form.submit();
						} else {
							alert('Вы не выбрали ни одного документа для переноса. Проставьте галочки напротив тех документов, которые хотите перенести');
							form.elements.newRef.selectedIndex = 0;
						}
					} 
				</xsl:if>
			</script>
		</xsl:if>
	</xsl:template>


	

	<!-- Дата в формате 07.11.2009 -->
	<xsl:template name="GetDate">
		<xsl:param name="date" select="/root/@date"/>
		<xsl:value-of select="substring($date, 9, 2)"/>.<xsl:value-of select="substring($date, 6, 2)"/>.<xsl:value-of select="substring($date, 1, 4)"/>
	</xsl:template>
	<!-- Дата и время в формате 07.11.2009 23:22 -->
	<xsl:template name="DateTimeFromDateTime">
		<xsl:param name="datetime" select="concat(/root/@date, ' ', /root/@time)"/>
		<xsl:value-of select="substring($datetime, 9, 2)"/>.<xsl:value-of select="substring($datetime, 6, 2)"/>.<xsl:value-of select="substring($datetime, 1, 4)"/>
		<xsl:text> </xsl:text>
		<xsl:value-of select="substring($datetime, 12, 2)"/>:<xsl:value-of select="substring($datetime, 15, 2)"/>
	</xsl:template>
	<!-- Дата и время в формате 07.11.2009 23:22 (из TIMESTAMP, после перехода на MySQL 4.1 не используется, 
	так как TIMESTAMP выдаётся из базы в том же формате, что и DATETIME -->
	<xsl:template name="DateTimeFromTimeStamp">
		<xsl:param name="timestamp"/>
		<xsl:value-of select="substring($timestamp, 7, 2)"/>.<xsl:value-of select="substring($timestamp, 5, 2)"/>.<xsl:value-of select="substring($timestamp, 1, 4)"/>
		<xsl:text> </xsl:text>
		<xsl:value-of select="substring($timestamp, 9, 2)"/>:<xsl:value-of select="substring($timestamp, 11, 2)"/>
	</xsl:template>
	<!-- Время в формате 23:22 -->
	<xsl:template name="TimeFromDateTime">
		<xsl:param name="datetime" select="concat(/root/@date, ' ', /root/@time)"/>
		<xsl:value-of select="substring($datetime, 12, 2)"/>:<xsl:value-of select="substring($datetime, 15, 2)"/>
	</xsl:template>
	<!-- Дата и время в формате 07.11.2009 23:22, вместо чисел для соотв. дат вставляется "Сегодня в " и "Вчера в " -->
	<xsl:template name="AdvancedForumDate">
		<xsl:param name="datetime" select="concat(/root/@date, ' ', /root/@time)"/>
		<xsl:choose>
			<xsl:when test="/root/@date = substring($datetime, 1, 10)">
				<xsl:text>Сегодня в </xsl:text>
				<xsl:call-template name="TimeFromDateTime">
					<xsl:with-param name="datetime" select="$datetime"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="../yesterday/text() = substring($datetime, 1, 10)">
						<xsl:text>Вчера в </xsl:text>
						<xsl:call-template name="TimeFromDateTime">
							<xsl:with-param name="datetime" select="$datetime"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="DateTimeFromDateTime">
							<xsl:with-param name="datetime" select="$datetime"/>
						</xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!-- Отладка - информация из нод info и error -->
	<xsl:template name="debugVars">
		<xsl:param name="path" select="."/>
		<xsl:if test="$path/info">
			<h5>[Info]</h5>
			<xsl:for-each select="$path/info/item">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="$path/vars//var">
			<h5>[Vars]</h5>
			<xsl:if test="$path/vars/own/var">
				<h6>- Own</h6>
				<xsl:for-each select="$path/vars/own/var">
					<strong>
						<xsl:value-of select="@name"/>
					</strong>: <xsl:value-of select="."/>
					<br/>
				</xsl:for-each>
			</xsl:if>
			<xsl:if test="$path/vars/general/var">
				<h6>- General</h6>
				<xsl:for-each select="$path/vars/general/var">
					<strong>
						<xsl:value-of select="@name"/>
					</strong>: <xsl:value-of select="."/>
					<br/>
				</xsl:for-each>
			</xsl:if>
			<xsl:if test="$path/vars/user/var">
				<h6>- User</h6>
				<xsl:for-each select="$path/vars/user/var">
					<strong>
						<xsl:value-of select="@name"/>
					</strong>: <xsl:value-of select="."/>
					<br/>
				</xsl:for-each>
			</xsl:if>
		</xsl:if>
		<xsl:if test="$path/error/item">
			<h5>[Errors]</h5>
			<xsl:for-each select="$path/error/item">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	<!-- Отладка - информация из нод rInfo и rError -->
	<xsl:template name="debugrVars">
		<xsl:param name="path" select="."/>
		<xsl:if test="$path/rInfo">
			<h5>[Info]</h5>
			<xsl:for-each select="$path/rInfo/item">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="$path/rVars/var">
			<h5>[Vars]</h5>
			<xsl:for-each select="$path/rVars/var">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
		<xsl:if test="$path/rError/item">
			<h5>[Errors]</h5>
			<xsl:for-each select="$path/rError/item">
				<strong>
					<xsl:value-of select="@name"/>
				</strong>: <xsl:value-of select="."/>
				<br/>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	<xsl:template name="ReplaceInQuery">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:param name="paramValue"/>
		<xsl:choose>
			<xsl:when test="$Query/@staticURL = 1">
				<xsl:call-template name="ReplaceInStaticQuery">
					<xsl:with-param name="paramPrefix" select="$paramPrefix"/>
					<xsl:with-param name="paramName" select="$paramName"/>
					<xsl:with-param name="paramValue" select="$paramValue"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="ReplaceInDynamicQuery">
					<xsl:with-param name="paramPrefix" select="$paramPrefix"/>
					<xsl:with-param name="paramName" select="$paramName"/>
					<xsl:with-param name="paramValue" select="$paramValue"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!-- Замена значения параметра paramName в строке запроса на новое paramValue -->
	<xsl:template name="ReplaceInDynamicQuery">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:param name="paramValue"/>
		<xsl:variable name="tmp" select="concat($paramPrefix, $paramName)"/>
		<xsl:value-of select="$Query/@url"/>
		<xsl:text>?</xsl:text>
		<xsl:for-each select="$Query/param">
			<xsl:if test="position() != 1">
				<xsl:text>&amp;</xsl:text>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="@name = $tmp">
					<xsl:if test="$paramValue != ''">
						<xsl:value-of select="$tmp"/>=<xsl:value-of select="$paramValue"/>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@name"/>=<xsl:value-of select="@escaped"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
		<xsl:if test="not($Query/param[@name = $tmp]) and $paramValue != ''">
			<xsl:if test="$Query/param">
				<xsl:text>&amp;</xsl:text>
			</xsl:if>
			<xsl:value-of select="$tmp"/>=<xsl:value-of select="$paramValue"/>
		</xsl:if>
	</xsl:template>
	<!-- Замена значения параметра paramName в строке запроса на новое paramValue для статичных урлов -->
	<xsl:template name="ReplaceInStaticQuery">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:param name="paramValue"/>
		<xsl:variable name="tmp" select="concat($paramPrefix, $paramName)"/>
		<xsl:value-of select="$Query/@url"/>
		<xsl:for-each select="$Query/param">
			<xsl:choose>
				<xsl:when test="@name = $tmp">
					<xsl:if test="$paramValue != ''">
						<xsl:value-of select="$tmp"/>
						<xsl:text>/</xsl:text>
						<xsl:value-of select="$paramValue"/>
						<xsl:text>/</xsl:text>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@name"/>
					<xsl:text>/</xsl:text>
					<xsl:value-of select="@escaped"/>
					<xsl:text>/</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
		<xsl:if test="not ($Query/param[@name = $tmp]) and $paramValue != ''">
			<xsl:value-of select="$tmp"/>
			<xsl:text>/</xsl:text>
			<xsl:value-of select="$paramValue"/>
			<xsl:text>/</xsl:text>
		</xsl:if>
	</xsl:template>
	<!-- Удаление параметра paramName в строке запроса -->
	<xsl:template name="DeleteInQuery">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:choose>
			<xsl:when test="$Query/@staticURL = '1'">
				<xsl:call-template name="DeleteInQueryStatic">
					<xsl:with-param name="paramPrefix" select="$paramPrefix"/>
					<xsl:with-param name="paramName" select="$paramName"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="DeleteInQueryDynamic">
					<xsl:with-param name="paramPrefix" select="$paramPrefix"/>
					<xsl:with-param name="paramName" select="$paramName"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="DeleteInQueryDynamic">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:variable name="tmp" select="concat($paramPrefix, $paramName)"/>
		<xsl:value-of select="$Query/@url"/>
		<xsl:if test="$Query/param[1]/@name != $tmp or $Query/param[2]">
			<xsl:text>?</xsl:text>
		</xsl:if>
		<xsl:for-each select="$Query/param[@name != $tmp]">
			<xsl:if test="position() != 1">
				<xsl:text>&amp;</xsl:text>
			</xsl:if>
			<xsl:value-of select="@name"/>=<xsl:value-of select="@escaped"/>
		</xsl:for-each>
	</xsl:template>
	<xsl:template name="DeleteInQueryStatic">
		<xsl:param name="paramPrefix" select="''"/>
		<xsl:param name="paramName"/>
		<xsl:variable name="tmp" select="concat($paramPrefix, $paramName)"/>
		<xsl:value-of select="$Query/@url"/>
		<xsl:for-each select="$Query/param[@name != $tmp]">
			<xsl:value-of select="@name"/>
			<xsl:text>/</xsl:text>
			<xsl:value-of select="@escaped"/>
			<xsl:text>/</xsl:text>
		</xsl:for-each>
	</xsl:template>


	

	<xsl:template name="edit">
		<xsl:if test="$Visitor/@id">
			<div class="divEdit">
				<xsl:if test="$Visitor/role/@name = 'admin' or $Visitor/role/@name = 'superadmin'">
					<xsl:variable name="admin">
						<xsl:choose>
							<xsl:when test="$ST//section[@name = 'secadmin']">secadmin</xsl:when>
							<xsl:otherwise>admin</xsl:otherwise>
						</xsl:choose>
					</xsl:variable>
					<a href="{$prefix}{$admin}/?id={$SCT/@id}">Редактировать секцию</a>
					<xsl:text> | </xsl:text>
				</xsl:if>
				<xsl:text>Логин: </xsl:text>
				<xsl:value-of select="$Visitor/@login"/>
				<xsl:text> (</xsl:text>
				<a href="{$prefix}?writemodule=Authorize&amp;logoff=1">Выход</a>
				<xsl:text>)</xsl:text>
			</div>
		</xsl:if>
	</xsl:template>

 
	

	<xsl:template name="moduleRightsEdit">
		<xsl:if test="$Visitor/role/@name = 'superadmin' and $SC/module[@name = 'moduleRightsEdit']">
			<script type="text/javascript" language="JavaScript">
					function changeVisibility(alink) {
						var moduleRightsContainer = document.getElementById('moduleRightsContainer') || false;
						if (!moduleRightsContainer) return;
						if (!moduleRightsContainer.style.display) moduleRightsContainer.style.display = "none";
						moduleRightsContainer.style.display = ("none" == moduleRightsContainer.style.display) ? "block" : "none";
						
						alink.innerHTML = ("none" == moduleRightsContainer.style.display) 
						? "Редактирование прав на модули (развернуть)"
						: "Редактирование прав на модули (свернуть)";
					}
				</script>
			<div class="divModuleRights" id="divModuleRights">
				<a onclick="changeVisibility(this)">
					<xsl:text>Редактирование прав на модули (развернуть)</xsl:text>
				</a>
				<div id="moduleRightsContainer" class="moduleRightsContainer">
					<xsl:call-template name="drawModuleRightsForm"/>
				</div>
			</div>
			<script type="text/javascript" language="JavaScript">
			var moduleRightsContainer = document.getElementById('moduleRightsContainer').style.display = "none";
			</script>
		</xsl:if>
	</xsl:template>
	<xsl:template name="drawModuleRightsForm">
		<form action="{$prefix}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="writemodule" value="ModuleRights"/>
			<input type="hidden" name="ref" value="{$SC/module[@name = 'moduleRightsEdit']/@id}"/>
			<input type="hidden" name="qref" value="{$SC/module[@name = 'moduleRightsEdit']/@id}"/>
			<input type="hidden" name="section_id" value="{$SCT/@id}"/>
			<input type="hidden" name="retpath" value="{$Query/@query}"/>
			<input type="hidden" name="errpath" value="{$Query/@query}"/>
			<xsl:for-each select="$SC/module[@name = 'moduleRightsEdit']/module">
				<div class="module">
					<strong>
						<xsl:text>Модуль </xsl:text>
						<xsl:value-of select="@id"/>
						<xsl:if test="@docTypeName">
							<xsl:text> (тип документа "</xsl:text>
							<xsl:value-of select="@docTypeName"/>
							<xsl:text>")</xsl:text>
						</xsl:if>
					</strong>
					<xsl:for-each select="right">
						<div class="role">
							<xsl:value-of select="@roleTitle"/>
						</div>
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'read'"/>
									<xsl:with-param name="value" select="@read"/>
								</xsl:call-template>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'create'"/>
									<xsl:with-param name="value" select="@create"/>
								</xsl:call-template>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'createEnabled'"/>
									<xsl:with-param name="value" select="@createEnabled"/>
								</xsl:call-template>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'edit'"/>
									<xsl:with-param name="value" select="@edit"/>
								</xsl:call-template>
								<xsl:call-template name="drawModuleRightCheckbox">
									<xsl:with-param name="moduleId" select="../@id"/>
									<xsl:with-param name="rolePrefix" select="@roleId"/>
									<xsl:with-param name="right" select="'delete'"/>
									<xsl:with-param name="value" select="@delete"/>
								</xsl:call-template>
							</tr>
						</table>
					</xsl:for-each>
				</div>
			</xsl:for-each>
			<div style="text-align: center;">
				<input type="submit" value="Изменить"/>
			</div>
		</form>
	</xsl:template>
	<xsl:template name="drawModuleRightCheckbox">
		<xsl:param name="moduleId"/>
		<xsl:param name="rolePrefix"/>
		<xsl:param name="right"/>
		<xsl:param name="value"/>
		<td style="padding:0 10px 0 0px; white-space:nowrap;">
			<input type="checkbox" id="{$moduleId}_{$rolePrefix}_{$right}" name="{$moduleId}_{$rolePrefix}_{$right}" style="vertical-align:middle;">
				<xsl:if test="$value = 1">
					<xsl:attribute name="checked"><xsl:text>checked</xsl:text></xsl:attribute>
				</xsl:if>
			</input>
			<label for="{$moduleId}_{$rolePrefix}_{$right}">
				<xsl:value-of select="$right"/>
			</label>
		</td>
	</xsl:template>

 
	

	<xsl:template name="authorizationBox">
		<xsl:choose>
			<xsl:when test="$Visitor/@id">
				<div style="float: right;">
					<xsl:text>Hello, </xsl:text>
					<xsl:value-of select="$Visitor/@login"/>
					<xsl:text> (</xsl:text>
					<a href="{$prefix}profile/">Your profile</a>
					<xsl:text> | </xsl:text>
					<a href="{$prefix}?writemodule=Authorize&amp;logoff=1">Sign Out</a>
					<xsl:text>)</xsl:text>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<script type="text/javascript">
					document.getElementById("selectfirst").select();
					document.getElementById("selectfirst").focus();
				</script>
				<form method="post" action="{$prefix}" style="margin-bottom: 5px;">
					<input type="hidden" name="writemodule" value="Authorize"/>
					<input type="hidden" name="ref" value="29"/>
					<input type="hidden" name="retpath" value="{$Query/@retpathPost}"/>
					<input type="hidden" name="errpath" value="{$ST//section[@name = 'login']/@URL}"/>
					<table cellpadding="0" cellspacing="5" border="0">
						<tr>
							<td>
								<xsl:text>Login</xsl:text>
								<br/>
								<input id="selectfirst" type="text" name="login" size="15" maxlenght="50" value=""/>
							</td>
							<td>
								<xsl:text>Password</xsl:text>
								<br/>
								<input type="password" name="pass" size="15" maxlenght="50" value=""/>
							</td>
							<td valign="bottom" style="padding-bottom: 1px;">
								<input type="submit" value="Sign In"/>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:text>Sign Up</xsl:text>
							</td>
							<td valign="bottom" style="padding-bottom: 1px;">
								<a href="{$ST//section[@name = 'official-reg']/@URL}">Officail representatives</a>
							</td>
							<td valign="bottom" style="padding-bottom: 1px;">
								<a href="{$ST//section[@name = 'team-member-reg']/@URL}">Team members</a>
							</td>
						</tr>
					</table>
				</form>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	

	<xsl:template name="DisplayHeader">
		<div class="header">
			<div class="header_resize">
				<div class="authFrom"> 
					<xsl:call-template name="authorizationBox"/>
				</div>
				<div class="logo">
					<h1>
						<xsl:choose>
							<xsl:when test="$isMain">
								<xsl:value-of select="$SC/module[@name = 'sloganOnTop']/document/field[@name = 'text']" disable-output-escaping="yes" />
							</xsl:when>
							<xsl:otherwise>
								<a href="{$prefix}">
									<xsl:value-of select="$SC/module[@name = 'sloganOnTop']/document/field[@name = 'text']" disable-output-escaping="yes" />
								</a>
							</xsl:otherwise>
						</xsl:choose>
					</h1>
				</div>
				<div class="clr"></div>
			    <div class="htext">
			    	<h2>Read me first...</h2>
			      	<xsl:value-of select="$SC/module[@name = 'headerText']/document/field[@name = 'text']" disable-output-escaping="yes" />
			    </div>
				<div class="clr"></div>
			    <!--div class="menu_nav"-->
			    <div>
			    	<xsl:call-template name="ShowMenu" />
				</div>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template name="ShowMenu">
		<ul id="main-menu">
			<xsl:for-each select="$ST//section[meta[@name = 'menu_header'] = 1]">
				<li>
					<xsl:if test="$isMain">	
						<xsl:attribute name="style">
							<xsl:text>font-size:14px;</xsl:text>	
						</xsl:attribute>
					</xsl:if>
					<a href="{@URL}" alt="{@title}">
						<xsl:value-of select="@title" />
					</a>
					<xsl:if test="count(./section[@content = 1])">
						<div class="dropdown">
				       		<div class="arrow"></div>
				       		<ul>
								<xsl:for-each select="section">
									<li>
										<a href="{@URL}" alt="{@title}">
											<xsl:value-of select="@title" />
										</a>
									</li>
								</xsl:for-each>
							</ul>
			 			</div>	
					</xsl:if>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>
	
	<xsl:template name="HeaderMenu">
		<ul>
   			<li>
   				<xsl:if test="not($isMain)">
					<xsl:attribute name="class">active</xsl:attribute>
				</xsl:if>
   				<a href="{$prefix}">Home</a>
   			</li>
   			<xsl:for-each select="$ST//section[meta[@name = 'menu_header'] = 1]">
   				<xsl:variable name="isActive" select="$SR/@id = @id"/>
   				<li>
   					<xsl:if test="not($isActive)">
   						<xsl:attribute name="class">active</xsl:attribute>
   					</xsl:if>
   					<a href="{@URL}" alt="{@title}">
						<xsl:value-of select="@title" />
					</a>
   				</li>
   			</xsl:for-each>
   		</ul>
	</xsl:template>

	

	<xsl:template name="DisplayContent">
		<div class="content">
    		<div class="content_resize">
      			<div class="mainbar">
        			<div class="article">
						<xsl:apply-templates />
        			</div>
        			<div class="article">
          				<xsl:text></xsl:text>
        			</div>
      			</div>
      			<div class="sidebar">
	        		<div class="gadget">
	          			<h2 class="star"><span>Main</span> Sections</h2>
	          			<xsl:call-template name="GenMenu">
							<xsl:with-param name="root" select="$home" />
						</xsl:call-template>
	        		</div>
	        		<div class="gadget">
	        			<xsl:choose>
	        				<xsl:when test="$SR[@name = 'events']">
	        					<xsl:call-template name="NewsBlock">
									<xsl:with-param name="path" select="$SC/module[@name = 'newsBlock']" />
								</xsl:call-template>
	        				</xsl:when>
	        				<xsl:when test="$SR[@name = 'news']">
	        					<xsl:call-template name="EventBlock">
									<xsl:with-param name="path" select="$SC/module[@name = 'eventBlock']" />
								</xsl:call-template>
	        				</xsl:when>
	        				<xsl:otherwise>
	        					<xsl:call-template name="EventBlock">
									<xsl:with-param name="path" select="$SC/module[@name = 'eventBlock']" />
								</xsl:call-template>
	        				</xsl:otherwise>
	        			</xsl:choose>
	          		</div>
	      		</div>
	      		<div class="clr"></div>
    		</div>
		</div>
	
	  	<div class="fbg">
	    	<div class="fbg_resize">
	      		<div class="col c1">
	        		<h2><span>Event Gallery</span></h2>
	        		<a href="#"><img src="{$prefix}img/pix1.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix2.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix3.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix4.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix5.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix6.jpg" width="56" height="56" alt="Event Photo" /></a>
	      		</div>
	      		<div class="col c2">
	        		<xsl:call-template name="TeamBlock">
						<xsl:with-param name="path" select="$SC/module[@name = 'teamBlock']" />
					</xsl:call-template>
	      		</div>
	      		<div class="col c3">
	      			<xsl:value-of select="$SC/module[@name = 'about']/document/field[@name = 'text']" disable-output-escaping="yes" />
	      			<a href="{$ST//section[@name = 'about']/@URL}">Learn more...</a>  		
	      		</div>
	      		<div class="clr"></div>
	    	</div>
	  	</div>
	</xsl:template>
		
	

	<xsl:template name="DisplayFooter">
		<div class="footer">
			<div class="footer_resize">
		   		<p class="lf">&#169; 2014</p>
		   		<xsl:call-template name="FooterMenu" />
		   		<div class="clr"></div>
			</div>
		</div>	
	</xsl:template>
	
	<xsl:template name="FooterMenu">
		<ul class="fmenu">
   			<li>
   				<xsl:choose>
   					<xsl:when test="$isMain">
   						<xsl:text>Home</xsl:text>
   					</xsl:when>
   					<xsl:otherwise>
   						<xsl:attribute name="class">active</xsl:attribute>
   						<a href="{$prefix}">Home</a>
   					</xsl:otherwise>
   				</xsl:choose>
   			</li>
   			<xsl:for-each select="$ST//section[meta[@name = 'menu_footer'] = 1]">
   				<xsl:variable name="isActive" select="$SR/@id = @id"/>
   				<li>
   					<xsl:choose>
	   					<xsl:when test="$isActive">
	   						<xsl:value-of select="@title" />
	   					</xsl:when>
	   					<xsl:otherwise>
	   						<xsl:attribute name="class">active</xsl:attribute>
	   						<a href="{@URL}">
	   							<xsl:value-of select="@title" />
	   						</a>
	   					</xsl:otherwise>
	   				</xsl:choose>
   				</li>
   			</xsl:for-each>
   		</ul>
	</xsl:template>
		
	

	<!-- Шаблон генерации блока новостей на главной странице -->
	<xsl:template name="NewsBlock">
		<xsl:param name="path" select="$SC/module" />
		<h2 class="star"><span>News</span></h2>
		<ul class="ex_menu">
			<xsl:for-each select="$path/document">
				<li>
					<xsl:value-of select="field[@name = 'pubdate']" />
					<br/>
					<a href="{@URL}" title="{field[@name = 'title']}">
						<xsl:value-of select="field[@name = 'title']" />
					</a>
				</li>
			</xsl:for-each>
 		</ul>
	</xsl:template>


	

	<!-- Шаблон генерации блока новостей на главной странице -->
	<xsl:template name="TeamBlock">
		<xsl:param name="path" select="$SC/module" />
		<xsl:variable name="current" select="$path/document" />
		<h2>
			<span>
				<xsl:value-of select="$current/field[@name = 'title']" /> 
			</span>
		</h2>
		<xsl:value-of select="$current/field[@name = 'description']" />
		<br/>
		<a href="{$current/@URL}">Read more...</a>
	</xsl:template>


	

	<!-- Шаблон генерации блока новостей на главной странице -->
	<xsl:template name="EventBlock">
		<xsl:param name="path" select="$SC/module" />
		<h2 class="star"><span>Events</span></h2>
		<ul class="ex_menu">
			<xsl:for-each select="$path/document">
				<li>
					<xsl:value-of select="field[@name = 'pubdate']" />
					<br/>
					<a href="{@URL}" title="{field[@name = 'title']}">
						<xsl:value-of select="field[@name = 'title']" />
					</a>
					<div class="eventBlock">
						<xsl:value-of select="field[@name = 'preview']" />
					</div>
				</li>
			</xsl:for-each>
 		</ul>
	</xsl:template>


	

	<xsl:template name="GenMenu">
		<xsl:param name="root"/>
		<ul class="sb_menu">
			<xsl:for-each select="$root/section[@hidden = 0]">
				<xsl:variable name="isActive" select="$SR/@id = @id"/>
				<li>
					<xsl:if test="$isActive">
                        <xsl:attribute name="class">open</xsl:attribute>
                    </xsl:if>
                    <a>
                        <xsl:attribute name="href">
                            <xsl:choose>
                                <xsl:when test="@content = 0 and ./section[@content = 1]">
                                    <xsl:value-of select="./section[@content = 1]/@URL"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="@URL"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>
                        <xsl:value-of select="@title"/>
                    </a>
                    <xsl:if test="$isActive">
                        <xsl:call-template name="GenSubMenu" />
                    </xsl:if>    
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>
	
	<!-- Генерация 2-го уровня меню -->
	<xsl:template name="GenSubMenu">
		<xsl:if test="section[@hidden = 0]">
			<xsl:for-each select="section[@hidden = 0]">
				<div class="sb_menu_second">
					<xsl:variable name="isActiveSubMenu" select="$SR/@id = @id"/>
					<a>
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="@content = 0 and ./section[@content = 1]">
									<xsl:value-of select="./section[@content = 1]/@URL"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@URL"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:value-of select="@title"/>
					</a>
					<!--xsl:if test="$isActiveSubMenu">
						<xsl:call-template name="GenThirdLevelMenu"/>
					</xsl:if-->	
				</div>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<!-- Генерация 3-го уровня меню -->
	<xsl:template name="GenThirdLevelMenu">
		<xsl:if test="section[@hidden = 0]">
			<xsl:for-each select="section[@hidden = 0]">
				<xsl:variable name="isActiveThirdLevelMenu" select="$SR/@id = @id"/>
				<div class="submenu3">
					<a>
						<xsl:if test="$isActiveThirdLevelMenu">
							<xsl:attribute name="style">
								<xsl:text>text-decoration:underline;</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="@content = 0 and ./section[@content = 1]">
									<xsl:value-of select="./section[@content = 1]/@URL"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@URL"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:value-of select="@title"/>
					</a>
					<xsl:if test="$isActiveThirdLevelMenu">
						<xsl:call-template name="GenLevelMenu"/>
					</xsl:if>	
				</div>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<!-- Генерация последующих уровней меню -->
	<xsl:template name="GenLevelMenu">
		<xsl:if test="section[@hidden = 0]">
			<xsl:for-each select="section[@hidden = 0]">
				<xsl:variable name="isActiveLevelMenu" select="$SR/@id = @id"/>
				<div>	
					<a>
						<xsl:if test="$isActiveLevelMenu">
							<xsl:attribute name="style">text-decoration:underline;</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="@content = 0 and ./section[@content = 1]">
									<xsl:value-of select="./section[@content = 1]/@URL"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@URL"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:value-of select="@title"/>
					</a>
					<xsl:if test="$isActiveLevelMenu">
						<xsl:call-template name="GenLevelMenu"/>
					</xsl:if>
				</div>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>


	<xsl:template match="text()"/>
	
	<!-- Основной шаблон -->
	<xsl:template match="/root/SectionCurrent">
		<html>
			<head>
				<xsl:call-template name="htmlHead" />
				<!-- CuFon: Enables smooth pretty custom font rendering. To disable, remove this section -->
				<script type="text/javascript" src="{prefix}js/cufon-yui.js"></script>
				<script type="text/javascript" src="{prefix}js/georgia.js"></script>
				<script type="text/javascript" src="{prefix}js/cuf_run.js"></script>
				<!-- CuFon ends -->
				
				<script type="text/javascript" src="{$Query/@jscore}jquery-1.11.1.min.js" />
				<script type="text/javascript" src="{$Query/@jscore}jquery-ui.js" />
				
				<script type="text/javascript">
				    $(document).ready(function(){
					    $('#main-menu .dropdown').each(function(){
					        var parentW = $(this).parent().outerWidth();
					        $(this).css('left', (-$(this).outerWidth() / 2) + parentW / 2);
					    });
					});	    
				</script>		    
			</head>
			<body>
				<xsl:choose>
					<!-- Стандартное отображение данных -->
					<xsl:when test="not($Query/param[@name = 'print' or @name = 'showtable'])">
						<div class="main">
							<xsl:call-template name="DisplayHeader" />
							<xsl:call-template name="DisplayContent" />
							<xsl:call-template name="DisplayFooter" />
							<xsl:call-template name="moduleRightsEdit"/>
						</div>	
					</xsl:when>
					<!-- -->
					<xsl:when test="$Query/param[@name = 'showtable']">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td width="100%" style="padding: 20px;">
									<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 5px;">
										<tr>
											<td>
												<div id="printButton">
													<a href="javascript:void(0);" onclick='printButton.style.display="none";window.print();printButton.style.display=""'>Print page</a>
												</div>
											</td>
											<td>
												<div align="right">
													<a href="javascript:void(0);" onclick="window.close()">Close window</a>
												</div>
											</td>
										</tr>
									</table>
									<xsl:apply-templates />
									<div align="right">
										<a href="javascript:void(0);" onclick="window.close()">Close window</a>
									</div>
								</td>
							</tr>
						</table>
					</xsl:when>
					<!-- -->
					<xsl:otherwise>
						<table align="center" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
							<tr>
								<td class="printTopTable">
									<div class="printurl rightalign">
										<xsl:variable name="URL">
											<xsl:call-template name="DeleteInQuery">
												<xsl:with-param name="paramName" select="'print'"/>
											</xsl:call-template>
										</xsl:variable>
										<xsl:text>Web page: </xsl:text>
										<a href="{$URL}">http://<xsl:value-of select="$Query/@host"/>
											<xsl:value-of select="$URL"/>
										</a>
									</div>
									<h1 class="printVersion">
										<xsl:choose>
											<xsl:when test="not($SCT/meta[@name = 'title'])">
												<xsl:text>Mathematics competitions :: </xsl:text>
												<xsl:value-of select="$section/@title"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="$SCT/meta[@name = 'title']"/>
											</xsl:otherwise>
										</xsl:choose>
									</h1>
									<xsl:apply-templates />
								</td>
							</tr>
						</table>
					</xsl:otherwise>
				</xsl:choose>
					
				<!--xsl:choose>
					<xsl:when test="$SC/section[@name = 'adminfaq']" />
					<xsl:otherwise>
						<div class="m">
							<xsl:call-template name="edit" />
							<xsl:call-template name="authorizationBox"/>
							<a href="{$prefix}">On main</a>
							<br />
							<xsl:for-each select="$ST/section[@name = 'main']/section">
								<a href="{@URL}">
									<xsl:value-of select="@title"/>
								</a>
								<br/>
							</xsl:for-each>
						</div>
					</xsl:otherwise>
				</xsl:choose>
				<div>
					<xsl:attribute name="class">
						<xsl:choose>
							<xsl:when test="$SC/section[@name = 'adminfaq']">faq</xsl:when>
							<xsl:otherwise>m</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
					<xsl:apply-templates/>
				</div>
				<xsl:call-template name="moduleRightsEdit"/-->
			</body>
		</html>
	</xsl:template>


