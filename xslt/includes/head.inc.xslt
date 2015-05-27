<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="htmlHead">
		<xsl:param name="WithJS" select="true()"/>
		<title>
			<xsl:choose>
				<xsl:when test="$SCT/meta[@name = 'title']">
					<xsl:value-of select="$SCT/meta[@name = 'title']"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$SCT/@title"/>
					<xsl:text> &#8212; </xsl:text>
					<xsl:value-of select="$projectTitle"/>
				</xsl:otherwise>
			</xsl:choose>
		</title>
		<xsl:if test="$SCT/ancestor-or-self::section[meta[@name = 'keywords']]">
			<meta name="keywords" content="{$SCT/ancestor-or-self::section[meta[@name = 'keywords']][1]/meta[@name = 'keywords']}"/>
		</xsl:if>
		<xsl:if test="$SCT/ancestor-or-self::section[meta[@name = 'description']]">
			<meta name="description" content="{$SCT/ancestor-or-self::section[meta[@name = 'description']][1]/meta[@name = 'description']}"/>
		</xsl:if>
		<base href="http://{$Query/@host}{$prefix}"/>
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<link rel="stylesheet" href="{$prefix}css.css" type="text/css"/>
		<xsl:if test="$WithJS">
			<script type="text/javascript">
				function openEditWindow(url, dt, id) {
					$pos = (window.opera) ? ", left=100, top=100" : "";
					nw = window.open(url, "<xsl:value-of select="$projectName"/>_" + dt + id, "status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, width=800, height=600" + $pos);
					nw.focus();
					return false;
				}
				function showImage(url, w, h) {
					href = "<xsl:value-of select="$prefix"/>show/?url=" + url;
	    			w += 20;
	    			h += 10;
	    			sh = screen.height - 80;
	    			if (document.all) sh -= 40;
	    			sw = screen.width;
	    			if (h &gt; sh) h = sh;
	    			if (w &gt; sw) w = sw;
	    			posX = sw/2 - w/2;
	    			posY = sh/2 - h/2;
	    			if (posY &lt; 0) posY = 0;
	    			if (posX &lt; 0) posX = 0;
	    			posCode = (document.all) ? ",left=" + posX + ",top=" + posY : ",screenX=" + posX + ",screenY=" + posY;
	    			//alert("w=" + w + "\nh=" + h + "\nposX=" + posX + "\nposY=" + posY);
	    			moreWin = window.open (href, "<xsl:value-of select="$projectName"/>", "status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, width=" + w + ", height=" + h + posCode);
	    			moreWin.focus();
	  			}
	  			
				function popupWindow(url, w, h) {
	            	href = url;
	            	sh = screen.height - 80;
	            	if (document.all) sh -= 40;
	            	sw = screen.width;
	            	if (h &gt; sh) h = sh;
	            	if (w &gt; sw) w = sw;
	            	posX = sw/2 - w/2;
	            	posY = sh/2 - h/2;
	            	if (posY &lt; 0) posY = 0;
	            	if (posX &lt; 0) posX = 0;
	            	posCode = (document.all) ? ",left=" + posX + ",top=" + posY : ",screenX=" + posX + ",screenY=" + posY;
					moreWin = window.open (href, "idm_popup", "status=no, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, width=" + w + ", height=" + h + posCode);
	            	moreWin.focus();
	          	}  			
	  			
				<xsl:if test="//refEdit">
					var elements = [];
					function changeElement(element) {
						if (element.checked == true) {
							addElement(element.name);
						}
						else {
							deleteElement(element.name);
						}
					}
					function addElement(name) {
						elements[elements.length] = name;
					}
					function deleteElement(name) {
						var check = false;
						var elLength = elements.length;
		
						//search for element
						for (i=0; i &lt; elLength; i++ ) {
							if (elements[i] == name) {
								check = true;
							}
							if (check &amp; (i &lt; (elLength-1))) {
								elements[i] = elements[i+1];
							}
						}
						elements.length = elLength - 1;
					}
					function moveElements(destination, formnumber) {
						var form = document.getElementById('moveDocForm' + formnumber);
						
						var reg = new RegExp('el_ref_([0-9]+)_([0-9]+)');
						var newelem = [];
						var newel, i, j = 0;
						for (i=0; i &lt; elements.length; i++) {
							newel = reg.exec(elements[i]);
							if (newel[1] == formnumber) {
								newelem[j] = newel[2];
								j++;
							}
						}
						
						if (elements.length != 0 &amp; destination.value != 'no') {
							form.elements.elemToMove.value = newelem;	
							form.submit();
						} else {
							alert('Вы не выбрали ни одного документа для переноса. Проставьте галочки напротив тех документов, которые хотите перенести');
							form.elements.newRef.selectedIndex = 0;
						}
					} 
				</xsl:if>
			</script>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
