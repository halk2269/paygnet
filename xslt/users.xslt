<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:choose>
			<xsl:when test="@documentID or $Query/param[@name = 'rolename']">
				<xsl:apply-templates/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="roles/role">
					<p>
						<strong>Создание нового пользователя</strong>
						<form action="{$SC/@URL}" method="get">
							<table cellpadding="3" cellspacing="0" border="0">
								<tr>
									<td>
										<xsl:text>Выберите роль </xsl:text>
									</td>
									<td>
										<select name="rolename" style="margin-top: 1px;">
											<xsl:for-each select="roles/role">
												<option value="{@name}">
													<xsl:value-of select="text()"/>
												</option>
											</xsl:for-each>
										</select>
									</td>
									<td valign="top">
										<input type="submit" value="Create"/>
									</td>
								</tr>
							</table>
						</form>
					</p>
				</xsl:if>
				<xsl:if test="not(document)">
					<div>
						<xsl:text>Ни одного пользователя не найдено</xsl:text>
					</div>
				</xsl:if>
				<xsl:if test="document">
					<div class="table">
						<table cellpadding="0" cellspacing="1" border="0" width="300" class="userTable">
							<tr class="color1">
								<th>
									<xsl:text>Логин</xsl:text>
								</th>
								<th>
									<xsl:text>Отключен</xsl:text>
								</th>
								<th>Удаление</th>
							</tr>
							<xsl:for-each select="document">
								<tr class="color{position() mod 2 + 1}">
									<td class="leftalign" width="50%">
										<a href="{@URL}">
											<xsl:value-of select="field[@name = 'login']"/>
										</a>
									</td>
									<td class="centeralign" width="50%">
										<xsl:choose>
											<xsl:when test="@enabled = 0">
												<xsl:text>Да</xsl:text>
											</xsl:when>
											<xsl:otherwise>
												<xsl:text>&#160;</xsl:text>
											</xsl:otherwise>
										</xsl:choose>
									</td>
									<td align="center">
										<xsl:choose>
											<xsl:when test="field[@name = 'role_id'] != $Visitor/user/field[@name = 'role_id']">
												<a href="{@deleteURL}"><img src="{$prefix}adminimg/delete.gif" alt=""/></a>
											</xsl:when>
											<xsl:otherwise>&#160;</xsl:otherwise>
										</xsl:choose>
									</td>
								</tr>
							</xsl:for-each>
						</table>
					</div>
					<xsl:if test="pages/page[2]">
						<div class="divUsersPages">
							<xsl:text>Страницы: </xsl:text>
							<xsl:for-each select="pages/page">
								<xsl:choose>
									<xsl:when test="@isCurrent">
										<xsl:value-of select="@num"/>
									</xsl:when>
									<xsl:otherwise>
										<a href="{@URL}">
											<xsl:value-of select="@num"/>
										</a>
									</xsl:otherwise>
								</xsl:choose>
								<xsl:if test="position() != last()">
									<xsl:text> | </xsl:text>
								</xsl:if>
							</xsl:for-each>
						</div>
					</xsl:if>
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
