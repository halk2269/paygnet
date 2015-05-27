<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<xsl:variable name="doc" select="document"/>
		<xsl:variable name="gvars" select="vars/general"/>
		<xsl:variable name="rInfo" select="rInfo"/>
		<xsl:variable name="doctype" select="doctype"/>
		<xsl:variable name="isnew">
			<xsl:choose>
				<xsl:when test="$SC//vars">2</xsl:when>
				<xsl:when test="not(document)">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="isrealnew">
			<xsl:choose>
				<xsl:when test="not($Visitor/@id)">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="isDocSaved" select="info/item[@name = 'DocWasSaved'] and not(error/item)"/>
		<xsl:if test="$isDocSaved">
			<strong>
				<xsl:text>Your profile has been saved!</xsl:text>
			</strong>
		</xsl:if>
		<xsl:if test="error/item">
			<div id="divPopupErrors">
				<div class="divListErrorsHead">Error is happen while creating (editing) profile</div>
				<xsl:call-template name="DTErrors"/>
			</div>
			<br/>
		</xsl:if>
		<xsl:if test="not($isDocSaved)">
			<form enctype="multipart/form-data" action="{$prefix}" method="post">
				<input type="hidden" name="writemodule" value="Profile"/>
				<input type="hidden" name="ref" value="{@id}"/>
				<input type="hidden" name="errpath" value="{$ST//section[@name = 'profile']/@URL}"/>
				<xsl:if test="$Query/param[@name = 'retpath']">
					<input type="hidden" name="retpath" value="{$Query/param[@name = 'retpath']}"/>
				</xsl:if>
				<div>
					<span class="star">
						<xsl:text>*</xsl:text>
					</span>
					<xsl:text> &#8212; this fields have to be filled</xsl:text>
					<br/>
				</div>
				<br/>
				<xsl:call-template name="ListFields">
					<xsl:with-param name="doctype" select="$doctype"/>
					<xsl:with-param name="doc" select="$doc"/>
					<xsl:with-param name="gvars" select="$gvars"/>
					<xsl:with-param name="isnew" select="$isnew"/>
					<xsl:with-param name="isrealnew" select="$isrealnew"/>
				</xsl:call-template>
				<br/>
				<p class="center">
					<xsl:choose>
						<xsl:when test="$Visitor/@id">
							<input type="submit" class="submit" value="Save"/>
						</xsl:when>
						<xsl:otherwise>
							<input type="submit" class="submit" value="Register"/>
						</xsl:otherwise>
					</xsl:choose>
				</p>
			</form>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
