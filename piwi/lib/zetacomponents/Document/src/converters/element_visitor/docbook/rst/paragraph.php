<?php
/**
 * File containing the ezcDocumentDocbookToRstParagraphHandler class.
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @package Document
 * @version //autogen//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

/**
 * Visit paragraphs
 *
 * Visit docbook paragraphs and transform them into HTML paragraphs.
 *
 * @package Document
 * @version //autogen//
 */
class ezcDocumentDocbookToRstParagraphHandler extends ezcDocumentDocbookToRstBaseHandler
{
    /**
     * Handle a node
     *
     * Handle / transform a given node, and return the result of the
     * conversion.
     *
     * @param ezcDocumentElementVisitorConverter $converter
     * @param DOMElement $node
     * @param mixed $root
     * @return mixed
     */
    public function handle( ezcDocumentElementVisitorConverter $converter, DOMElement $node, $root )
    {
        // Find all anachors in paragraph, create pre paragraph RST anchors out
        // of them and remove them from the paragraph.
        $anchors = $node->getElementsByTagName( 'anchor' );
        $foundAnchors = false;
        foreach ( $anchors as $anchor )
        {
            $root .= '.. _' . $anchor->getAttribute( 'ID' ) . ":\n";
            $anchor->parentNode->removeChild( $anchor );
            $foundAnchors = true;
        }

        $root .= ( $foundAnchors ? "\n" : '' );

        // Visit paragraph contents
        $contents = $converter->visitChildren( $node, '' );

        // Remove all line breaks inside the paragraph.
        $contents = trim( preg_replace( '(\s+)', ' ', $contents ) );
        $root .= ezcDocumentDocbookToRstConverter::wordWrap( $contents ) . "\n\n";

        $root = $converter->finishParagraph( $root );

        return $root;
    }
}

?>
