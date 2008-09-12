<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
	<xsl:output method="xml" encoding="UTF-8" indent="no" />
	<xsl:template match="/document/header"></xsl:template>

	<xsl:template match="title"></xsl:template>

	<xsl:template match="header">
		<h1>
			<xsl:apply-templates />
		</h1>
	</xsl:template>

	<xsl:template match="separator">
		<hr />
	</xsl:template>

	<xsl:template match="/document">
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="/document/content">
		<content>
			<xsl:attribute name="position">
		    <xsl:value-of select="@position" />
		</xsl:attribute>

			<xsl:apply-templates select="section" />
		</content>
	</xsl:template>

	<xsl:template match="section">
		<xsl:if test="title != ''">
			<h2>
				<xsl:value-of select="title" />
			</h2>
		</xsl:if>
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="generator">
		<xsl:for-each
			select="php:function('GeneratorFactory::callGenerator',string(@id))">
			<xsl:apply-templates />
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="p">
		<p>
			<xsl:apply-templates />
		</p>
	</xsl:template>

	<xsl:template match="up">
		<span class="up">
			<xsl:apply-templates />
		</span>
	</xsl:template>

	<xsl:template match="a">
		<a>
			<xsl:attribute name="href">
    			<xsl:value-of select="@href" />
  			</xsl:attribute>
			<xsl:if test="string(@target) != ''">
				<xsl:attribute name="target">
               <xsl:value-of select="@target" />
            </xsl:attribute>
			</xsl:if>
			<xsl:apply-templates />
		</a>
	</xsl:template>

	<xsl:template match="br">
		<br />
	</xsl:template>

	<xsl:template match="span">
		<span>
			<xsl:attribute name="class">
		    	<xsl:value-of select="@class" />
		  	</xsl:attribute>
			<xsl:apply-templates />
		</span>
	</xsl:template>

	<xsl:template match="ul">
		<ul>
			<xsl:apply-templates />
		</ul>
	</xsl:template>

	<xsl:template match="li">
		<li>
			<xsl:apply-templates />
		</li>
	</xsl:template>

	<xsl:template match="table">
		<table>
			<xsl:apply-templates />
		</table>
	</xsl:template>

	<xsl:template match="tr">
		<tr>
			<xsl:apply-templates />
		</tr>
	</xsl:template>

	<xsl:template match="td">
		<td>
			<xsl:if test="string(@colspan) != ''">
				<xsl:attribute name="colspan">
               <xsl:value-of select="@colspan" />
            </xsl:attribute>
			</xsl:if>
			<xsl:apply-templates />
		</td>
	</xsl:template>

	<xsl:template match="img">
		<img>
			<xsl:attribute name="alt">
    			<xsl:value-of select="." />
  			</xsl:attribute>
			<xsl:attribute name="src">
    			<xsl:value-of select="@src" />
  			</xsl:attribute>
			<xsl:if test="string(@class) != ''">
				<xsl:attribute name="class">
               <xsl:value-of select="@class" />
            </xsl:attribute>
			</xsl:if>
		</img>
	</xsl:template>

	<xsl:template match="releases">
		<table>
			<xsl:attribute name="class">
		    	<xsl:value-of select="@class" />
		  	</xsl:attribute>
			<xsl:apply-templates />
		</table>
	</xsl:template>

	<xsl:template match="release">
		<tr>
			<td>
				<img>
					<xsl:attribute name="alt">
		    			<xsl:value-of select="@alt" />
		  			</xsl:attribute>
					<xsl:attribute name="src">
		    			<xsl:value-of select="@image" />
		  			</xsl:attribute>
				</img>
			</td>
			<td>
				<p>
					<xsl:apply-templates />
				</p>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="news">
		<div class="news">
			<h4>
				<xsl:value-of select="@headline" />
			</h4>
			<span class="date">
				<xsl:value-of select="@date" />
			</span>
			<span class="text">
				-
				<xsl:apply-templates />
			</span>
		</div>
	</xsl:template>


	<xsl:template match="links">
		<h3>
			<xsl:value-of select="@name" />
		</h3>
		<xsl:apply-templates select="link" />
	</xsl:template>

	<xsl:template match="link">
		<div class="link">
			<span class="link">
				<a>
					<xsl:attribute name="href">
	    			<xsl:value-of select="@href" />
	  			</xsl:attribute>
					<xsl:attribute name="target">_new</xsl:attribute>
					<xsl:value-of select="@label" />
				</a>
			</span>
			<span class="text">
				-
				<xsl:value-of select="." />
			</span>
		</div>
	</xsl:template>

	<xsl:template match="plain">
		<xsl:call-template name="lf2br">
			<xsl:with-param name="StringToTransform" select="." />
		</xsl:call-template>
	</xsl:template>

	<xsl:template match="html">
		<xsl:copy-of select="node()" />
	</xsl:template>

	<xsl:template name="cp">
		<xsl:copy>
			<xsl:copy-of select="@*" /><!-- alle Attribute und Tags kopieren -->
			<!-- <xsl:apply-templates/> -->
		</xsl:copy>
	</xsl:template>

	<xsl:template name="lf2br">
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
				<xsl:value-of
					select="substring-before($StringToTransform,'&#xA;')" />
				<!-- by putting a 'br' element in the result tree instead  -->
				<!-- of the linefeed character, a <br> will be output at   -->
				<!-- that point in the HTML                                -->
				<br />
				<!-- repeat for the remainder of the original string -->
				<xsl:call-template name="lf2br">
					<xsl:with-param name="StringToTransform">
						<xsl:value-of
							select="substring-after($StringToTransform,'&#xA;')" />
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