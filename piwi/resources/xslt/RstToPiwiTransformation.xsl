<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" encoding="UTF-8" indent="no" omit-xml-declaration="yes" />

   <!-- Copy everything that is not a generator or a form -->
	<xsl:template match="*">
		<content position="main">
		<xsl:copy-of select="node()"></xsl:copy-of>
		</content>
	</xsl:template>

	<xsl:template match="head">
	</xsl:template>
</xsl:stylesheet>