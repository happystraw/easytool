<?php

namespace HappyStraw;

/**
 * FViewCreator - A Simple Tool For Create Html/Xml Code
 *
 * @author FangYutao <fangyutao1993@hotmail.com>
 * @since 2017-02-04
 * @version 0.0.1
 */
class FViewCreator
{
    private $ele;
    private $close;

    const KEY_TAG = 'tag';
    const KEY_ATTR = 'attr';
    const KEY_INNER = 'inner';

    protected $selfCloseTags = [
        'br', 'hr', 'area', 'base', 'img', 'input', 'link', 'meta',
        'basefont', 'param', 'col', 'frame',
    ];

    public function __construct()
    {
        $this->ele = [self::KEY_TAG => '', self::KEY_ATTR => [], self::KEY_INNER => []];
    }

    /**
     * New a element object
     *
     * @param string $tag
     * @param string|self|array $attr
     * @param string|self|array $inner
     * @return self
     */
    public static function make($tag = '', $attr = [], $inner = [])
    {
        return (new self)->setTag($tag)->setAttr($attr)->append($inner);
    }

    /**
     * Set the tag is self close or not
     *
     * @param bool $isClose
     * @return self
     */
    public function close($isClose)
    {
        $this->close = $isClose;
        return $this;
    }

    /**
     * Set the tag name of this element
     *
     * @param string $tag
     * @return self
     */
    public function setTag($tag)
    {
        $this->ele[self::KEY_TAG] = $tag;
        return $this->close(in_array($tag, $this->selfCloseTags));
    }

    /**
     * Get the tag name
     *
     * @return string
     */
    public function getTag()
    {
        return $this->ele[self::KEY_TAG];
    }

    /**
     * Set Attibutes
     *
     * @param mixed $name
     * @param bool|null|string
     * @return self
     */
    public function setAttr($name, $value = false)
    {
        // batch set attributes
        if (is_array($name)) {
            $name && $this->ele[self::KEY_ATTR] = array_merge($this->ele[self::KEY_ATTR], $name);
        }
        // delete all attributes
        elseif (is_null($name)) {
            $this->ele[self::KEY_ATTR] = [];
        }
        // single set attribute
        else {
            // delete attribute
            if (is_null($value)) {
                unset($this->ele[self::KEY_ATTR][$name]);
            }
            elseif (FALSE === $value) {
                $name && $this->ele[self::KEY_ATTR][] = $name;
            }
            else {
                $this->ele[self::KEY_ATTR][$name] = $value;
            }
        }
        return $this;
    }

    /**
     * Get Attributes
     *
     * @param  string $name
     * @return array
     */
    public function getAttr($name = '')
    {
        if ($name) {
            return isset($this->ele[self::KEY_ATTR][$name]) ? $this->ele[self::KEY_ATTR][$name] : null;
        } else {
            return $this->ele[self::KEY_ATTR];
        }
    }

    /**
     * Append child elements to inner
     *
     * @param  mixed $inners
     * @return self
     */
    public function append($inners)
    {
        if (is_array($inners) && isset($inners[0])) {
            $this->ele[self::KEY_INNER] = array_merge($this->ele[self::KEY_INNER], $inners);
        }
        else {
            '' !== $inners && $this->ele[self::KEY_INNER][] = $inners;
        }
        return $this;
    }

    /**
     * Repalce inner
     *
     * @param mixed $inner
     * @return self
     */
    public function setInner($inner)
    {
        if (is_null($inner)) {
            $this->ele[self::KEY_INNER] = [];
        } else {
            $this->ele[self::KEY_INNER] = $inner;
        }
        return $this;
    }

    /**
     * Get the inner
     *
     * @return mixed
     */
    public function getInner()
    {
        return $this->ele(self::KEY_INNER);
    }

    /**
     * Empty all info
     */
    public function clear()
    {
        $this->ele = [];
    }

    /**
     * Get the parsed result or this element
     *
     * @param  bool $isEle
     * @return string|array
     */
    public function fetch($isEle = false)
    {
        return $isEle ? $this->ele : $this->parse($this->ele);
    }

    /**
     * Parse the element to string
     *
     * @param  mixed $ele
     * @return string
     */
    public function parse($ele)
    {
        if ($ele instanceof self) return $ele->fetch();
        else $ele = array_merge([self::KEY_TAG => '', self::KEY_ATTR => [], self::KEY_INNER => []], $ele);
        $tagTpl = $this->parseTag($ele[self::KEY_TAG]);
        $attrTpl = $this->parseAttr($ele[self::KEY_ATTR]);
        $innerTpl = $this->parseInner($ele[self::KEY_INNER]);
        return str_replace(
            [$this->getReplaceKey(self::KEY_ATTR), $this->getReplaceKey(self::KEY_INNER)], [$attrTpl, $innerTpl], $tagTpl
        );
    }

    protected function parseTag($tag)
    {
        if (!$tag) return $this->getReplaceKey(self::KEY_INNER);
        return $this->close
        ? "<{$tag}" . $this->getReplaceKey(self::KEY_ATTR) . '/>'
        : "<{$tag}" . $this->getReplaceKey(self::KEY_ATTR) . '>' . $this->getReplaceKey(self::KEY_INNER) . "</{$tag}>";
    }

    protected function parseAttr($attr)
    {
        $str = '';
        if (is_string($attr) && $attr) {
            $str = " {$attr}";
        } elseif (is_array($attr)){
            foreach ($attr as $k => $v) {
                if (is_numeric($k)) {
                    $str .= " {$v}";
                } else {
                    $str .= " {$k}=\"{$v}\"";
                }
            }
        }
        return $str;
    }

    protected function parseInner($inner)
    {
        if (is_numeric($inner)) return strval($inner);
        if (is_string($inner)) return $inner;
        if ($inner instanceof self) return $inner->fetch();
        $str = '';
        if (is_array($inner) && isset($inner[0])) {
            foreach ($inner as $item) {
                if (is_string($item) || is_numeric($item)) {
                    $str .= $item;
                } else {
                    $str .= $this->parse($item);
                }
            }
        }
        return $str;
    }

    protected function getReplaceKey($name)
    {
        return "{{{$name}}}";
    }
}
