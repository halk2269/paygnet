<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<xsl:template>
		<table summary="general table" cellspacing="0" id="general">
			<tr id="upmenu">
				<td>Редактирование списков</td>
				<td class="submenu">
					<xsl:if test="$ST//section[@name = 'admin']">
						<a href="{$ST//section[@name = 'admin']/@URL}">Администрирование</a> | 
					</xsl:if>
					<xsl:if test="$ST//section[@name = 'users']">
						<a href="{$ST//section[@name = 'users']/@URL}">Пользователи</a> | 
					</xsl:if>
					<a>
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="$ST//section[@name = 'secadmin']">
									<xsl:value-of select="$ST//section[@name = 'secadmin']/@URL" />
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="$ST//section[@name = 'admin']/@URL" />
								</xsl:otherwise>
							</xsl:choose>	
						</xsl:attribute>Секции
					</a> | 
					<xsl:if test="$ST//section[@name = 'listedit']">
						<a href="{$ST//section[@name = 'listedit']/@URL}">Списки</a> | 
					</xsl:if>
					<a href="{$prefix}"><strong>Перейти на сайт</strong></a> | 
					<a href="{$prefix}?writemodule=Authorize&amp;logoff=1">Выход</a>
				</td>
			</tr>
			<tr>
				<td class="main" colspan="2">
					<table summary="menu and content" cellspacing="0" id="main">
						<tr>
							<td id="menu" class="listItems">
								<xsl:choose>
									<xsl:when test="not($Query/param[@name = 'listid']) or not(list[@id = $Query/param[@name = 'listid']])">
										<div class="inf p100"><div>Выберите, пожалуйста, в правой колонке список, который желаете отредактировать</div></div>
									</xsl:when>
									<xsl:otherwise>
										<xsl:choose>
											<xsl:when test="$Visitor/role/@listEdit != 1">
												<b>Извините, у Вас нет прав на редактиорвание списков</b>
											</xsl:when>
											<xsl:otherwise>
												<xsl:variable name="EditBoth" select="list[@id = $Query/param[@name = 'listid']]/aux[@name = 'editboth'] = 1" />
												<xsl:if test="not($EditBoth)">
													<div class="inf p100"><div>Здесь Вы можете отредактировать пункты списков, используемых на сайте. Для изменения порядка отображения (сортировки) поменяте местами пункты списка. Для удаления &#8212; очистите соответствующее поле. Одинаковые названия пунктов списка запрещены! Начальные и конечные пробелы удаляются.</div></div>
												</xsl:if>
												<h2>
													<xsl:value-of select="list[@id = $Query/param[@name = 'listid']]/aux[@name = 'title']" />
												</h2>
												<xsl:if test="info/item[@name = 'ChangesWereSaved']">
													<div class="inf p100"><div>Изменения были успешно сохранены</div></div>
												</xsl:if>
												<form action="{$prefix}" method="post">
													<input type="hidden" name="writemodule" value="Lists" />
													<input type="hidden" name="ref" value="{@id}" />
													<input type="hidden" name="list" value="{$Query/param[@name = 'listid']}" />
													<xsl:if test="$EditBoth">
														<table cellpadding="0" cellspacing="0" class="listHead">
															<tr>
																<td>
																	<b>Псевдоним</b>
																	<!--
																	<xsl:text> </xsl:text>
																	<span title="" style="cursor: help">[?]</span>
																	-->
																</td>
																<td>
																	<b>Заголовок</b>
																	<!--
																	<xsl:text> </xsl:text>
																	<span title="" style="cursor: help">[?]</span>
																	-->
																</td>
															</tr>
														</table>
													</xsl:if>
													<xsl:for-each select="listitem">
														<xsl:if test="$EditBoth">
															<input class="nameInput" type="text" name="ItemName{position()}" value="{aux[@name = 'name']}" />
														</xsl:if>
														<input class="titleInput" type="text" name="ItemTitle{position()}" value="{aux[@name = 'title']}" />
														<br />
													</xsl:for-each>
													<div class="listAddItems">
														<b>Добавить новые элементы</b>
													</div>
													<xsl:if test="$EditBoth">
														<input class="nameInput" type="text" name="ItemName{count(listitem) + 1}" value="" />
													</xsl:if>
													<input class="titleInput" type="text" name="ItemTitle{count(listitem) + 1}" value="" />
													<br />
													<xsl:if test="$EditBoth"><input class="nameInput" type="text" name="ItemName{count(listitem) + 2}" value="" /></xsl:if><input class="titleInput" type="text" name="ItemTitle{count(listitem) + 2}" value="" /><br />
													<xsl:if test="$EditBoth"><input class="nameInput" type="text" name="ItemName{count(listitem) + 3}" value="" /></xsl:if><input class="titleInput" type="text" name="ItemTitle{count(listitem) + 3}" value="" /><br />
													<xsl:if test="$EditBoth"><input class="nameInput" type="text" name="ItemName{count(listitem) + 4}" value="" /></xsl:if><input class="titleInput" type="text" name="ItemTitle{count(listitem) + 4}" value="" /><br />
													<xsl:if test="$EditBoth"><input class="nameInput" type="text" name="ItemName{count(listitem) + 5}" value="" /></xsl:if><input class="titleInput" type="text" name="ItemTitle{count(listitem) + 5}" value="" /><br />
													<div class="submitDiv">
														<input type="submit" value="Отправить" />
													</div>
												</form>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td id="rightmenu">
								<div class="listOfLists">
									<xsl:for-each select="list">
										<xsl:variable name="URL">
											<xsl:call-template name="ReplaceInQuery">
												<xsl:with-param name="paramName" select="'listid'" />
												<xsl:with-param name="paramValue" select="@id" />
											</xsl:call-template>
										</xsl:variable>
										<xsl:choose>
											<xsl:when test="$Query/param[@name = 'listid'] = @id">
												<xsl:value-of select="aux[@name = 'title']" />
											</xsl:when>
											<xsl:otherwise>
												<a href="{$URL}">
													<xsl:value-of select="aux[@name = 'title']" />
												</a>
											</xsl:otherwise>
										</xsl:choose>
										<br />
									</xsl:for-each>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr id="downmenu">
				<td colspan="2">Система управления сайтом &#169; <a href="http://www.infodesign.ru" title="www.infodesign.ru" target="_blank">Инфодизайн</a></td>
			</tr>
		</table>
	</xsl:template>
	
	<xsl:template name="ViewErrors">
		<xsl:param name="path" />
		<xsl:if test="$path">
			<div class="divError">
				<xsl:for-each select="$path/item">
					<div class="error">
						<xsl:choose>
							<xsl:when test="@name = 'BadRights'">Извините, у Вас нет прав на выполнение запрошенного действия</xsl:when>
							<xsl:when test="@name = 'BlankString'">Пустая строка не может быть передаваемым значением</xsl:when>
							<xsl:when test="@name = 'BadIDField'">Неверный идентификатор секции. Возможно, секция уже удалена</xsl:when>							
							<xsl:when test="@name = 'NeedParam'">
								<xsl:choose>
									<xsl:when test="@description = 'title'">Не задано имя секции</xsl:when>
									<xsl:when test="@description = 'newname'">Не задан псевдоним секции</xsl:when>
									<xsl:when test="@description = 'from'">Не задан идентификатор секции назначения</xsl:when>
									<xsl:when test="@description = 'createtype'">Не задан тип создаваемой секции</xsl:when>
								</xsl:choose>
							</xsl:when>
							<xsl:when test="@name = 'IDNotFound'">Секция с переданным идентификатором не найдена. Возможно, она уже была удалена</xsl:when>
							<xsl:when test="@name = 'BadNameChars'">Псевдоним секции может содержать только символы латинского алфавита, цифры, дефис и знак подчёркивания</xsl:when>
							<xsl:when test="@name = 'NameExists'">Секция с псевдонимом '<strong><xsl:value-of select="." /></strong>' уже существует</xsl:when>
							<xsl:when test="@name = 'CantMoveTopmost'">Нельзя переместить секцию '<strong><xsl:value-of select="." /></strong>' вверх</xsl:when>
							<xsl:when test="@name = 'CantMoveBottommost'">Нельзя переместить секцию '<strong><xsl:value-of select="." /></strong>' вниз</xsl:when>
							<xsl:when test="@name = 'BadFROMField'">Неверный идентификатор перемещаемой секции. Возможно, переносимая секция уже была удалена</xsl:when>
							<xsl:when test="@name = 'BadMoveNode'">Секция не может быть перемещена сама в себя или в свою дочернюю ноду</xsl:when>
							<xsl:when test="@name = 'BadRedirectChars'">URL переадресации может содержать только символы латинского алфавита, цифры, дефис, знак подчёркивания, ?, &amp;, . и т.д.<br />Примеры URL:<br />http://www.yandex.ru/<br/>ftp://sitename.org<br/>https://web13.local.sitename.net/script-name.php?p1=abc&amp;p2=def#anchor1</xsl:when>
							<xsl:when test="@name = 'BadGoToChildField'">Неверное значение параметра «Переход к первой дочерней»</xsl:when>
							<xsl:when test="@name = 'BadMetaTextField'">Неверное значение текста мета-тега</xsl:when>
							<xsl:when test="@name = 'BadCreateType'">Некорректное значение типа создаваемой секции</xsl:when>
							<xsl:otherwise>
								Неизвестный код сообщения об ошибке:
								<strong><xsl:value-of select="@name" /></strong><br />
								Тело сообщения: 
								<strong><xsl:value-of select="." /></strong>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template name="ViewInfo">
		<xsl:param name="path" />
		<xsl:if test="$path">
			<div class="divInfo">
				<xsl:for-each select="$path/item">
					<div class="info">
						<xsl:choose>
							<xsl:when test="@name = 'TitleWasChanged'">Название секции было изменено на '<strong><xsl:value-of select="." /></strong>'</xsl:when>
							<xsl:when test="@name = 'NameWasChanged'">Псевдоним секции был изменен на '<strong><xsl:value-of select="." /></strong>'</xsl:when>
							<xsl:when test="@name = 'RedirectWasChanged'">
								<xsl:choose>
									<xsl:when test=". != ''">URL переадресации секции был изменен на '<strong><xsl:value-of select="." /></strong>'</xsl:when>
									<xsl:otherwise>Переадресации с секции успешно отменена</xsl:otherwise>
								</xsl:choose>
							</xsl:when>
							<xsl:when test="@name = 'GoToChildWasChanged'">
								Переход к первой дочерней
								<xsl:choose>
									<xsl:when test=". = 1">включен.</xsl:when>
									<xsl:otherwise>выключен.</xsl:otherwise>
								</xsl:choose>
							</xsl:when>							
							<xsl:when test="@name = 'SectionWasShown'">Секция отображается на сайте</xsl:when>
							<xsl:when test="@name = 'SectionWasHidden'">Секция скрыта</xsl:when>
							<xsl:when test="@name = 'SectionWasShownOnMap'">Секция отображается на карте сайта</xsl:when>
							<xsl:when test="@name = 'SectionWasHiddenOnMap'">Секция скрыта на карте сайта</xsl:when>							
							<xsl:when test="@name = 'SectionWasEnabled'">Секция включена</xsl:when>
							<xsl:when test="@name = 'SectionWasDisabled'">Секция выключена</xsl:when>
							<xsl:when test="@name = 'SectionWasDeleted'">Секция удалена</xsl:when>
							<xsl:when test="@name = 'SectionWasCreated'">Создана секция '<strong><xsl:value-of select="." /></strong>'</xsl:when>
							<xsl:when test="@name = 'SectionMovedUp'">Секция '<strong><xsl:value-of select="." /></strong>' перемещена вверх</xsl:when>
							<xsl:when test="@name = 'SectionMovedDown'">Секция '<strong><xsl:value-of select="." /></strong>' перемещена вниз</xsl:when>
							<xsl:when test="@name = 'SectionMoved'">Секция '<strong><xsl:value-of select="." /></strong>' перемещена</xsl:when>
							<xsl:when test="@name = 'SectionMovedToTop'">Секция '<strong><xsl:value-of select="." /></strong>' перемещена в самый верх</xsl:when>
							<xsl:when test="@name = 'SectionMovedToBottom'">Секция '<strong><xsl:value-of select="." /></strong>' перемещена в самый низ</xsl:when>
							<xsl:when test="@name = 'MetaWasCreated'">Мета-тег для секции '<strong><xsl:value-of select="$ST222//section[@id = current()]/@title" /></strong>' был успешно создан</xsl:when>
							<xsl:when test="@name = 'MetaWasDeleted'">Мета-тег для секции '<strong><xsl:value-of select="$ST222//section[@id = current()]/@title" /></strong>' успешно удален</xsl:when>
							<xsl:when test="@name = 'MetaWasChanged'">Изменения в мета-теге для секции '<strong><xsl:value-of select="$ST222//section[@id = current()]/@title" /></strong>' успешно сохранены</xsl:when>
							<xsl:otherwise>
								Неизвестный код информационного сообщения: 
								<strong><xsl:value-of select="@name" /></strong><br />
								Тело сообщения: 
								<strong><xsl:value-of select="." /></strong>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>