<?php

namespace App\Classes\Blocks;

/**
 * Interface IBlock
 * @package App\Classes\Blocks
 */
abstract class IBlock
{
    /**
     * @var array
     */
    protected $block = [];

    /**
     * IBlock constructor.
     *
     * @param $block
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * @return mixed
     */
    abstract public function get();
}
