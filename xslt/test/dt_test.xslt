<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template>
		<p>
			<a href="{$SCT/@URL}">
				<xsl:text>К основному списку</xsl:text>
			</a>
		</p>
		<p class="leftalign">
			<xsl:call-template name="adminEditDelText"/>
		</p>
		<p class="leftalign">
			<style type="text/css">
				<xsl:text>
					table.test {border-left: 1px solid #555; border-top: 1px solid #555;}
					table.test td, table.test th {border-right: 1px solid #555; border-bottom: 1px solid #555;}
					table.test th {background: #E0E7EB; font-weight: bold}
				</xsl:text>
			</style>
			<xsl:for-each select="field[@name = 'ttable']/table">
				<table cellpadding="4" cellspacing="0" class="test">
					<xsl:for-each select="*">
						<xsl:copy-of select="."/>
					</xsl:for-each>
				</table>
			</xsl:for-each>
			<br/>
			<br/>
			<xsl:if test="field[@name = 'timage']/@URL">
				<img src="{field[@name = 'timage']/@URL}" width="{field[@name = 'timage']/@width}" height="{field[@name = 'timage']/@height}" alt="{field[@name = 'timage']/@description}"/>
				<br/>
				<br/>
			</xsl:if>
			<xsl:if test="field[@name = 'tfile']/@URL">
				<a href="{field[@name = 'tfile']/@URL}">
					<xsl:value-of select="field[@name = 'tfile']/@title"/>
				</a>
				<br/>
				<br/>
			</xsl:if>
			<xsl:for-each select="field[@name = 'ttable_file']/table">
				<table cellpadding="4" cellspacing="0" class="test">
					<xsl:for-each select="*">
						<xsl:copy-of select="."/>
					</xsl:for-each>
				</table>
				<div>
					<br/>
				</div>
			</xsl:for-each>
		</p>
		<p>Дочерние:</p>
		<xsl:for-each select="field[@name = 'tarray'][1]">
			<xsl:call-template name="adminCreate"/>
		</xsl:for-each>
		<xsl:for-each select="field[@name = 'tarray']/subdoc">
			<p>
				<b>
					<xsl:value-of select="@id"/>
				</b>:
			<xsl:text> </xsl:text>
				<xsl:value-of select="field[@name = 'str']"/>
				<xsl:if test="@enabled = 0"> [Отключен]</xsl:if>
				<xsl:call-template name="adminEditDel"/>
			</p>
		</xsl:for-each>
		<script type="text/javascript">
		var submitButton;
		var uploadForm;
		var iframe;
		var canupload = false;
		function iframeLoaded() {
			if (!canupload) return;
			//alert(iframe.document.body.innerHTML);
			var q = iframe.document.getElementById("errcode");
			if (!q) return;
			document.getElementById("errcode").innerHTML = "Code: " + q.innerHTML;
			var q = iframe.document.getElementById("result");
			if (!q) return;
			document.getElementById("result").value = q.value;
			//document.getElementById("result").value = iframe.document.body.innerHTML;
			
			
			
			var inputt = document.getElementById("inputt");
			var inputtp = inputt.parentNode;
			inputtp.removeChild(inputt);
			var file;
			if (navigator.appName == "Microsoft Internet Explorer") {
	            file = document.createElement('<input id="inputt" type="file" name="excelfile"/>');
	        } else {
	        	file = document.createElement("input");
	        	file.setAttribute("type", "file");
	        	file.setAttribute("name", "excelfile");
	        	file.setAttribute("id", "inputt");
	        }
	        inputtp.insertBefore(file, submitButton);
	        
		}
		function uploadSubmit() {
			if (!canupload) return false;
			uploadForm.submit();
			return false;
		}
	</script>
		<span id="errcode">Code: </span>
		<br/>
		<textarea id="result" cols="60" rows="8"/>
		<br/>
		<form enctype="multipart/form-data" action="{$prefix}excel-tables/" target="uploadBuffer" method="post" id="uploadForm">
			<input id="inputt" type="file" name="excelfile"/>
			<input id="btnSubmit" type="submit" value="Go!" onclick="return uploadSubmit();"/>
		</form>
		<iframe name="uploadBuffer" src="about:blank" onload="iframeLoaded()" style="position:absolute; visibility:hidden; width:0px; height:0px;"/>
		<script type="text/javascript">
		submitButton = document.getElementById("btnSubmit");
		uploadForm = document.getElementById("uploadForm");
		iframe = window.frames["uploadBuffer"];
		canupload = true;
	</script>
	</xsl:template>
</xsl:stylesheet>
