<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
	<xsl:output method="xml" encoding="UTF-8" indent="no" />

   <xsl:template match="*">
      <xsl:copy>
         <xsl:copy-of select="@*"/>
         <xsl:apply-templates/>
      </xsl:copy>
   </xsl:template>
   
   <xsl:template match="generator">
      <xsl:for-each
         select="php:function('GeneratorFactory::callGenerator',string(@id))">
         <xsl:copy-of select="." />
      </xsl:for-each>
   </xsl:template>
</xsl:stylesheet>