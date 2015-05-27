

	<xsl:template match="module[@id = 10]">
		<xsl:choose>
			<xsl:when test="@documentID">
				<xsl:apply-templates />
			</xsl:when>
			<xsl:otherwise>
				<div>
					<xsl:call-template name="adminCreate"/>
				</div>
				<div class="divNewsList">
					<xsl:for-each select="document">
						<div class="divNewsListItem">
							<div class="divNewsListDate">
								<xsl:value-of select="field[@name = 'pubdate']"/>
							</div>
							<div class="divNewsListTitle">
								<!-- Document move check -->
								<xsl:call-template name="docMoveChecker" />
								<a href="{@URL}"><xsl:value-of select="field[@name = 'title']"/></a>
								<xsl:if test="@enabled = 0"> [Отключен]</xsl:if>
								<xsl:call-template name="adminEditDel" />
							</div>
							<div class="divNewsListPreview">
								<xsl:value-of select="field[@name = 'preview']" />
							</div>
						</div>
					</xsl:for-each>
					<!-- Document move option -->
					<xsl:call-template name="DocMoveOption" />
					<!-- Pages -->
					<xsl:call-template name="Paging">
						<xsl:with-param name="className" select="'divNewsPages'" />
					</xsl:call-template>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
