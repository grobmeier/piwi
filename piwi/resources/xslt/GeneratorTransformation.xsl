<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl" xmlns:piwixml="http://piwi.googlecode.com/xsd/piwixml" exclude-result-prefixes="php">
	<xsl:output method="xml" encoding="UTF-8" indent="no" />

   <!-- Copy everything that is not a generator or a form -->
   <xsl:template match="*">
      <xsl:copy>
         <xsl:copy-of select="@*"/>
         <xsl:apply-templates/>
      </xsl:copy>
   </xsl:template>
   
   <!-- Execute generators -->
   <xsl:template match="piwixml:generator">
      <xsl:for-each
         select="php:function('GeneratorFactory::callGenerator', string(@id))">
         <xsl:copy-of select="." />
      </xsl:for-each>
   </xsl:template>
   
   <!-- Evaluate forms -->
   <xsl:template match="piwixml:piwiform">
      <xsl:for-each
         select="php:function('FormProcessor::process', string(@path))">
         <xsl:copy-of select="." />
      </xsl:for-each>
   </xsl:template>
</xsl:stylesheet>