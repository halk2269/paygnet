<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="authorizationBox">
		<xsl:choose>
			<xsl:when test="$Visitor/@id">
				<div style="float: right;">
					<xsl:text>Hello, </xsl:text>
					<xsl:value-of select="$Visitor/@login"/>
					<xsl:text> (</xsl:text>
					<a href="{$prefix}profile/">Your profile</a>
					<xsl:text> | </xsl:text>
					<a href="{$prefix}?writemodule=Authorize&amp;logoff=1">Sign Out</a>
					<xsl:text>)</xsl:text>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<script type="text/javascript">
					document.getElementById("selectfirst").select();
					document.getElementById("selectfirst").focus();
				</script>
				<form method="post" action="{$prefix}" style="margin-bottom: 5px;">
					<input type="hidden" name="writemodule" value="Authorize"/>
					<input type="hidden" name="ref" value="29"/>
					<input type="hidden" name="retpath" value="{$Query/@retpathPost}"/>
					<input type="hidden" name="errpath" value="{$ST//section[@name = 'login']/@URL}"/>
					<table cellpadding="0" cellspacing="5" border="0">
						<tr>
							<td>
								<xsl:text>Login</xsl:text>
								<br/>
								<input id="selectfirst" type="text" name="login" size="15" maxlenght="50" value=""/>
							</td>
							<td>
								<xsl:text>Password</xsl:text>
								<br/>
								<input type="password" name="pass" size="15" maxlenght="50" value=""/>
							</td>
							<td valign="bottom" style="padding-bottom: 1px;">
								<input type="submit" value="Sign In"/>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:text>Sign Up</xsl:text>
							</td>
							<td valign="bottom" style="padding-bottom: 1px;">
								<a href="{$ST//section[@name = 'official-reg']/@URL}">Officail representatives</a>
							</td>
							<td valign="bottom" style="padding-bottom: 1px;">
								<a href="{$ST//section[@name = 'team-member-reg']/@URL}">Team members</a>
							</td>
						</tr>
					</table>
				</form>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
