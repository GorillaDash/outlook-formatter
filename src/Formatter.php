<?php
declare(strict_types=1);

namespace GorillaDash\OutlookFormatter;

use Illuminate\Support\Arr;
use KubAT\PhpSimple\HtmlDomParser;

/**
 * Class Formatter
 *
 * @package Gorilladash\OutlookFormatter
 */
class Formatter
{
    /**
     * Max contain width
     *
     * @var int
     */
    private $maxWidth;

    /**
     * Auto center
     *
     * @var bool
     */
    private $autoCenter = false;

    /**
     * Formatter constructor.
     *
     * @param int $maxWidth
     */
    public function __construct($maxWidth = 600)
    {
        $this->maxWidth = $maxWidth;
    }

    /**
     * Make all table/td/image to be center
     *
     * @param $value
     *
     * @return $this
     */
    public function setAutoCenter($value)
    {
        $this->autoCenter = $value;
        return $this;
    }

    /**
     * Format html
     *
     * @param $html
     *
     * @return string
     */
    public function format($html)
    {
        $doms = HtmlDomParser::str_get_html($html);
        $body = Arr::get($doms->find('body'), '0');
        $children = optional($body)->children ?? [];
        foreach ($children as $dom) {
            $width = $this->getWidth($dom, $this->maxWidth);
            $this->setWidth($dom, $width);
            $this->setHeight($dom, $this->getHeight($dom));
            if ($this->autoCenter) {
                $this->alignCenter($dom);
            }
            $this->childrenElements($dom, $width);
        }

        return (string)$doms;
    }

    /**
     * Scan all element and set width
     *
     * @param $dom
     * @param $max
     */
    private function childrenElements($dom, $max)
    {
        foreach ($dom->children as $element) {
            $parent = null;
            if (\in_array($element->tag, ['table', 'td', 'img'], true)) {
                $parent = $this->getWidth($element, $max);
                $this->setWidth($element, $parent);
                $this->setHeight($element, $this->getHeight($element));
                if ($this->autoCenter) {
                    $this->alignCenter($element);
                }
            } else {
                $parent = $max;
            }

            if (count($element->children) > 0) {
                $this->childrenElements($element, $parent);
            }
        }
    }

    /**
     * Set align center
     *
     * @param $dom
     */
    private function alignCenter($dom)
    {
        $dom->setAttribute('align', 'center');
    }

    /**
     * Set width
     *
     * @param $dom
     * @param $width
     */
    private function setWidth($dom, $width)
    {
        $dom->setAttribute('width', $width);
    }

    /**
     * Set height
     *
     * @param $dom
     * @param $height
     */
    private function setHeight($dom, $height)
    {
        if ($height) {
            $dom->setAttribute('height', $height);
        }
    }

    /**
     * Get width
     *
     * @param $dom
     * @param $parent
     *
     * @return float|int
     */
    private function getWidth($dom, $parent)
    {
        $pattern = '/(max-)?width:\s(?P<width>\d+(%|px));/';
        $matches = [];
        preg_match_all($pattern, $dom->getAttribute('style'), $matches, PREG_SET_ORDER);
        $max = 0;

        if (\count($matches) === 0) {
            return $parent;
        }

        foreach ($matches as $match) {
            if (stripos($match['width'], 'px') > -1) {
                $width = (int)str_replace('px', '', $match['width']);
            } elseif (strpos($match['width'], '%') > -1) {
                $contain = $parent ?: $this->maxWidth;
                $width = $contain * (int)str_replace('%', '', $match['width']) / 100;
            } else {
                $width = $parent ?: $this->maxWidth;
            }


            if (stripos($match[0], 'max-width') > -1) {
                return $width;
            }

            $max = $width > $max ? $width : $max;
        }
        return $max;
    }

    /**
     * Get height
     *
     * @param $dom
     *
     * @return int|null
     */
    private function getHeight($dom)
    {
        $height = null;
        $pattern = '/height:\s(?P<height>\d+(%|px));/';
        $matches = [];
        preg_match_all($pattern, $dom->getAttribute('style'), $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (stripos($match['height'], 'px') > -1) {
                $height = (int)str_replace('px', '', $match['height']);
            } else {
                $height = $match['height'];
            }
        }

        return $height;
    }
}
