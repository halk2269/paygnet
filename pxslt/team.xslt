<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:choose>
			<xsl:when test="@documentID">
				<xsl:apply-templates />
			</xsl:when>
			<xsl:otherwise>
				<div>
					<xsl:call-template name="adminCreate"/>
				</div>
				<div class="divTeamList">
					<table cellpadding="0" cellspacing="0">
						<xsl:for-each select="document">
							<tr>
								<td class="tdTeamListItem">
									<div class="divTeamListTitle">
										<!-- document move check -->
										<xsl:call-template name="docMoveChecker" />	
										<a href="{@URL}"><xsl:value-of select="field[@name = 'title']"/></a>
										<xsl:if test="@enabled = 0"> [Disabled]</xsl:if>
										<xsl:call-template name="adminEditDel" />
									</div>
									<div class="divTeamListState">
										State:&#160;<xsl:value-of select="field[@name = 'state']"/>
									</div>
									<div class="divTeamListCity">
										City:&#160;<xsl:value-of select="field[@name = 'city']" />
									</div>
								</td>
							</tr>
						</xsl:for-each>
					</table>
					
					<!-- document move option -->
					<xsl:call-template name="DocMoveOption" />
					
					<xsl:call-template name="Paging">
						<xsl:with-param name="className" select="'divTeamPages'" />
					</xsl:call-template>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>