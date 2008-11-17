<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
	<!-- Piwi Elements -->
   <xsl:include href="PiwiXML.xsl"/>
   
	<!-- Custom transformation -->
	<xsl:include href="xsltsss://customStyleSheet"/>
	
   <!-- Output configuration -->
	<xsl:output method="xml" encoding="UTF-8" indent="no" omit-xml-declaration="yes" />
	
	<!-- Only evaluate elements under '/content' (ignore the root) -->
	<xsl:template match="/content">
		<xsl:apply-templates />
	</xsl:template>
	
	<!-- Copy everything that is not a specific piwixml tag -->
	<xsl:template match="*">
		<xsl:copy>
			<xsl:copy-of select="@*" />
			<xsl:apply-templates />			
		</xsl:copy>
	</xsl:template>
</xsl:stylesheet>