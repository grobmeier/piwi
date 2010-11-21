<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xhtml="http://www.w3.org/1999/xhtml">
	
	<!-- Output configuration -->
	<xsl:output method="xml" encoding="UTF-8" indent="no" omit-xml-declaration="yes" />
	
    <!-- Only evaluate elements under '/body' (ignore the root) -->
	<xsl:template match="body">
		<content position="main">
      		<xsl:apply-templates/>
		</content>
	</xsl:template>
	
	<!-- Copy everything but remove the namespace -->
	<xsl:template match="*">
	    <xsl:element name="{local-name()}">
	      <xsl:apply-templates select="@*|node()"/>
	    </xsl:element>
	</xsl:template>
	
	<xsl:template match="@*">
	    <xsl:attribute name="{local-name()}">
	      <xsl:value-of select="."/>
	    </xsl:attribute>
	</xsl:template>

</xsl:stylesheet>