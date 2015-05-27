<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:idm="http://infodesign.ru" exclude-result-prefixes="idm">
	<xsl:include href="includes/sectionrights.inc.xslt"/>
	<xsl:variable name="secTree" select="$SC//section[@title = 'Дерево секций']"/>
	<xsl:template>
		<script type="text/javascript">
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
	    </script>
		<xsl:variable name="ST2" select="section"/>
		<xsl:variable name="curSec">
			<xsl:choose>
				<xsl:when test="$Query/param[@name = 'id']">
					<xsl:value-of select="$Query/param[@name = 'id']"/>
				</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="CS" select="$ST2//section[@id = $curSec]"/>
		<xsl:variable name="SR2" select="$CS/ancestor-or-self::section"/>
		<xsl:variable name="RootSectionName" select="'Дерево секций'"/>
		<xsl:variable name="from">
			<xsl:if test="$Query/param[@name = 'from']">
				<xsl:value-of select="$Query/param[@name = 'from']"/>
			</xsl:if>
		</xsl:variable>
		<xsl:variable name="fromURL">
			<xsl:if test="$from != ''">
				<xsl:text>&amp;from=</xsl:text><xsl:value-of select="$from"/>
			</xsl:if>
		</xsl:variable>
		<table summary="general table" cellspacing="0" id="general">
			<tr id="upmenu">
				<td>
					<xsl:call-template name="GenNavi">
						<xsl:with-param name="CS" select="$CS"/>
						<xsl:with-param name="curSec" select="$curSec"/>
						<xsl:with-param name="RootSectionName" select="$RootSectionName"/>
						<xsl:with-param name="fromURL" select="$fromURL"/>
					</xsl:call-template>
				</td>
				<td class="submenu">
					<xsl:if test="$ST//section[@name = 'admin']">
						<a href="{$ST//section[@name = 'admin']/@URL}">Администрирование</a> | 
					</xsl:if>
					<xsl:if test="$ST//section[@name = 'users']">
						<a href="{$ST//section[@name = 'users']/@URL}">Пользователи</a> | 
					</xsl:if>
					<xsl:if test="$ST//section[@name = 'allorders']">
						<a href="{$ST//section[@name = 'allorders']/@URL}">Заказы</a> | 
					</xsl:if>
					<a>
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="$ST//section[@name = 'secadmin']">
									<xsl:value-of select="$ST//section[@name = 'secadmin']/@URL"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="$ST//section[@name = 'admin']/@URL"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>Секции
					</a> |
					<xsl:if test="$ST//section[@name = 'listedit']">
						<a href="{$ST//section[@name = 'listedit']/@URL}">Списки</a> | 
					</xsl:if>
					<a href="{$prefix}">
						<strong>Перейти на сайт</strong>
					</a> | 
					<a href="{$prefix}?writemodule=Authorize&amp;logoff=1">Выход</a>
				</td>
			</tr>
			<tr>
				<td class="main" colspan="2">
					<table summary="menu and content" cellspacing="0" id="main">
						<tr>
							<td id="menu">
								<xsl:if test="$from != ''">
									<div class="divMoveInfo">
										<strong>Перенос секции</strong>  «<xsl:value-of select="$ST2//section[@id = $from]/@title"/>»:
										выберите целевую секцию 
										(<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;ref={@id}&amp;id={$curSec}&amp;act=move">Перенести в выбранную секцию</a> | 
										<a href="{$Query/@url}?id={$from}">Отмена</a>)
									</div>
								</xsl:if>
								<ul>
									<li>
										<img src="{$prefix}adminimg/menu_minus_clear.gif" alt="-"/>
										<a href="{$Query/@url}?id=0{$fromURL}">
											<xsl:if test="$curSec = 0">
												<xsl:attribute name="class">menuActive</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="$RootSectionName"/>
										</a>
										<ul>
											<xsl:for-each select="$ST2/section">
												<xsl:call-template name="GenMenu" select=".">
													<xsl:with-param name="curSec" select="$curSec"/>
													<xsl:with-param name="fromURL" select="$fromURL"/>
												</xsl:call-template>
											</xsl:for-each>
										</ul>
									</li>
								</ul>
							</td>
							<td id="rightmenu">
								<xsl:call-template name="GetInfo">
									<xsl:with-param name="ST2" select="$ST2"/>
									<xsl:with-param name="curSec" select="$curSec"/>
									<xsl:with-param name="ref" select="@id"/>
									<xsl:with-param name="fromURL" select="$fromURL"/>
								</xsl:call-template>
								<xsl:call-template name="ViewErrors">
									<xsl:with-param name="path" select="./error"/>
								</xsl:call-template>
								<xsl:call-template name="ViewInfo">
									<xsl:with-param name="path" select="./info"/>
								</xsl:call-template>
								<!--div style="padding-left: 20px;">
									<xsl:call-template name="debugVars" />
								</div-->
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr id="downmenu">
				<td colspan="2">&#160;</td>
			</tr>
		</table>
	</xsl:template>
	<xsl:template name="GenMenu">
		<xsl:param name="curSec"/>
		<xsl:param name="fromURL"/>
		<xsl:variable name="isCurrent" select="@id = $curSec"/>
		<xsl:variable name="isEmpty" select="not(.//section)"/>
		<xsl:variable name="mustBeOpen" select=".//section/@id = $curSec or $isCurrent"/>
		<xsl:variable name="secURL" select="concat($Query/@url, '?id=', @id, $fromURL)"/>
		<xsl:variable name="secName" select="@title"/>
		<li>
			<xsl:choose>
				<xsl:when test="$mustBeOpen">
					<xsl:choose>
						<xsl:when test="$isEmpty">
							<img src="{$prefix}adminimg/menu_dot_clear.gif" alt="-"/>
						</xsl:when>
						<xsl:otherwise>
							<img src="{$prefix}adminimg/menu_minus_clear.gif" alt="-"/>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="@hidden = 1">
						<span class="hidden" title="Скрытая секция. Не отображается в меню, но доступна через строку навигации браузера">[Ск]</span>&#160;
					</xsl:if>
					<a href="{$secURL}">
						<xsl:variable name="tmp">
							<xsl:if test="@enabled = 0">menuDisabled</xsl:if>
						</xsl:variable>
						<xsl:if test="$isCurrent">
							<xsl:attribute name="class">
								<xsl:value-of select="concat('menuActive ', $tmp)"/>
							</xsl:attribute>
						</xsl:if>
						<xsl:if test="@enabled = 0">
							<xsl:attribute name="title">Секция выключена (не отображается в меню и не доступна через адресную строку браузера)</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="$secName"/>
						<!--id [<xsl:value-of select="@id" />]-->
					</a>
					<ul>
						<xsl:for-each select="./section">
							<xsl:call-template name="GenMenu" select="section">
								<xsl:with-param name="curSec" select="$curSec"/>
								<xsl:with-param name="fromURL" select="$fromURL"/>
							</xsl:call-template>
						</xsl:for-each>
					</ul>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="$isEmpty">
							<img src="{$prefix}adminimg/menu_dot_clear.gif" alt="-"/>
						</xsl:when>
						<xsl:otherwise>
							<img src="{$prefix}adminimg/menu_plus_clear.gif" alt="+"/>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="@hidden = 1">
						<span class="hidden" title="Скрытая секция. Не отображается в меню, но доступна через строку навигации браузера">[Ск]</span>&#160;
					</xsl:if>
					<a href="{$secURL}">
						<xsl:if test="$isCurrent">
							<xsl:attribute name="class">menuActive</xsl:attribute>
						</xsl:if>
						<xsl:if test="@enabled = 0">
							<xsl:attribute name="class">menuDisabled</xsl:attribute>
							<xsl:attribute name="title">Секция выключена (не отображается в меню и не доступна через адресную строку браузера)</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="$secName"/>
						<!--id [<xsl:value-of select="@id" />]-->
					</a>
				</xsl:otherwise>
			</xsl:choose>
		</li>
	</xsl:template>
	<xsl:template name="GetInfo">
		<xsl:param name="ST2"/>
		<xsl:param name="curSec"/>
		<xsl:param name="ref"/>
		<xsl:param name="fromURL"/>
		<xsl:variable name="CS" select="$ST2//section[@id = $curSec] | $ST2[@id = $curSec]"/>
		<table class="properties" cellpadding="0" cellspacing="0">
			<xsl:choose>
				<xsl:when test="$curSec = 0">
					<xsl:call-template name="CreateAndDelete">
						<xsl:with-param name="ST2" select="$ST2"/>
						<xsl:with-param name="CS" select="$CS"/>
						<xsl:with-param name="fromURL" select="$fromURL"/>
						<xsl:with-param name="ref" select="$ref"/>
						<xsl:with-param name="curSec" select="$curSec"/>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test="$secTree/section[@name = 'adminfaq']">
						<tr>
							<td>
								<a href="javascript:void(0);" onclick="return popupWindow('{$ST//section[@name = 'adminfaq']/@URL}', 500, 500);">Справка для администратора</a>&#160;
									<a class="aHelp" onclick="return false" title="" href="javascript:void(0)">[<b>?</b>]</a>
							</td>
						</tr>
						<tr>
							<td class="separator">
								<div class="divSep"/>
							</td>
						</tr>
					</xsl:if>
					<tr>
						<td>Название: <strong>
								<xsl:value-of select="$CS/@title"/>
							</strong>
							<xsl:text> </xsl:text>
							<xsl:if test="$CS/@edit = 1">
								(<a onclick="if (rv=prompt('Введите новое название секции (до 255 символов)', '{$CS/@title}')) location.href='{$prefix}?writemodule=Sect{$fromURL}&amp;ref={$ref}&amp;id={$curSec}&amp;act=rename&amp;title=' + encodeURIComponent(rv) + '&amp;retpath=' + escape(location.href); return false" href="javascript:void(0)">Изменить</a>) 
							</xsl:if>
							<a class="aHelp" onclick="return false" title="Название секции &#8212; это имя, отображаемое в меню и в строке навигации" href="javascript:void(0)">[<b>?</b>]</a>
						</td>
					</tr>
					<tr>
						<td>Псевдоним: <strong>
								<a class="normal" href="{$prefix}{$CS/@name}/" title="Перейти в выбранную секцию (в новом окне)">
									<xsl:value-of select="$CS/@name"/>
								</a>
							</strong>
							<xsl:text> </xsl:text>
							<xsl:if test="$CS/@editName = 1">
								(<a onclick="if (rv=prompt('Введите новый псевдоним секции (до 50 символов)', '{$CS/@name}')) location.href='{$prefix}?writemodule=Sect{$fromURL}&amp;ref={$ref}&amp;id={$curSec}&amp;act=chname&amp;newname=' + encodeURIComponent(rv) + '&amp;retpath=' + escape(location.href) + '&amp;errpath=' + escape(location.href); return false" href="javascript:void(0)">Изменить</a>)
							</xsl:if>
							<a class="aHelp" onclick="return false" title="Псевдоним секции &#8212; это имя, под которым секция будет доступна из адресной строки браузера. Если имя секции &apos;abc&apos;, то секция будет доступна как http://www.domain.com/abc/. Разрешается использовать только латинские символы, цифры, дефис и знак подчёркивания" href="javascript:void(0)">[<b>?</b>]</a>
						</td>
					</tr>
					<tr>
						<td class="separator">
							<div class="divSep"/>
						</td>
					</tr>
					<!-- Redirect -->
					<tr>
						<td>Переадресация: 
								<strong>
								<xsl:choose>
									<xsl:when test="$CS/@isRedirect = 1">
										<a class="normal" target="_blank" href="{$CS/@URL}" title="{$CS/@URL} (в новом окне)">
											<xsl:choose>
												<xsl:when test="string-length($CS/@URL) > 12">
													<xsl:value-of select="substring($CS/@URL, 1, 12)"/>
													<xsl:text>&#8230;</xsl:text>
												</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="$CS/@URL"/>
												</xsl:otherwise>
											</xsl:choose>
										</a>
									</xsl:when>
									<xsl:otherwise>нет</xsl:otherwise>
								</xsl:choose>
							</strong>
							<xsl:text> </xsl:text>
							<xsl:if test="$CS/@edit = 1">
								<xsl:variable name="tmp">
									<xsl:choose>
										<xsl:when test="$CS/@isRedirect = 1">
											<xsl:value-of select="$CS/@URL"/>
										</xsl:when>
									</xsl:choose>
								</xsl:variable>
									(<a onclick="if (rv=prompt('Введите новый URL переадресации (до 255 символов)', '{$tmp}')) location.href='{$prefix}?writemodule=Sect{$fromURL}&amp;ref={$ref}&amp;id={$curSec}&amp;act=chredirect&amp;newname=' + encodeURIComponent(rv) + '&amp;retpath=' + escape(location.href) + '&amp;errpath=' + escape(location.href); return false" href="javascript:void(0)">Изменить</a>
								<xsl:choose>
									<xsl:when test="$CS/@URL != ''">
										<xsl:text>, </xsl:text>
										<a onclick="location.href='{$prefix}?writemodule=Sect{$fromURL}&amp;ref={$ref}&amp;id={$curSec}&amp;act=chredirect&amp;newname=&amp;retpath=' + escape(location.href) + '&amp;errpath=' + escape(location.href); return false" href="javascript:void(0)">Очистить</a>
										<xsl:text>)&#160;</xsl:text>
									</xsl:when>
									<xsl:otherwise>
										<xsl:text>)&#160;</xsl:text>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:if>
							<a class="aHelp" onclick="return false" title="URL переадресации &#8212; это адрес, на который будет осуществляться перенаправление пользователя с текущего адреса. Разрешается использовать только латинские символы, цифры, дефис, точку, двоеточие, прямой слеш и знак подчёркивания" href="javascript:void(0)">[<b>?</b>]</a>
						</td>
					</tr>
					<!-- Переход к первой дочерней -->
					<xsl:if test="$CS/section[@enabled = 1 and not($CS/@isRedirect)]">
						<tr>
							<td>
									Переход к первой дочерней: 
									<strong>
									<xsl:choose>
										<xsl:when test="$CS/@goToChild = 1">Вкл</xsl:when>
										<xsl:otherwise>Выкл</xsl:otherwise>
									</xsl:choose>
								</strong>
								<xsl:text> </xsl:text>
								<xsl:variable name="status">
									<xsl:choose>
										<xsl:when test="$CS/@goToChild = 1">0</xsl:when>
										<xsl:otherwise>1</xsl:otherwise>
									</xsl:choose>
								</xsl:variable>
								<xsl:if test="$CS/@edit = 1">
										(<a onclick="location.href='{$prefix}?writemodule=Sect{$fromURL}&amp;ref={$ref}&amp;id={$curSec}&amp;act=gotochild&amp;status={$status}&amp;retpath=' + escape(location.href); return false" href="javascript:void(0)">
										<xsl:choose>
											<xsl:when test="$CS/@goToChild = 1">Выключить</xsl:when>
											<xsl:otherwise>Включить</xsl:otherwise>
										</xsl:choose>
									</a>)
									</xsl:if>
								<a class="aHelp" onclick="return false" title="Переход к первой дочерней &#8212; определяет должен ли будет осуществлятся автоматический переход к первой дочерней секции, при щелчке на корневую секцию (для меню сайта)" href="javascript:void(0)">[<b>?</b>]</a>
							</td>
						</tr>
					</xsl:if>
					<tr>
						<td class="separator">
							<div class="divSep"/>
						</td>
					</tr>
					<tr>
						<td>
							<!-- Enabled/Disabled -->
							<xsl:choose>
								<xsl:when test="$CS/@enabled = 1">
									<img src="{$prefix}adminimg/bar_green.gif" alt=""/>Секция включена 
										<xsl:if test="$CS/@editEnabled = 1">
											(<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=disable&amp;ref={$ref}">Выключить</a>)
										</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<img src="{$prefix}adminimg/bar_red.gif" alt=""/>Секция выключена 
										<xsl:if test="$CS/@editEnabled = 1">										
											(<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=enable&amp;ref={$ref}">Включить</a>)
										</xsl:if>
								</xsl:otherwise>
							</xsl:choose>
							<a class="aHelp" onclick="return false" title="Выключенная секция не доступна пользователю ни из меню, ни при наборе адреса секции в адресной строке браузера" href="javascript:void(0)">[<b>?</b>]</a>
						</td>
					</tr>
					<xsl:if test="$CS/@enabled = 1">
						<tr>
							<td>
								<!-- Hidden/Открытая -->
								<xsl:choose>
									<xsl:when test="$CS/@hidden = 1">
										<img src="{$prefix}adminimg/bar_orange.gif" alt=""/>Скрытая секция 
											<xsl:if test="$CS/@edit = 1">
												(<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;ref={$ref}&amp;id={$curSec}&amp;act=show&amp;ref={$ref}">Показать</a>)
											</xsl:if>
									</xsl:when>
									<xsl:otherwise>
										<img src="{$prefix}adminimg/bar_green.gif" alt=""/>Секция отображается 
											<xsl:if test="$CS/@edit = 1">											
												(<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;ref={$ref}&amp;id={$curSec}&amp;act=hide&amp;ref={$ref}">Скрыть</a>)
											</xsl:if>
									</xsl:otherwise>
								</xsl:choose>
								<a class="aHelp" onclick="return false" title="Скрытая секция не отображается в меню, но доступна из адресной строки браузера" href="javascript:void(0)">[<b>?</b>]</a>
							</td>
						</tr>
						<tr>
							<td>
								<!-- OnMap/Не отображать на карте сайта -->
								<xsl:choose>
									<xsl:when test="$CS/@onMap = 1">
										<img src="{$prefix}adminimg/bar_green.gif" alt=""/>Секция отображается на карте сайта 
											<xsl:if test="$CS/@edit = 1">
												(<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=hideonmap&amp;ref={$ref}">Скрыть</a>)
											</xsl:if>
									</xsl:when>
									<xsl:otherwise>
										<img src="{$prefix}adminimg/bar_orange.gif" alt=""/>Секция скрыта на карте сайта 
											<xsl:if test="$CS/@edit = 1">											
												(<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=showonmap&amp;ref={$ref}">Показать</a>)
											</xsl:if>
									</xsl:otherwise>
								</xsl:choose>
								<a class="aHelp" onclick="return false" title="Определяет, будет ли секция отображаться на карте сайта" href="javascript:void(0)">[<b>?</b>]</a>
							</td>
						</tr>
					</xsl:if>
					<tr>
						<td class="separator">
							<div class="divSep"/>
						</td>
					</tr>
					<!-- Мета-теги секции -->
					<tr>
						<td>
							<xsl:text>Мета-теги</xsl:text>
							<xsl:if test="not($CS/meta)">:&#160;<strong>нет</strong>
							</xsl:if>
							<xsl:if test="$CS/@edit = 1">
									(<a onclick="if (rv=prompt('Задайте название мета-тега (name) и его содержание (content) в формате &quot;name:content&quot;', '')) location.href='{$prefix}?writemodule=Sect&amp;ref={$ref}&amp;id={$curSec}&amp;metaid=0&amp;act=metaedit&amp;text=' + encodeURIComponent(rv) + '&amp;retpath=' + escape(location.href) + '&amp;errpath=' + escape(location.href); return false" href="javascript:void(0)">Создать новый</a>)
								</xsl:if>
							<xsl:choose>
								<xsl:when test="$CS/meta">
									<br/>
									<table id="metaTags" cellpadding="0" cellspacing="0">
										<colgroup>
											<col width="3%"/>
											<col width="*"/>
										</colgroup>
										<xsl:for-each select="$CS/meta">
											<tr>
												<th>
													<xsl:choose>
														<xsl:when test="$CS/@edit = 1">
															<a onclick="if (rv=prompt('Задайте название мета-тега (name) и его содержание (content) в формате &quot;name:content&quot;', '{@name}: {.}')) location.href='{$prefix}?writemodule=Sect&amp;ref={$ref}&amp;id={$curSec}&amp;metaid={@id}&amp;act=metaedit&amp;text=' + encodeURIComponent(rv) + '&amp;retpath=' + escape(location.href) + '&amp;errpath=' + escape(location.href); return false" href="javascript:void(0)">
																<xsl:value-of select="@name"/>
															</a>
														</xsl:when>
														<xsl:otherwise>
															<xsl:value-of select="@name"/>
														</xsl:otherwise>
													</xsl:choose>
												</th>
												<td>
													<xsl:choose>
														<xsl:when test="$CS/@edit = 1">
															<a onclick="return confirm('Вы уверены, что хотите удалить мета-тег &quot;{@name}&quot; для секции &quot;{$CS/@title}&quot;? Данное действие отменить будет невозможно!')" href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;metaid={@id}&amp;act=metadelete&amp;ref={$ref}">
																<img style="margin-top: 2px;" src="{$prefix}adminimg/icon_delete_small.gif" width="12" height="11" alt="Удалить мета-тег {@name}"/>
															</a>
														</xsl:when>
														<xsl:otherwise>
															<img src="{$prefix}img/p.gif" width="12" height="11" alt=""/>
														</xsl:otherwise>
													</xsl:choose>
												</td>
												<td>
													<xsl:choose>
														<xsl:when test="string-length(.) > 65">
															<span title="{.}">
																<xsl:value-of select="substring(., 1, 65)"/>&#8230;</span>
														</xsl:when>
														<xsl:otherwise>
															<xsl:value-of select="."/>
														</xsl:otherwise>
													</xsl:choose>
												</td>
											</tr>
										</xsl:for-each>
									</table>
								</xsl:when>
								<xsl:otherwise>
									</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
					<tr>
						<td class="separator">
							<div class="divSep"/>
						</td>
					</tr>
					<xsl:if test="$CS/../@edit = 1">
						<tr>
							<td>
								<!-- В самый верх -->
								<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=movetotop&amp;ref={$ref}">
									<img src="{$prefix}adminimg/icon_totop.gif" alt=""/>В самый верх</a>
							</td>
						</tr>
						<tr>
							<td>
								<!-- Вверх -->
								<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=moveup&amp;ref={$ref}">
									<img src="{$prefix}adminimg/icon_up.gif" alt=""/>Вверх</a>
							</td>
						</tr>
						<tr>
							<td>
								<!-- Вниз -->
								<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=movedown&amp;ref={$ref}">
									<img src="{$prefix}adminimg/icon_down.gif" alt=""/>Вниз</a>
							</td>
						</tr>
						<tr>
							<td>
								<!-- В самый низ -->
								<a href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=movetobottom&amp;ref={$ref}">
									<img src="{$prefix}adminimg/icon_tobottom.gif" alt=""/>В самый низ</a>
							</td>
						</tr>
					</xsl:if>
					<xsl:if test="$CS/@edit = 1">
						<tr>
							<td>
								<!-- Перенести секцию -->
								<a href="{$Query/@url}?from={$curSec}&amp;id={$curSec}">
									<img src="{$prefix}adminimg/icon_right.gif" alt=""/>Перенести секцию</a>
							</td>
						</tr>
					</xsl:if>
					<xsl:if test="$CS/../@edit = 1 or $CS/@edit = 1">
						<tr>
							<td class="separator">
								<div class="divSep"/>
							</td>
						</tr>
					</xsl:if>
					<xsl:call-template name="CreateAndDelete">
						<xsl:with-param name="ST2" select="$ST2"/>
						<xsl:with-param name="CS" select="$CS"/>
						<xsl:with-param name="fromURL" select="$fromURL"/>
						<xsl:with-param name="ref" select="$ref"/>
						<xsl:with-param name="curSec" select="$curSec"/>
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
			<tr>
				<td>
					<xsl:call-template name="sectionRightsEdit"/>
				</td>
			</tr>
		</table>
	</xsl:template>
	<xsl:template name="CreateAndDelete">
		<xsl:param name="ST2"/>
		<xsl:param name="CS"/>
		<xsl:param name="fromURL"/>
		<xsl:param name="ref"/>
		<xsl:param name="curSec"/>
		<xsl:if test="$CS/@create = 1 and createList/item[(($ST//section[@id = $Query/param[@name = 'id']]/ancestor-or-self::section/@name) = (aux[@name = 'ancestor'])) or not(string(aux[@name = 'ancestor']))]">
			<xsl:for-each select="createList/item[(($ST//section[@id = $Query/param[@name = 'id']]/ancestor-or-self::section/@name) = (aux[@name = 'ancestor'])) or not(string(aux[@name = 'ancestor']))]">
				<tr>
					<td>
						<a onclick="if (rv=prompt('Введите название новой секции (до 255 символов)', '')) location.href='{$prefix}?writemodule=Sect{$fromURL}&amp;ref={$ref}&amp;id={$curSec}&amp;act=create&amp;createtype={@id}&amp;title=' + encodeURIComponent(rv) + '&amp;retpath=' + escape(location.href); return false" href="javascript:void(0)">
							<img src="{$prefix}adminimg/icon_create.gif" alt=""/>
							<xsl:value-of select="aux[@name = 'title']"/>
						</a>
					</td>
				</tr>
			</xsl:for-each>
			<tr>
				<td class="separator">
					<div class="divSep"/>
				</td>
			</tr>
		</xsl:if>
		<xsl:if test="$CS/@delete = 1">
			<tr>
				<td>
					<xsl:choose>
						<xsl:when test="$CS/../@id">
							<a onclick="return confirm('Вы уверены, что хотите удалить секцию &quot;{$CS/@title}&quot;? Данное действие отменить будет невозможно!')" href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=delete&amp;ref={$ref}&amp;retpath={$Query/@urlEscaped}%3Fid%3D{$CS/../@id}%26from%3D{$Query/param[@name = 'from']}">
								<img src="{$prefix}adminimg/icon_delete.gif" alt=""/>Удалить секцию</a>
						</xsl:when>
						<xsl:otherwise>
							<a onclick="return confirm('Вы уверены, что хотите удалить секцию &quot;{$CS/@title}&quot;? Данное действие отменить будет невозможно!')" href="{$prefix}?writemodule=Sect{$fromURL}&amp;id={$curSec}&amp;act=delete&amp;ref={$ref}">
								<img src="{$prefix}adminimg/icon_delete.gif" alt=""/>Удалить секцию</a>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<tr>
				<td class="separator">
					<div class="divSep"/>
				</td>
			</tr>
		</xsl:if>
	</xsl:template>
	<xsl:template name="GenNavi">
		<xsl:param name="CS"/>
		<xsl:param name="curSec"/>
		<xsl:param name="RootSectionName"/>
		<xsl:param name="fromURL"/>
		<xsl:choose>
			<xsl:when test="$curSec = 0">
				<xsl:value-of select="$RootSectionName"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:for-each select="$CS/ancestor-or-self::section">
					<xsl:choose>
						<xsl:when test="@id = $curSec">
							<xsl:value-of select="@title"/>
						</xsl:when>
						<xsl:otherwise>
							<a href="{$Query/@url}?id={@id}{$fromURL}">
								<xsl:value-of select="@title"/>
							</a> /
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="ViewErrors">
		<xsl:param name="path"/>
		<xsl:if test="$path">
			<div class="divError">
				<xsl:for-each select="$path/item">
					<div class="error">
						<xsl:choose>
							<xsl:when test="@name = 'BadRights'">Извините, у Вас нет прав на выполнение запрошенного действия</xsl:when>
							<xsl:when test="@name = 'BadMoveRights'">Извините, у Вас нет прав на выполнение переноса секции</xsl:when>
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
							<xsl:when test="@name = 'NameExists'">Секция с псевдонимом '<strong>
									<xsl:value-of select="."/>
								</strong>' уже существует</xsl:when>
							<xsl:when test="@name = 'CantMoveTopmost'">Нельзя переместить секцию '<strong>
									<xsl:value-of select="."/>
								</strong>' вверх</xsl:when>
							<xsl:when test="@name = 'CantMoveBottommost'">Нельзя переместить секцию '<strong>
									<xsl:value-of select="."/>
								</strong>' вниз</xsl:when>
							<xsl:when test="@name = 'MoveSectionIsNotExist'">Неверный идентификатор перемещаемой секции.</xsl:when>
							<xsl:when test="@name = 'BadFromField'">Неверный идентификатор перемещаемой секции. Возможно, переносимая секция уже была удалена</xsl:when>
							<xsl:when test="@name = 'BadMoveNode'">Секция не может быть перемещена сама в себя или в свою дочернюю ноду</xsl:when>
							<xsl:when test="@name = 'BadRedirectChars'">URL переадресации может содержать только символы латинского алфавита, цифры, дефис, знак подчёркивания, ?, &amp;, . и т.д.<br/>Примеры URL:<br/>http://www.yandex.ru/<br/>ftp://sitename.org<br/>https://web13.local.sitename.net/script-name.php?p1=abc&amp;p2=def#anchor1</xsl:when>
							<xsl:when test="@name = 'BadGoToChildField'">Неверное значение параметра «Переход к первой дочерней»</xsl:when>
							<xsl:when test="@name = 'BadMetaTextField'">Неверное значение текста мета-тега</xsl:when>
							<xsl:when test="@name = 'BadCreateType'">Некорректное значение типа создаваемой секции</xsl:when>
							<xsl:otherwise>
								Неизвестный код сообщения об ошибке:
								<strong>
									<xsl:value-of select="@name"/>
								</strong>
								<br/>
								Тело сообщения: 
								<strong>
									<xsl:value-of select="."/>
								</strong>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>
	<xsl:template name="ViewInfo">
		<xsl:param name="path"/>
		<xsl:if test="$path">
			<div class="divInfo">
				<xsl:for-each select="$path/item">
					<div class="info">
						<xsl:choose>
							<xsl:when test="@name = 'TitleWasChanged'">Название секции было изменено на '<strong>
									<xsl:value-of select="."/>
								</strong>'</xsl:when>
							<xsl:when test="@name = 'NameWasChanged'">Псевдоним секции был изменен на '<strong>
									<xsl:value-of select="."/>
								</strong>'</xsl:when>
							<xsl:when test="@name = 'RedirectWasChanged'">
								<xsl:choose>
									<xsl:when test=". != ''">URL переадресации секции был изменен на '<strong>
											<xsl:value-of select="."/>
										</strong>'</xsl:when>
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
							<xsl:when test="@name = 'SectionWasCreated'">Создана секция '<strong>
									<xsl:value-of select="."/>
								</strong>'</xsl:when>
							<xsl:when test="@name = 'SectionMovedUp'">Секция '<strong>
									<xsl:value-of select="."/>
								</strong>' перемещена вверх</xsl:when>
							<xsl:when test="@name = 'SectionMovedDown'">Секция '<strong>
									<xsl:value-of select="."/>
								</strong>' перемещена вниз</xsl:when>
							<xsl:when test="@name = 'SectionMoved'">Секция '<strong>
									<xsl:value-of select="."/>
								</strong>' перемещена</xsl:when>
							<xsl:when test="@name = 'SectionMovedToTop'">Секция '<strong>
									<xsl:value-of select="."/>
								</strong>' перемещена в самый верх</xsl:when>
							<xsl:when test="@name = 'SectionMovedToBottom'">Секция '<strong>
									<xsl:value-of select="."/>
								</strong>' перемещена в самый низ</xsl:when>
							<xsl:when test="@name = 'MetaWasCreated'">Мета-тег для секции '<strong>
									<xsl:value-of select="$secTree//section[@id = current()]/@title"/>
								</strong>' был успешно создан</xsl:when>
							<xsl:when test="@name = 'MetaWasDeleted'">Мета-тег для секции '<strong>
									<xsl:value-of select="$secTree//section[@id = current()]/@title"/>
								</strong>' успешно удален</xsl:when>
							<xsl:when test="@name = 'MetaWasChanged'">Изменения в мета-теге для секции '<strong>
									<xsl:value-of select="$secTree//section[@id = current()]/@title"/>
								</strong>' успешно сохранены</xsl:when>
							<xsl:when test="@name = 'SectionRightsWasChanged'">
								<xsl:text>Права на секцию были изменены</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								Неизвестный код информационного сообщения: 
								<strong>
									<xsl:value-of select="@name"/>
								</strong>
								<br/>
								Тело сообщения: 
								<strong>
									<xsl:value-of select="."/>
								</strong>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
