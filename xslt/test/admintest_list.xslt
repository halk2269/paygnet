<?xml version="1.0"?>
<xsl:stylesheet>
	<xsl:template>
		<xsl:variable name="prefix" select="$Query/@prefix"/>
		<table border="1" width="50%">
			<tr>
				<td><a href="javascript:void(0)" onclick="window.open(&quot;{$prefix}docedit?id=1&amp;ref=4&quot;, &quot;r_4_1&quot;, &quot;status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, width=800, height=600&quot;)">Документ 1 (JS)</a></td>
				<td><a href="{$prefix}docedit?id=1&amp;ref=4">Документ 1 (просто в новом окне)</a></td>
			</tr>
			<tr>
				<td><a href="javascript:void(0)" onclick="window.open(&quot;{$prefix}docedit?id=2&amp;ref=4&quot;, &quot;r_4_2&quot;, &quot;status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, width=800, height=600&quot;)">Документ 2 (JS)</a></td>
				<td><a href="{$prefix}docedit?id=2&amp;ref=4">Документ 2 (просто в новом окне)</a></td>
			</tr>
			<tr>
				<td><a href="javascript:void(0)" onclick="window.open(&quot;{$prefix}docedit?id=3&amp;ref=4&quot;, &quot;r_4_3&quot;, &quot;status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, width=800, height=600&quot;)">Документ 3 (JS)</a></td>
				<td><a href="{$prefix}docedit?id=3&amp;ref=4">Документ 3 (просто в новом окне)</a></td>
			</tr>
			<tr>
				<td><a href="javascript:void(0)" onclick="window.open(&quot;{$prefix}docedit?id=4&amp;ref=4&amp;SID={$Query/@SID}&quot;, &quot;r_4_4&quot;, &quot;status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, width=800, height=600&quot;)">Документ 4 (JS)</a></td>
				<td><a href="{$prefix}docedit?id=4&amp;ref=4">Документ 4 (просто в новом окне)</a></td>
			</tr>
			<tr>
				<td><a href="javascript:void(0)" onclick="window.open(&quot;{$prefix}docedit?id=5&amp;ref=3&quot;, &quot;r_3_5&quot;, &quot;status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, width=800, height=600&quot;)">Документ 5 (JS)</a></td>
				<td><a href="{$prefix}docedit?id=5&amp;ref=3">Документ 5 (просто в новом окне)</a></td>
			</tr>
		</table>
		
		
		<!--table border="1" width="30%">
			<xsl:for-each select="document">
				<tr>
					<th><xsl:value-of select="@id" /></th>
					<td>
						<xsl:call-template name="docUrl">
							<xsl:with-param name="doc" select="." />
						</xsl:call-template>
					</td>
				</tr>
			</xsl:for-each>
		</table-->
		
		<a href="{$ST//section[@name = 'docedit']/@URL}?id=0&amp;ref=">
			Создать новый документ
		</a>
	</xsl:template>
	
	<xsl:template name="docUrl">
		<a href="javascript:void(0)" onclick="window.open(&quot;{$ST//section[@name = 'docedit']/@URL}?id={$doc/@id}&amp;ref=&quot;, &quot;tehnostroy_photo&quot;, &quot;status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, width=800, height=600&quot;)">
			<xsl:value-of select="$doc/field[@name = 'ttitle']" />
		</a>
		<xsl:text disable-output-escaping="yes"> | </xsl:text>
		<a target="_blank" href="{$ST//section[@name = 'docedit']/@URL}?id={$doc/@id}&amp;ref=">
			<xsl:value-of select="$doc/field[@name = 'ttitle']" />
		</a>

	</xsl:template>	
</xsl:stylesheet>