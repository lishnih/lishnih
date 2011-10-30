<?xml version="1.0" encoding="UTF-8" ?>

<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns="http://www.w3.org/1999/xhtml">
  <xsl:output method="xml" indent="yes"
    doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
    doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" />


  <!-- XHTML document outline -->
  <xsl:template match="/">
    <html>
      <head>
        <title>
          <xsl:value-of select="//TITLE" />
        </title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <xsl:apply-templates select="//STYLE" />
        <xsl:apply-templates select="//style" />
        <xsl:apply-templates select="//SCRIPT" />
        <xsl:apply-templates select="//script" />
      </head>
      <body>
        <xsl:apply-templates select="//body" />

        <!-- Div for debugging -->
        <xsl:element name="div">
          <xsl:attribute name="class">
            <xsl:text>debug</xsl:text>
          </xsl:attribute>
        </xsl:element>

      </body>
    </html>
  </xsl:template>


  <!-- STYLE outline -->
  <xsl:template match="//STYLE">
    <link type="text/css" rel="stylesheet">
      <xsl:attribute name="href">
        <xsl:text>css/</xsl:text>
        <xsl:value-of select="@href" />
      </xsl:attribute>
    </link>
  </xsl:template>


  <!-- style inline -->
  <xsl:template match="//style">
    <style>
      <xsl:apply-templates />
    </style>
  </xsl:template>


  <!-- SCRIPT outline -->
  <xsl:template match="//SCRIPT">
    <script type="text/javascript">
      <xsl:attribute name="src">
        <xsl:text>js/</xsl:text>
        <xsl:value-of select="@src" />
      </xsl:attribute>
    </script>
  </xsl:template>


  <!-- script inline -->
  <xsl:template match="//script">
    <script type="text/javascript">
      <xsl:apply-templates />
    </script>
  </xsl:template>


  <!-- body inline -->
  <xsl:template match="//body">
    <xsl:value-of select="." disable-output-escaping="yes" />
  </xsl:template>


</xsl:stylesheet>
