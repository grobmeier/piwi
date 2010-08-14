<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:php="http://php.net/xsl" xmlns:piwixml="http://piwi.googlecode.com/xsd/piwixml" exclude-result-prefixes="php">
	<xsl:output method="xml" encoding="UTF-8" indent="no" omit-xml-declaration="yes" />

   <!-- Copy everything that is not a generator or a form -->
   <xsl:template match="*">
      <xsl:copy>
         <xsl:copy-of select="@*"/>
         <xsl:apply-templates/>
      </xsl:copy>
   </xsl:template>

   <!-- Execute generators -->
   <xsl:template match="piwixml:generator">
      <xsl:apply-templates select="php:function('GeneratorFactory::callGenerator', string(@id))" />
   </xsl:template>
   
   <!-- Evaluate forms -->
   <xsl:template match="piwixml:piwiform">
      <xsl:apply-templates select="php:function('FormProcessorWrapper::process', string(@id))" />
   </xsl:template>
   
   <!-- Translate the labels -->
   <xsl:template match="piwixml:label">
      <xsl:choose>   
      <xsl:when test="@key != ''">
         <xsl:value-of select="php:function('ResourcesManager::getLabelText', string(@key))"/>
      </xsl:when>
      <xsl:otherwise>
      <xsl:copy>
         <xsl:copy-of select="@*"/>
         <xsl:apply-templates/>
      </xsl:copy>
      </xsl:otherwise>
      </xsl:choose>      
   </xsl:template>
</xsl:stylesheet>