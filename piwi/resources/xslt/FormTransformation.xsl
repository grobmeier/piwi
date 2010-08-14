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
   <xsl:choose>   
      <xsl:when test="@type = 'submit' or @type = 'reset'">
         <xsl:copy-of select="." />
      </xsl:when>
      <xsl:otherwise>
         <xsl:copy-of select="php:function('FormProcessorWrapper::generateInput', .)"/>
      </xsl:otherwise>
      </xsl:choose>      
   </xsl:template>

   <xsl:template match="select">
      <xsl:copy-of select="php:function('FormProcessorWrapper::generateSelect', .)"/>
   </xsl:template>
   
   <xsl:template match="textarea">
      <xsl:copy-of select="php:function('FormProcessorWrapper::generateTextArea', .)"/>
   </xsl:template>
   
   <!-- Executes a Validator -->
   <xsl:template match="validator">
      <xsl:copy-of select="php:function('FormProcessorWrapper::executeValidator', .)"/>
   </xsl:template>
   
   <!-- Executes a StepProcessor -->
   <xsl:template match="stepprocessor">
      <xsl:copy-of select="php:function('FormProcessorWrapper::executeStepProcessor', .)"/>
   </xsl:template>
</xsl:stylesheet>