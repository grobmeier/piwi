<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl"
	exclude-result-prefixes="php">
	<xsl:output method="xml" encoding="UTF-8" indent="no" />
	
	<xsl:template match="/content">
		<xsl:apply-templates />
	</xsl:template>
	
	<xsl:template match="*">
		<xsl:copy>
			<xsl:copy-of select="@*" />
			<xsl:apply-templates />			
		</xsl:copy>
	</xsl:template>
   
   <!-- DIVs must be handled separetly, otherwise a not allowed 'xmlns' could occur  -->
   <xsl:template match="div">
	   <div>
		   <xsl:copy-of select="@*" />
		   <xsl:apply-templates />
	   </div>
   </xsl:template>
   
   <!-- FORMs must be handled separetly, otherwise a not allowed 'xmlns' could occur  -->
   <xsl:template match="form">
      <form>
         <xsl:copy-of select="@*" />
         <xsl:apply-templates />
      </form>
   </xsl:template>  
   
   <!-- Piwi Elements -->
	<xsl:template match="header">
		<h1>
			<xsl:apply-templates />
		</h1>
	</xsl:template>
	
	<xsl:template match="title">
		<h2>
			<xsl:apply-templates />
		</h2>
	</xsl:template>

	<xsl:template match="section">
		<xsl:apply-templates />
	</xsl:template>
	
	<xsl:template match="separator">
		<hr />
	</xsl:template>
	
	<xsl:template match="plain">
		<xsl:call-template name="linefeedToBr">
			<xsl:with-param name="StringToTransform" select="." />
		</xsl:call-template>
	</xsl:template>
	
	<xsl:template name="linefeedToBr">
      <!-- import $StringToTransform -->
		<xsl:param name="StringToTransform" />
		<xsl:choose>
         <!-- string contains linefeed -->
			<xsl:when test="contains($StringToTransform,'&#xA;')">
            <!-- output substring that comes before the first linefeed -->
            <!-- note: use of substring-before() function means        -->
            <!-- $StringToTransform will be treated as a string,       -->
            <!-- even if it is a node-set or result tree fragment.     -->
            <!-- So hopefully $StringToTransform is really a string!   -->
				<xsl:value-of select="substring-before($StringToTransform,'&#xA;')" />
            <!-- by putting a 'br' element in the result tree instead  -->
            <!-- of the linefeed character, a <br> will be output at   -->
            <!-- that point in the HTML                                -->
				<br />
            <!-- repeat for the remainder of the original string -->
				<xsl:call-template name="lf2br">
					<xsl:with-param name="StringToTransform">
						<xsl:value-of select="substring-after($StringToTransform,'&#xA;')" />
					</xsl:with-param>
				</xsl:call-template>
			</xsl:when>
         <!-- string does not contain newline, so just output it -->
			<xsl:otherwise>
				<xsl:value-of select="$StringToTransform" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>