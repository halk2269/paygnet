<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="ListFields">
		<xsl:param name="doctype"/>
		<xsl:param name="doc" select="''"/>
		<xsl:param name="gvars"/>
		<xsl:param name="isnew"/>
		<xsl:param name="isrealnew"/>
		<xsl:param name="hidefield" select="''"/>
		<xsl:param name="present" select="'vertical'" />
		<xsl:param name="block" />
		<xsl:param name="blockName" />
		<xsl:choose>
			<xsl:when test="$block > 0 and $blockName != ''">
				<fieldset title="{$blockName}" class="formFieldset">
					<legend>
						<xsl:value-of select="$blockName" />
					</legend>
					<xsl:for-each select="$doctype/field[@block = $block]">
						<xsl:variable name="tmpType" select="@type"/>
						<xsl:variable name="tmpAlias" select="@name"/>
						<xsl:choose>
							<xsl:when test="(@cantedit and not($Visitor/role/@name = 'admin' or $Visitor/role/@name = 'superadmin')) or (@hiddenfromall = 1)"/>
							<xsl:when test="@name = $hidefield and $Visitor/@id"/>
							<xsl:when test="$tmpType = 'int'">
								<xsl:call-template name="tpl_int">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'float'">
								<xsl:call-template name="tpl_float">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'text'">
								<xsl:call-template name="tpl_text">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'select'">
								<xsl:call-template name="tpl_select">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'radio'">
								<xsl:call-template name="tpl_radio">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'multibox'">
								<xsl:call-template name="tpl_multibox">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var"/>
									<xsl:with-param name="isnew" select="$isnew"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'string'">
								<xsl:call-template name="tpl_string">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'bool'">
								<xsl:call-template name="tpl_bool">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'strlist'">
								<xsl:call-template name="tpl_strlist">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'file'">
								<xsl:call-template name="tpl_file">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'password'">
								<xsl:call-template name="tpl_password">
									<xsl:with-param name="view" select="$present" />
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="isrealnew" select="$isrealnew"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'date'">
								<xsl:call-template name="tpl_date">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'datetime'">
								<xsl:call-template name="tpl_datetime">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'image'">
								<xsl:call-template name="tpl_image">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="view" select="$present" />
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'table'">
								<xsl:call-template name="tpl_table">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="isnew" select="$isnew"/>
									<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:when test="$tmpType = 'link'">
								<xsl:call-template name="tpl_link">
									<xsl:with-param name="type" select="."/>
									<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
									<xsl:with-param name="view" select="$present" />
								</xsl:call-template>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</fieldset>
			</xsl:when>
			<xsl:when test="$block = 0">
				<xsl:for-each select="$doctype/field[@block = 0]">
					<xsl:variable name="tmpType" select="@type"/>
					<xsl:variable name="tmpAlias" select="@name"/>
					<xsl:choose>
						<xsl:when test="(@cantedit and not($Visitor/role/@name = 'admin' or $Visitor/role/@name = 'superadmin')) or (@hiddenfromall = 1)"/>
						<xsl:when test="@name = $hidefield and $Visitor/@id"/>
						<xsl:when test="$tmpType = 'int'">
							<xsl:call-template name="tpl_int">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'float'">
							<xsl:call-template name="tpl_float">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'text'">
							<xsl:call-template name="tpl_text">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'select'">
							<xsl:call-template name="tpl_select">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'radio'">
							<xsl:call-template name="tpl_radio">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'multibox'">
							<xsl:call-template name="tpl_multibox">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'string'">
							<xsl:call-template name="tpl_string">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'bool'">
							<xsl:call-template name="tpl_bool">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'strlist'">
							<xsl:call-template name="tpl_strlist">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'file'">
							<xsl:call-template name="tpl_file">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'password'">
							<xsl:call-template name="tpl_password">
								<xsl:with-param name="view" select="$present" />
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isrealnew" select="$isrealnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'date'">
							<xsl:call-template name="tpl_date">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'datetime'">
							<xsl:call-template name="tpl_datetime">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'image'">
							<xsl:call-template name="tpl_image">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="view" select="$present" />
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'table'">
							<xsl:call-template name="tpl_table">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'link'">
							<xsl:call-template name="tpl_link">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<xsl:for-each select="$doctype/field">
					<xsl:variable name="tmpType" select="@type"/>
					<xsl:variable name="tmpAlias" select="@name"/>
					<xsl:choose>
						<xsl:when test="(@cantedit and not($Visitor/role/@name = 'admin' or $Visitor/role/@name = 'superadmin')) or (@hiddenfromall = 1)"/>
						<xsl:when test="@name = $hidefield and $Visitor/@id"/>
						<xsl:when test="$tmpType = 'int'">
							<xsl:call-template name="tpl_int">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'float'">
							<xsl:call-template name="tpl_float">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'text'">
							<xsl:call-template name="tpl_text">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'select'">
							<xsl:call-template name="tpl_select">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'radio'">
							<xsl:call-template name="tpl_radio">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'multibox'">
							<xsl:call-template name="tpl_multibox">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var"/>
								<xsl:with-param name="isnew" select="$isnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'string'">
							<xsl:call-template name="tpl_string">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'bool'">
							<xsl:call-template name="tpl_bool">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'strlist'">
							<xsl:call-template name="tpl_strlist">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'file'">
							<xsl:call-template name="tpl_file">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'password'">
							<xsl:call-template name="tpl_password">
								<xsl:with-param name="view" select="$present" />
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="isrealnew" select="$isrealnew"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'date'">
							<xsl:call-template name="tpl_date">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'datetime'">
							<xsl:call-template name="tpl_datetime">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'image'">
							<xsl:call-template name="tpl_image">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="view" select="$present" />
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'table'">
							<xsl:call-template name="tpl_table">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="isnew" select="$isnew"/>
								<xsl:with-param name="var" select="$gvars/var[@name = $tmpAlias]"/>
							</xsl:call-template>
						</xsl:when>
						<xsl:when test="$tmpType = 'link'">
							<xsl:call-template name="tpl_link">
								<xsl:with-param name="type" select="."/>
								<xsl:with-param name="field" select="$doc/field[@name = $tmpAlias]"/>
								<xsl:with-param name="view" select="$present" />
							</xsl:call-template>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</xsl:otherwise>	
		</xsl:choose>
	</xsl:template>
	<!-- Отображение целых числовых значений -->
	<xsl:template name="tpl_int">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<xsl:choose>
				<!-- если не создание нового документа, то записываем значения по умолчанию -->
				<xsl:when test="$isnew = 1">
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$type}"/>
				</xsl:when>
				<xsl:when test="$isnew = 2">
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$var}"/>
				</xsl:when>
				<xsl:otherwise>
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$field/.}"/>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	<!-- Отображение дробных числовых значений -->
	<xsl:template name="tpl_float">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<xsl:choose>
				<!-- если не создание нового документа, то записываем значения по умолчанию -->
				<xsl:when test="$isnew = 1">
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$type}"/>
				</xsl:when>
				<xsl:when test="$isnew = 2">
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$var}"/>
				</xsl:when>
				<xsl:otherwise>
					<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$field/.}"/>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	<!-- Отображение текста (тип "text") -->
	<xsl:template name="tpl_text">
		<xsl:param name="type" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:variable name="withoutSpaces">
				<xsl:if test="$type/@noSpacesLimit = 1">
					<xsl:value-of select="' знаков без пробелов'"/>
				</xsl:if>
			</xsl:variable>
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@length and not(ancestor::*[2]/disableStringConstr = '1')">
				(максимальная длина: <xsl:value-of select="$type/@length"/>
				<xsl:value-of select="$withoutSpaces"/>)
			</xsl:if>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<br/>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<textarea cols="80" rows="18" id="{$type/@name}" name="{$type/@name}">
							<xsl:attribute name="rows">
								<xsl:choose>
									<xsl:when test="$type/@mode = 'simple'">6</xsl:when>
									<xsl:when test="$type/@mode = 'nl2br'">8</xsl:when>
									<xsl:otherwise>18</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
							<xsl:choose>
								<!-- если не создание нового документа, то записываем значения по умолчанию -->
								<xsl:when test="$isnew = 1">
									<xsl:value-of select="$type" disable-output-escaping="no"/>
								</xsl:when>
								<xsl:when test="$isnew = 2">
									<xsl:value-of select="$var" disable-output-escaping="no"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="$field/." disable-output-escaping="no"/>
								</xsl:otherwise>
							</xsl:choose>
						</textarea>
						<br/>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>
	<!-- Отображение значений типа "select" -->
	<xsl:template name="tpl_select">
		<xsl:param name="type" />	
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<select name="{$type/@name}">
				<xsl:for-each select="$type/item">
					<option value="{@id}">
						<xsl:choose>
							<xsl:when test="@id = $field/@item_id and $isnew = 0">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:when>
							<xsl:when test="../@defaultSelected = @id and $isnew = 1">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:when>
							<xsl:when test="@id = $var and $isnew = 2">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:when>
						</xsl:choose>
						<xsl:value-of select="."/>
					</option>
				</xsl:for-each>
			</select>
			<br/>
		</div>
	</xsl:template>
	<!-- Отображение значений типа "radio" -->
	<xsl:template name="tpl_radio">
		<xsl:param name="type" />	
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<br/><br/>
			<xsl:for-each select="$type/item">
				<input type="radio" name="{$type/@name}" id="{@id}" value="{@id}">
					<xsl:choose>
						<xsl:when test="$isnew = 0 and $field/@item_id = @id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
						<xsl:when test="$isnew = 1 and $type/@defaultChecked = @id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
						<xsl:when test="$isnew = 2 and $var = @id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
					</xsl:choose>
				</input>	
				<label for="{@id}">
					<xsl:value-of select="text()" />
				</label>
				<br/>
			</xsl:for-each>
			<br/>
		</div>
	</xsl:template>
	<!-- Отображение значений типа "multibox" -->
	<xsl:template name="tpl_multibox">
		<xsl:param name="type" />	
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<br/><br/>
			<xsl:for-each select="$type/item">
				<input type="checkbox" name="{$type/@name}_{@item_id}" id="{@item_id}" value="{@item_id}">
					<xsl:choose>
						<xsl:when test="$isnew = 0 and $field/item/@item_id = @item_id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
						<xsl:when test="$isnew = 2 and $var[@name = concat($type/@name, '_', @item_id)] = @item_id">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
					</xsl:choose>
				</input>
				<label for="{@id}">
					<xsl:value-of select="text()" />
				</label>
				<br/>
			</xsl:for-each>
			<br/>
		</div>
	</xsl:template>	
	<!-- Отображение значений строкового типа ("string") -->
	<xsl:template name="tpl_string">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<!-- Родитель родителя для ноды field, 1-ый родитель — нода <doctype>, 2-ой — нода <module> -->
			<xsl:if test="not(ancestor::*[2]/disableStringConstr = '1')">
			(максимальная длина: <xsl:value-of select="$type/@length"/>)
			</xsl:if>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<span class="formFieldString">
				<xsl:choose>
					<!-- если не создание нового документа, то записываем значения по умолчанию -->
					<xsl:when test="$isnew = 1">
						<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$type}"/>
					</xsl:when>
					<xsl:when test="$isnew = 2">
						<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$var}"/>
					</xsl:when>
					<xsl:otherwise>
						<input name="{$type/@name}" type="text" size="20" maxlength="{$type/@length}" value="{$field/.}"/>
					</xsl:otherwise>
				</xsl:choose>
			</span>
		</div>
	</xsl:template>
	<!-- Отображение значений булевского типа -->
	<xsl:template name="tpl_bool">
		<xsl:param name="type" />
		<xsl:param name="isnew" />
		<xsl:param name="field" />
		<xsl:param name="var" />
		<div class="formField">
			<xsl:choose>
				<!-- если не создание нового документа, то записываем значения по умолчанию -->
				<xsl:when test="$isnew = 1">
					<input type="checkbox" name="{$type/@name}" id="{$type/@name}" value="1" class="checkbox">
						<xsl:if test="$type = 1">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</xsl:when>
				<xsl:when test="$isnew = 2">
					<input type="checkbox" name="{$type/@name}" id="{$type/@name}" value="1" class="checkbox">
						<xsl:if test="$var = 1">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</xsl:when>
				<xsl:otherwise>
					<input type="checkbox" name="{$type/@name}" id="{$type/@name}" value="1" class="checkbox">
						<xsl:if test="$field = 1">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</xsl:otherwise>
			</xsl:choose>
			<label for="{$type/@name}">
				<xsl:value-of select="$type/@description"/>
			</label>
			<br/>
		</div>
	</xsl:template>
	<!-- Отображение списка строк ("strlist") -->
	<xsl:template name="tpl_strlist">
		<xsl:param name="type" />
		<xsl:param name="isnew" />
		<xsl:param name="field" />
		<xsl:param name="var" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<br/>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<textarea cols="80" rows="6" name="{$type/@name}">
							<xsl:choose>
								<xsl:when test="$isnew = 0">
									<xsl:for-each select="$field/line">
										<xsl:value-of select="."/>
										<xsl:text>&#13;&#10;</xsl:text>
									</xsl:for-each>
								</xsl:when>
								<xsl:when test="$isnew = 2">
									<xsl:value-of select="$var"/>
								</xsl:when>
							</xsl:choose>
						</textarea>
						<br/>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>
	<!-- Отображение поля для загрузки файла -->
	<xsl:template name="tpl_file">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$isnew = 0 and $field/@file_id != 0">
				[Скачать: <a href="{$field/@URL}">
					<xsl:value-of select="$field/@title"/>
				</a> (<xsl:value-of select="$field/@size"/>)]
			</xsl:if>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<input type="file" name="{$type/@name}"/>
			<br/>
			<xsl:if test="$type/@importance != 1 and $isnew = 0 and $field/@file_id != 0">
				<div class="divInnerCheckBox">
					<input type="checkbox" name="{$type/@name}_delete" id="{$type/@name}_delete"/>
					<label for="{$type/@name}_delete">Удалить файл?</label>
					<br/>
				</div>
			</xsl:if>
		</div>
	</xsl:template>
	<!-- Отображение значений типа datetime -->
	<xsl:template name="tpl_datetime">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:choose>
				<xsl:when test="$type/@show = 'selects'">
					<xsl:value-of select="$type/@description"/>
					<br/>
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/dates"/>
						<xsl:with-param name="add" select="$field/@date"/>
						<xsl:with-param name="default" select="$type/date"/>
					</xsl:call-template>&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/months"/>
						<xsl:with-param name="add" select="$field/@month"/>
						<xsl:with-param name="default" select="$type/month"/>
					</xsl:call-template>&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/years"/>
						<xsl:with-param name="add" select="$field/@year"/>
						<xsl:with-param name="default" select="$type/year"/>
					</xsl:call-template>&#160;&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/hours"/>
						<xsl:with-param name="add" select="$field/@hour"/>
						<xsl:with-param name="default" select="$type/hour"/>
					</xsl:call-template>:
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/minutes"/>
						<xsl:with-param name="add" select="$field/@minute"/>
						<xsl:with-param name="default" select="$type/minute"/>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$type/@description"/> (в формате ГГГГ-ММ-ДД ЧЧ:MM:СС)
					<xsl:call-template name="RepresentValue">
						<xsl:with-param name="type" select="$view" />
					</xsl:call-template>
					<!--xsl:if test="$type/@importance = 1"><span class="star">*</span></xsl:if><br/-->
					<xsl:choose>
						<xsl:when test="$isnew = 1">
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$type}"/>
						</xsl:when>
						<xsl:when test="$isnew = 2">
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$var}"/>
						</xsl:when>
						<xsl:otherwise>
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$field/@value}"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	<!-- Отображение значений типа date -->
	<xsl:template name="tpl_date">
		<xsl:param name="type" />
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:choose>
				<xsl:when test="$type/@show = 'selects'">
					<xsl:value-of select="$type/@description"/>
					<br/>
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/dates"/>
						<xsl:with-param name="add" select="$field/@date"/>
						<xsl:with-param name="default" select="$type/date"/>
					</xsl:call-template>&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/months"/>
						<xsl:with-param name="add" select="$field/@month"/>
						<xsl:with-param name="default" select="$type/month"/>
					</xsl:call-template>&#160;
					<xsl:call-template name="DrawSelect">
						<xsl:with-param name="type" select="$type"/>
						<xsl:with-param name="var" select="$var"/>
						<xsl:with-param name="isnew" select="$isnew"/>
						<xsl:with-param name="node" select="$type/years"/>
						<xsl:with-param name="add" select="$field/@year"/>
						<xsl:with-param name="default" select="$type/year"/>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$type/@description"/> (в формате ГГГГ-ММ-ДД)
					<xsl:call-template name="RepresentValue">
						<xsl:with-param name="type" select="$view" />
					</xsl:call-template>
					<!--xsl:if test="$type/@importance = 1"><span class="star">*</span></xsl:if><br /-->
					<xsl:choose>
						<xsl:when test="$isnew = 1">
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{substring($type, 1, 10)}"/>
						</xsl:when>
						<xsl:when test="$isnew = 2">
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$var}"/>
						</xsl:when>
						<xsl:otherwise>
							<input name="{$type/@name}" type="text" maxlength="19" size="22" value="{$field/@value}"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	<!-- Отображение значений типа password -->
	<xsl:template name="tpl_password">
		<xsl:param name="view" />
		<xsl:param name="type" />
		<xsl:param name="isrealnew" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:choose>
				<xsl:when test="$isrealnew = 1">
					<xsl:if test="$type/@importance = 1">
						<span class="star"> *</span>
					</xsl:if>
					<xsl:call-template name="RepresentValue">
						<xsl:with-param name="type" select="$view" />
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text> (если Вы не хотите изменять пароль, оставьте поле незаполненным)</xsl:text>
					<xsl:call-template name="RepresentValue">
						<xsl:with-param name="type" select="$view" />
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
			<input name="{$type/@name}" type="password" size="20" value=""/>
		</div>
		<div class="formField">
			<xsl:text>Confirm password</xsl:text>
			<xsl:if test="$isrealnew = 1 and $type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<input name="{$type/@name}_passconfirm" type="password" size="20" value="" />
		</div>
	</xsl:template>
	<!-- Отображение поля изображения -->
	<xsl:template name="tpl_image">
		<xsl:param name="type" />	
		<xsl:param name="view" />
		<xsl:param name="isnew" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$isnew = 0 and $field/@file_id != 0">
				<xsl:text>[Скачать: </xsl:text>
				<a href="{$field/@URL}">
					<xsl:value-of select="$field/@title"/>
				</a>
				<xsl:text> (</xsl:text>
				<xsl:value-of select="$field/@size"/>
				<xsl:if test="$field/@width and $isnew = 0 and $field/@file_id != 0">
					<xsl:text>, </xsl:text>
					<xsl:value-of select="$field/@width"/>
					<xsl:text>x</xsl:text>
					<xsl:value-of select="$field/@height"/>
					<xsl:text> пикселей</xsl:text>
				</xsl:if>
				<xsl:text>)]</xsl:text>
			</xsl:if>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<input type="file" name="{$type/@name}"/>
			<br/>
			<xsl:if test="$type/@importance != 1 and $isnew = 0 and $field/@file_id != 0">
				<div class="divInnerCheckBox">
					<input type="checkbox" name="{$type/@name}_delete" id="{$type/@name}_delete"/>
					<label for="{$type/@name}_delete">Удалить файл?</label>
					<br/>
				</div>
			</xsl:if>
		</div>
	</xsl:template>
	
	<xsl:template name="tpl_table">
		<xsl:param name="type" />
		<xsl:param name="field" />
		<xsl:param name="isnew" />
		<xsl:param name="var" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<br/>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<textarea id="tbl_textarea_{$type/@name}" cols="80" rows="12" name="{$type/@name}" class="mono">
							<xsl:choose>
								<xsl:when test="$isnew = 0">
									<xsl:for-each select="$field/table">
										<xsl:call-template name="ShowTable">
											<xsl:with-param name="table" select="."/>
										</xsl:call-template>
										<xsl:if test="position() != last()">
											<xsl:text>&#13;&#10;&#13;&#10;&#13;&#10;</xsl:text>
										</xsl:if>
									</xsl:for-each>
								</xsl:when>
								<xsl:when test="$isnew = 2">
									<xsl:value-of select="$var"/>
								</xsl:when>
							</xsl:choose>
						</textarea>
						<br/>
					</td>
				</tr>
				<tr>
					<td>
						<div style="display: none" id="tbl_inputs_container_{$type/@name}">
							<input type="file" name="excelfile"/>
							<xsl:text> </xsl:text>
							<input id="tbl_file_submit_{$type/@name}" type="submit" value="Загрузить таблицу из Excel-файла" onclick="return tbl_upload_excel_file('{$type/@name}');"/>
							<xsl:text> </xsl:text>
							<span style="white-space: nowrap" id="tbl_informer_{$type/@name}"> </span>
						</div>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				document.getElementById('tbl_inputs_container_<xsl:value-of select="$type/@name"/>').style.display = "block";
			</script>
		</div>
	</xsl:template>
	
	<!-- Шаблон вывода содержимого таблицы через табуляцию -->
	<xsl:template name="ShowTable">
		<xsl:param name="table" />
		<xsl:for-each select="$table/tr">
			<xsl:for-each select="td|th">
				<xsl:value-of select="." disable-output-escaping="yes"/>
				<xsl:if test="position() != last()">
					<xsl:text>&#9;</xsl:text>
				</xsl:if>
				<xsl:choose>
					<xsl:when test="@colspan != 0">
						<xsl:call-template name="RepeatTabString">
							<xsl:with-param name="cnt" select="@colspan - 1"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:for-each>
			<xsl:if test="position() != last()">
				<xsl:text>&#13;&#10;</xsl:text>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="RepeatTabString">
		<xsl:param name="cnt" select="0"/>
		<xsl:param name="isLast" select="0"/>
		<xsl:if test="$cnt != 0">
			<xsl:text>&#9;</xsl:text>
			<xsl:call-template name="RepeatTabString">
				<xsl:with-param name="cnt" select="$cnt - 1"/>
				<xsl:with-param name="isLast" select="$isLast"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	
	<!-- Отображение значений типа "link" -->
	<xsl:template name="tpl_link">
		<xsl:param name="type" />	
		<xsl:param name="view" />
		<xsl:param name="field" />
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<xsl:call-template name="RepresentValue">
				<xsl:with-param name="type" select="$view" />
			</xsl:call-template>
			<select name="{$type/@name}">
				<option value="0">
					<xsl:text>&#160;</xsl:text>
				</option>
				<xsl:for-each select="$type/document">
					<option value="{@id}">
						<xsl:if test="@id = $field/@selectedDocumentID">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="field[@name = $type/@targetDTTitle]"/>
					</option>
				</xsl:for-each>
			</select>
		</div>
	</xsl:template>
	<xsl:template name="DrawSelect">
		<xsl:param name="type"/>
		<xsl:param name="var"/>
		<xsl:param name="isnew"/>
		<xsl:param name="node"/>
		<xsl:param name="add"/>
		<xsl:param name="default"/>
		<xsl:variable name="selectName" select="concat($type/@name, '_', name($node))"/>
		<xsl:variable name="selectVar" select="$SC/module/vars/general/var[@name = $selectName]"/>
		<select name="{$selectName}">
			<xsl:for-each select="$node/item">
				<option value="{@id}">
					<xsl:choose>
						<xsl:when test="@id = $add and $isnew = 0">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:when>
						<xsl:when test="@id = $default and $isnew = 1">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:when>
						<xsl:when test="@id = $selectVar and $isnew = 2">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:when>
					</xsl:choose>
					<xsl:value-of select="."/>
				</option>
			</xsl:for-each>
		</select>
	</xsl:template>
	
	<xsl:template name="RepresentValue">
		<xsl:param name="type" select="'vertical'" />
		<xsl:choose>
			<xsl:when test="$type = 'vertical'">
				<br/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>&#160;&#160;</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<!-- Отображение таблицы ("table") -->
	<!--xsl:template name="tpl_table">
		<div class="formField">
			<xsl:value-of select="$type/@description"/>
			<xsl:if test="$type/@importance = 1">
				<span class="star"> *</span>
			</xsl:if>
			<br/>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<textarea id="tbl_textarea_{$type/@name}" cols="80" rows="12" name="{$type/@name}" class="mono">
							<xsl:choose>
								<xsl:when test="$isnew = 0">
									<xsl:value-of select="$field"/>
								</xsl:when>
								<xsl:when test="$isnew = 2">
									<xsl:value-of select="$var"/>
								</xsl:when>
							</xsl:choose>
						</textarea>
						<br/>
					</td>
				</tr>
				<tr>
					<td>
						<div style="display: none" id="tbl_inputs_container_{$type/@name}">
							<input type="file" name="excelfile" />
							<xsl:text> </xsl:text>
							<input id="tbl_file_submit_{$type/@name}" type="submit" value="Загрузить таблицу из Excel-файла" onclick="return tbl_upload_excel_file('{$type/@name}');" />
							<xsl:text> </xsl:text>
							<span style="white-space: nowrap" id="tbl_informer_{$type/@name}"> </span>
						</div>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				document.getElementById('tbl_inputs_container_<xsl:value-of select="$type/@name" />').style.display = "block";
			</script>
		</div>
	</xsl:template-->
</xsl:stylesheet>
