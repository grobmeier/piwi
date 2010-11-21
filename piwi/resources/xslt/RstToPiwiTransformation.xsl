<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
        
	<!-- Output configuration -->
	<xsl:output method="xml" encoding="UTF-8" indent="no" omit-xml-declaration="yes" />
	
    <!-- Only evaluate elements under '/body' (ignore the root) -->
    <xsl:template match="div[@class='body']">
	    <content position="main">
	    	<xsl:copy-of select="node()"></xsl:copy-of>
	    </content>
    </xsl:template>
</xsl:stylesheet>
