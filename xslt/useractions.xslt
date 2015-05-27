<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:choose>
			<xsl:when test="not(firstAction)">
				<strong>Ни одного действия в системе зарегистрировано не было.</strong>
			</xsl:when>
			<xsl:otherwise>
				<div class="intervalForm">
					<form id="intervalForm" action="{$SC/@URL}" method="get">
						<xsl:variable name="start" select="$SC//module/startInterval"/>
						<xsl:variable name="end" select="$SC//module/endInterval"/>
						<p>Статистика ведется с <xsl:value-of select="firstAction/@day"/>-<xsl:value-of select="firstAction/@month"/>-<xsl:value-of select="firstAction/@year"/>.</p>
						<p>
							<xsl:text>Вывести статистику c </xsl:text>
							<input name="start" type="text" size="9" id="start" maxlength="10" value="{$start/@year}-{$start/@month}-{$start/@day}"/>
							<xsl:text> по </xsl:text>
							<input name="end" type="text" size="9" id="end" maxlength="10" value="{$end/@year}-{$end/@month}-{$end/@day}"/>
							<xsl:text> (дата должна быть в формате ГГГГ-ММ-ДД)</xsl:text>
						</p>
						<input type="submit" value="Отобразить"/>
					</form>
				</div>
				<br/>
				<xsl:choose>
					<xsl:when test="actions/action">
						<strong>
							<xsl:text>Действий за указанный интервал: </xsl:text>
							<xsl:value-of select="count(actions/action)"/>
						</strong>
						<table cellspacing="0" cellpadding="5" border="0" width="100%" style="margin-top:10px;" class="border">
							<tr>
								<th>Время</th>
								<th>Логин</th>
								<th>Действия</th>
							</tr>
							<xsl:for-each select="actions/action">
								<tr>
									<td>
										<xsl:value-of select="@time"/>
									</td>
									<td style="text-align:center;">
										<xsl:value-of select="@userLogin"/>
									</td>
									<xsl:choose>
										<xsl:when test="@type = 1">
											<td>
												<xsl:call-template name="sectionActionName">
													<xsl:with-param name="actionName" select="@action"/>
													<xsl:with-param name="id" select="@sectionId"/>
													<xsl:with-param name="title" select="@sectionTitle"/>
												</xsl:call-template>
											</td>
										</xsl:when>
										<xsl:otherwise>
											<td>
												<xsl:call-template name="documentActionName">
													<xsl:with-param name="actionName" select="@action"/>
													<xsl:with-param name="docType" select="@docType"/>
												</xsl:call-template>
											</td>
										</xsl:otherwise>
									</xsl:choose>
								</tr>
							</xsl:for-each>
						</table>
					</xsl:when>
					<xsl:otherwise>
						<strong>За указанный интервал никаких действий совершено не было.</strong>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="sectionActionName">
		<xsl:param name="actionName"/>
		<xsl:param name="id"/>
		<xsl:param name="title"/>
		<xsl:choose>
			<xsl:when test="$actionName = 'rename'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> переинаменована</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'chname'">
				<xsl:text>У секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> изменен псевдоним</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'chredirect'">
				<xsl:text>У секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> изменена переадресация</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'gotochild'">
				<xsl:text>У секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> изменена настройка "переход к первой дочерней"</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'show'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> отображается на сайте</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'hide'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> скрыта</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'showonmap'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> отображается на карте сайта</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'hideonmap'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> скрыта на карте сайта</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'enable'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> включена</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'disable'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> выключена</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'delete'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> удалена</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'create'">
				<xsl:text>В секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> создана дочерняя секция</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'moveup'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> перемещена вверх</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'movedown'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> перемещена вниз</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'move'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> перемещена</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'movetotop'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> перемещена в самый верх</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'movetobottom'">
				<xsl:text>Секция </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> перемещена в самый низ</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'metaedit'">
				<xsl:text>Мета-тег для секции </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> был успешно изменен</xsl:text>
			</xsl:when>
			<xsl:when test="$actionName = 'metadelete'">
				<xsl:text>Мета-тег для секции </xsl:text>
				<xsl:call-template name="printSectionURL">
					<xsl:with-param name="id" select="$id"/>
					<xsl:with-param name="title" select="$title"/>
				</xsl:call-template>
				<xsl:text> успешно удален</xsl:text>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="printSectionURL">
		<xsl:param name="id"/>
		<xsl:param name="title"/>
		<xsl:choose>
			<xsl:when test="$ST//section[@id = $id]/@URL">
				<a href="{$ST//section[@id = $id]/@URL}" target="_blank">
					<xsl:value-of select="$ST//section[@id = $id]/@title"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<strong>
					<xsl:value-of select="$title"/>
				</strong>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="documentActionName">
		<xsl:param name="actionName"/>
		<xsl:param name="docType"/>
		<xsl:choose>
			<xsl:when test="$actionName = 'Create'">
				<xsl:text>Создан документ типа </xsl:text>
				<strong>
					<xsl:value-of select="$docType"/>
				</strong>
			</xsl:when>
			<xsl:when test="$actionName = 'Edit'">
				<xsl:text>Изменён документ типа </xsl:text>
				<strong>
					<xsl:value-of select="$docType"/>
				</strong>
			</xsl:when>
			<xsl:when test="$actionName = 'Delete'">
				<xsl:text>Удален документ типа </xsl:text>
				<strong>
					<xsl:value-of select="$docType"/>
				</strong>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
