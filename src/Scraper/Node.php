<?php

namespace PersonalTouch\Scraper;

class Node
{
    /**
     * Page title
     *
     * @var string
     */
    private $value;

    /**
     * Page URL
     *
     * @var string
     */
    private $key;

    /**
     * @var Node
     */
    private $left;

    /**
     * @var Node
     */
    private $right;

    /**
     * Node constructor.
     * @param string $value
     * @param string $key
     * @param Node $left
     * @param Node $right
     */
    public function __construct(string $key, string $value, ?Node $left, ?Node $right)
    {
        $this->key = $key;
        $this->value = $value;
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return Node
     */
    public function getLeft(): ?Node
    {
        return $this->left;
    }

    /**
     * @param Node $left
     */
    public function setLeft(?Node $left): void
    {
        $this->left = $left;
    }

    /**
     * @return Node
     */
    public function getRight(): ?Node
    {
        return $this->right;
    }

    /**
     * @param Node $right
     */
    public function setRight(?Node $right): void
    {
        $this->right = $right;
    }


}
