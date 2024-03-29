<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\Utils;

if (!defined('ABSPATH')) exit;


/**
 * This class should guarantee that our work with the DOMDocument is unified and safe.
 */
class DomDocumentHelper {
  private \DOMDocument $dom;

  public function __construct(
    string $htmlContent
  ) {
    $this->loadHtml($htmlContent);
  }

  private function loadHtml(string $htmlContent): void {
    libxml_use_internal_errors(true);
    $this->dom = new \DOMDocument();
    if (!empty($htmlContent)) {
      // prefixing the content with the XML declaration to force the input encoding to UTF-8
      $this->dom->loadHTML('<?xml encoding="UTF-8">' . $htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    }
    libxml_clear_errors();
  }

  public function findElement(string $tagName): ?\DOMElement {
    $elements = $this->dom->getElementsByTagName($tagName);
    return $elements->item(0) ?: null;
  }

  public function getAttributeValue(\DOMElement $element, string $attribute): string {
    return $element->hasAttribute($attribute) ? $element->getAttribute($attribute) : '';
  }

  public function getOuterHtml(\DOMElement $element): string {
    return (string)$this->dom->saveHTML($element);
  }
}
