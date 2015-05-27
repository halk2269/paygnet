

	<xsl:template match="module[@id = 42]">
		<xsl:choose>
			<xsl:when test="@documentID">
				<xsl:apply-templates />
			</xsl:when>
			<xsl:otherwise>
				<div>
					<xsl:call-template name="adminCreate"/>
				</div>
				<div class="divAwardList">
					<table cellpadding="0" cellspacing="0" width="100%">
						<xsl:for-each select="document">
							<tr>
								<td class="tdAwardListItem">
									<div class="divAwardListTitle">
										<!-- document move check -->
										<xsl:call-template name="docMoveChecker" />	
										<xsl:value-of select="field[@name = 'title']"/>
										<xsl:if test="@enabled = 0"> [Disabled]</xsl:if>
										<xsl:call-template name="adminEditDel" />
									</div>
									<xsl:if test="field[@name = 'smallimg']/@file_id > 0">
										<div class="divAwardListImage">
											<img src="{field[@name = 'smallimg']/@URL}" alt="{field[@name = 'title']}" />
										</div>
									</xsl:if>
									<div class="divAwardListDesc">
										<xsl:value-of select="field[@name = 'description']" disable-output-escaping="yes" />
									</div>
								</td>
							</tr>
						</xsl:for-each>
					</table>
					
					<!-- document move option -->
					<xsl:call-template name="DocMoveOption" />
					
					<xsl:call-template name="Paging">
						<xsl:with-param name="className" select="'divAwardPages'" />
					</xsl:call-template>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
