<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="DisplayContent">
		<div class="content">
    		<div class="content_resize">
      			<div class="mainbar">
        			<div class="article">
						<xsl:apply-templates />
        			</div>
        			<div class="article">
          				<xsl:text></xsl:text>
        			</div>
      			</div>
      			<div class="sidebar">
	        		<div class="gadget">
	          			<h2 class="star"><span>Main</span> Sections</h2>
	          			<xsl:call-template name="GenMenu">
							<xsl:with-param name="root" select="$home" />
						</xsl:call-template>
	        		</div>
	        		<div class="gadget">
	        			<xsl:choose>
	        				<xsl:when test="$SR[@name = 'events']">
	        					<xsl:call-template name="NewsBlock">
									<xsl:with-param name="path" select="$SC/module[@name = 'newsBlock']" />
								</xsl:call-template>
	        				</xsl:when>
	        				<xsl:when test="$SR[@name = 'news']">
	        					<xsl:call-template name="EventBlock">
									<xsl:with-param name="path" select="$SC/module[@name = 'eventBlock']" />
								</xsl:call-template>
	        				</xsl:when>
	        				<xsl:otherwise>
	        					<xsl:call-template name="EventBlock">
									<xsl:with-param name="path" select="$SC/module[@name = 'eventBlock']" />
								</xsl:call-template>
	        				</xsl:otherwise>
	        			</xsl:choose>
	          		</div>
	      		</div>
	      		<div class="clr"></div>
    		</div>
		</div>
	
	  	<div class="fbg">
	    	<div class="fbg_resize">
	      		<div class="col c1">
	        		<h2><span>Event Gallery</span></h2>
	        		<a href="#"><img src="{$prefix}img/pix1.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix2.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix3.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix4.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix5.jpg" width="56" height="56" alt="Event Photo" /></a>
	        		<a href="#"><img src="{$prefix}img/pix6.jpg" width="56" height="56" alt="Event Photo" /></a>
	      		</div>
	      		<div class="col c2">
	        		<xsl:call-template name="TeamBlock">
						<xsl:with-param name="path" select="$SC/module[@name = 'teamBlock']" />
					</xsl:call-template>
	      		</div>
	      		<div class="col c3">
	      			<xsl:value-of select="$SC/module[@name = 'about']/document/field[@name = 'text']" disable-output-escaping="yes" />
	      			<a href="{$ST//section[@name = 'about']/@URL}">Learn more...</a>  		
	      		</div>
	      		<div class="clr"></div>
	    	</div>
	  	</div>
	</xsl:template>
</xsl:stylesheet>		