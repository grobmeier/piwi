<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

   <!-- Custom Elements -->
   <xsl:template match="release">
      <div style="border: 1px solid #dcdcdc">
         <xsl:apply-templates />
      </div>
   </xsl:template>
   
   <xsl:template match="artist">
      <span style="font-size: 1.1em; color: #cc0000">
         <xsl:apply-templates />
      </span>
   </xsl:template>
   
   <xsl:template match="album">
      <span style="color: #0000cc">
         <xsl:apply-templates />
      </span>
   </xsl:template>   
</xsl:stylesheet>