<?php

namespace Rapidez\SnowdogMenu\Models;

use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Models\Block;
use Rapidez\Core\Models\Category;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\Traits\HasContentAttributeWithVariables;

class MenuItem extends Model
{
    use HasContentAttributeWithVariables {
        HasContentAttributeWithVariables::getContentAttribute as parseVariables;
    }

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'node_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snowmenu_node';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IsActiveScope());
    }

    /**
     * Get the menu item content.
     *
     * @param string $value
     *
     * @return string
     */
    public function getContentAttribute($value)
    {
        if ($this->type == 'category') {
            $category = Category::find($value);

            return isset($category->url_path) ? '/'.$category->url_path : null;
        }

        if ($this->type == 'cms_block') {
            $block = Block::firstWhere('identifier', $value);

            return $block->content ?? null;
        }

        return $this->parseVariables($value);
    }

    /**
     * Get the menu item html.
     *
     * @param object $loop
     *
     * @return string
     */
    public function html(object $loop)
    {
        return view('snowdogmenu::item.'.$this->type, [
            'item' => $this,
            'loop' => $loop,
        ]);
    }
}
