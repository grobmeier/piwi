<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
	<xsl:output method="xml" encoding="UTF-8" indent="no" omit-xml-declaration="yes" />

   <xsl:template match="/step">
      <xsl:apply-templates />
   </xsl:template>
   
   <!-- Copy everything that is not a form field -->
   <xsl:template match="*">
      <xsl:copy>
         <xsl:copy-of select="@*"/>
         <xsl:apply-templates/>
      </xsl:copy>
   </xsl:template>
   
   <xsl:template match="input">
      <xsl:for-each
         select="php:function('FormProcessor::generateInput', .)">
         <xsl:copy-of select="." />
      </xsl:for-each>
   </xsl:template>

   <xsl:template match="select">
      <xsl:for-each
         select="php:function('FormProcessor::generateSelect', .)">
         <xsl:copy-of select="." />
      </xsl:for-each>
   </xsl:template>
   
   <xsl:template match="textarea">
      <xsl:for-each
         select="php:function('FormProcessor::generateTextArea', .)">
         <xsl:copy-of select="." />
      </xsl:for-each>
   </xsl:template>
   
   <xsl:template match="validator">
      <xsl:for-each
         select="php:function('FormProcessor::executeValidator', .)">
         <xsl:copy-of select="." />
      </xsl:for-each>
   </xsl:template>
   
   <xsl:template match="stepprocessor">
      <xsl:for-each
         select="php:function('FormProcessor::executeStepProcessor', .)">
         <xsl:copy-of select="." />
      </xsl:for-each>
   </xsl:template>
</xsl:stylesheet>