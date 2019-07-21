<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Formatters;

use DOMDocument,
    DOMElement,
    DOMNode,
    DOMText,
    NGSOFT\Tools\Interfaces\StyleSheetInterface;
use function mb_convert_encoding;

class TagFormatter extends PlainTextFormatter {

    /**
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Block-level_elements
     * @var array<string>
     */
    protected $blocktags = [
        "address", "article", "aside", "blockquote", "details",
        "dialog", "dd", "div", "dl", "dt", "fieldset", "figcation",
        "figure", "footer", "form", "h1", "h2", "h3", "h4", "h5", "h6",
        "header", "hgroup", "hr", "li", "main", "nav", "ol", "p", "pre",
        "section", "table", "ul"
    ];

    /** {@inheritdoc} */
    public function format(string $message): string {
        if ($this->stylesheet and $message !== strip_tags($message)) $message = $this->parseStyles($message);
        return parent::format($message);
    }

    protected function getInnerHTML(DOMNode $node): string {
        $html = "";
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }
        return $html;
    }

    protected function getOuterHTML(DOMNode $e) {
        $doc = new DOMDocument();
        $doc->appendChild($doc->importNode($e, true));
        return $doc->saveHTML();
    }

    /**
     * Parse Tags
     * @param DOMNode $body
     * @param StyleSheetInterface $stylesheet
     * @return string
     */
    protected function parseNodes(DOMNode $body, StyleSheetInterface $stylesheet) {
        $result = "";

        foreach ($body->childNodes as $node) {

            if ($node instanceof DOMText) {
                $result .= ltrim($node->nodeValue);
            } elseif ($node instanceof DOMElement) {
                $tag = strtolower($node->tagName);
                if ($stylesheet->hasStyle($tag)) {
                    $result .= $stylesheet->getStyle($tag)->applyTo($this->parseNodes($node, $stylesheet));
                } else {

                    switch ($tag) {
                        case "br":
                            $result .= PHP_EOL;
                            break;
                        case "hr":
                            $result .= PHP_EOL . str_repeat('-', 64) . PHP_EOL;
                            break;
                        case "rect":
                            $prefix = $suffix = ""; $matches = [];
                            if ($classList = $node->getAttribute("class") and preg_match_all('/(\w+)(?:\s+)?/', $classList, $matches)) {
                                foreach ($matches[1] as $class) {
                                    if ($stylesheet->hasStyle($class)) {
                                        $suffix .= $stylesheet->getStyle($class)->getSuffix();
                                        $prefix .= $stylesheet->getStyle($class)->getPrefix();
                                    }
                                }
                            }
                            if ($w = $node->getAttribute("width") and preg_match('/^\d+$/', $w)) $width = intval($w);
                            else $width = 64;
                            $message = $this->parseNodes($node, $stylesheet);
                            $len = mb_strlen($message);
                            if ($width > $len) {
                                $repeats = (int) ceil(($width - $len) / 2);
                                $message = str_repeat(" ", $repeats) . $message . str_repeat(" ", $repeats);
                                $len = mb_strlen($message);
                            }

                            $result .= PHP_EOL . $prefix;
                            $result .= "{:\t\t:}" . str_repeat(" ", $len + 8) . PHP_EOL;
                            $result .= "{:\t\t:}{:space*4:}" . $message . "{:space*4:}" . PHP_EOL;
                            $result .= "{:\t\t:}" . str_repeat(" ", $len + 8) . $suffix . PHP_EOL;

                            break;
                        default :
                            $prefix = $suffix = ""; $matches = [];
                            if ($classList = $node->getAttribute("class") and preg_match_all('/(\w+)(?:\s+)?/', $classList, $matches)) {
                                foreach ($matches[1] as $class) {
                                    if ($stylesheet->hasStyle($class)) {
                                        $suffix .= $stylesheet->getStyle($class)->getSuffix();
                                        $prefix .= $stylesheet->getStyle($class)->getPrefix();
                                    }
                                }
                            }
                            if (in_array($tag, $this->blocktags)) {

                                $prefix .= PHP_EOL;
                                $suffix .= PHP_EOL;
                            }
                            $result .= $prefix . $this->parseNodes($node, $stylesheet) . $suffix;
                            break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Parse style tags
     * @param string $message
     * @return string
     */
    protected function parseStyles(string $message): string {
        $html = '<!DOCTYPE html><head></head><body>' . $message . '</body></html>';
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $body = $dom->getElementsByTagName('body')->item(0);
        return $body instanceof DOMNode ? $this->parseNodes($body, $this->stylesheet) : $message;
    }

}
