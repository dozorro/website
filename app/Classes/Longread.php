<?php

namespace App\Classes;
use App\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * Class Longread
 * @package App\Classes
 */
class Longread
{
    /**
     * @var array
     */
    protected $blocks = [];

    /**
     * @var string
     */
    protected $backendClassName = '';

    /**
     * @var string
     */
    protected $objectId = 0;

    /**
     * Longread constructor.
     * @param array $blocks
     * @param $objectId
     * @param string $backendClassName
     */
    public function __construct(array $blocks, $objectId, $backendClassName = 'Perevorot\Page\Models\Page')
    {
        $this->blocks = $blocks;
        $this->backendClassName = $backendClassName;
        $this->objectId = $objectId;

        $this->proccessData();
    }

    /**
     * @return void
     */
    private function proccessData()
    {
        foreach ($this->blocks as $block) {
            $block->data = $this->processBlockClass($block);

            if (isset($block->files)) {
                $this->proccessFiles($block);
            }
        }
    }

    /**
     * @param $block
     *
     * @return array|mixed
     */
    private function processBlockClass($block)
    {
        $namespace = '\App\Classes\Blocks\\' . ucfirst($block->alias);

        if (!class_exists($namespace)) {
            return [];
        }

        /**
         * @var \App\Classes\Blocks\IBlock $block
         */
        $block = new $namespace($block);

        return $block->get();
    }

    /**
     * @param \stdClass $block
     */
    private function proccessFiles(\stdClass $block)
    {
        foreach ($block->files as $field => $file)
        {
            if (ends_with($field, 's'))  {
                $block->value->{$field} = File::where('attachment_type', $this->backendClassName)
                    ->where('attachment_id', $this->objectId)
                    ->where('field', $file)
                    ->orderBy('id', 'DESC')
                    ->get()
                ;
            } else {
                $block->value->{$field} = File::where('attachment_type', $this->backendClassName)
                    ->where('attachment_id', $this->objectId)
                    ->where('field', $file)
                    ->orderBy('id', 'DESC')
                    ->first()
                ;
            }
        }
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}
