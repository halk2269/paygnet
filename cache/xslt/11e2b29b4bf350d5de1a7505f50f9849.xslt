

	<xsl:template match="module[@id = 11]">
		<xsl:choose>
			<xsl:when test="@documentID">
				<xsl:apply-templates />
			</xsl:when>
			<xsl:otherwise>
				<div>
					<xsl:call-template name="adminCreate"/>
				</div>
				<div class="divArtList">
					<table cellpadding="0" cellspacing="0">
						<xsl:for-each select="document">
							<tr>
								<td class="tdArtListItem">
									<div class="divArtListTitle">
										<!-- document move check -->
										<xsl:call-template name="docMoveChecker" />	
										<a href="{@URL}"><xsl:value-of select="field[@name = 'title']"/></a>
										<xsl:if test="@enabled = 0"> [Disabled]</xsl:if>
										<xsl:call-template name="adminEditDel" />
									</div>
									<div class="divArtListDate">
										Published:&#160;<xsl:value-of select="field[@name = 'pubdate']"/>
									</div>
									<div class="divArtListPreview">
										<xsl:value-of select="field[@name = 'preview']" />
									</div>
								</td>
							</tr>
						</xsl:for-each>
					</table>
					
					<!-- document move option -->
					<xsl:call-template name="DocMoveOption" />
					
					<xsl:call-template name="Paging">
						<xsl:with-param name="className" select="'divArtPages'" />
					</xsl:call-template>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
