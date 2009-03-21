<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
   
	<!-- Output configuration -->
  	<xsl:output method="html" encoding="UTF-8" indent="no" omit-xml-declaration="yes" />
	
   <xsl:template match="content">
		<xsl:apply-templates />
	</xsl:template>
	
	<xsl:template match="head">
		<xsl:apply-templates />
	</xsl:template>
	
	<xsl:template match="/">
		<xsl:apply-templates />
	</xsl:template>
	
	<xsl:template match="p">
		\n\n <xsl:apply-templates />
	</xsl:template>
	
</xsl:stylesheet>