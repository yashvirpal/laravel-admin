<?php

namespace App\View\Components\Frontend;

use App\Services\MetaService;
use Illuminate\View\Component;

class Meta extends Component
{
    public $data;

    public function __construct(
        $model = null,
        $listitems = null, 
        $title = null,
        $description = null,
        $keywords = null,
        $image = null,
        $canonical = null,
        $schema = null,
        bool $is404 = false,
        $ogType = null
    ) {
        $overrides = compact('title', 'description', 'keywords', 'image', 'canonical', 'schema', 'is404', 'ogType');

        $this->data = MetaService::generate($model, $overrides, $listitems);
    }

    public function render()
    {
        return view('components.frontend.meta', $this->data);
    }
}
