<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:choose>
			<xsl:when test="@documentID">
				<xsl:apply-templates/>
			</xsl:when>
			<xsl:otherwise>
				<div>
					<xsl:call-template name="adminCreate"/>
				</div>
				<br />
		<!-- DEBUG INFORMATION -->
		<!--h4>[Info]</h4>
		<xsl:for-each select="info/item">
			<strong>
				<xsl:value-of select="@name"/>
			</strong>: <xsl:value-of select="."/>
			<br/>
		</xsl:for-each>
		<h4>[Vars]</h4>
		<h4>- Own</h4>
		<xsl:for-each select="vars/own/var">
			<strong>
				<xsl:value-of select="@name"/>
			</strong>: <xsl:value-of select="."/>
			<br/>
		</xsl:for-each>
		<h4>- General</h4>
		<xsl:for-each select="vars/general/var">
			<strong>
				<xsl:value-of select="@name"/>
			</strong>: <xsl:value-of select="."/>
			<br/>
		</xsl:for-each>
		<h4>- User</h4>
		<xsl:for-each select="vars/user/var">
			<strong>
				<xsl:value-of select="@name"/>
			</strong>: <xsl:value-of select="."/>
			<br/>
		</xsl:for-each-->
			
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<xsl:variable name="qreference" select="@id" />
					<xsl:for-each select="document">
						<tr>
						
						
							<td align="left" valign="top">
								<strong><xsl:value-of select="field[@name = 'email']"/></strong>
								<xsl:if test="@enabled = 0"> [Отключен]</xsl:if>
								<xsl:call-template name="adminEditDel" />
								<br />
								Статус: 
								<xsl:variable name="status" select="field[@name = 'status']" />
								<xsl:choose>
									<xsl:when test="$status = 1">Получает рассылку</xsl:when>
									<xsl:when test="$status = 2">Ожидание подтверждения подписки</xsl:when>
									<xsl:when test="$status = 3">Ожидание подтверждения отписки</xsl:when>
									<xsl:otherwise>Ошибочный статус</xsl:otherwise>
								</xsl:choose><br />
								<br /><br />
								<!-- Дата отсылки сообщения подтверждения действий: <xsl:value-of select="field[@name = 'notifdate']" /><br /> -->
							</td>
						</tr>
					</xsl:for-each>
					<xsl:if test="pages/page[2]">
						<tr>
							<td align="left" valign="top">
								<div class="divBannerPages">
									Страницы: 
									<xsl:for-each select="pages/page">
										<xsl:choose>
											<xsl:when test="@isCurrent">
												<xsl:value-of select="@num" />
											</xsl:when>
											<xsl:otherwise>
												<a href="{@URL}"><xsl:value-of select="@num" /></a>
											</xsl:otherwise>
										</xsl:choose>
										<xsl:if test="position() != last()">
											<xsl:text> | </xsl:text>
										</xsl:if>
									</xsl:for-each>
								</div>
							</td>
						</tr>
					</xsl:if>
				</table>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
