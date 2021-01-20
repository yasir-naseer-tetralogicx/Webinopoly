<?php

namespace App\Exports;

use App\ProductVariant;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ProductVariantExport implements FromView
{
    private $variants;

    public function __construct($variants)
    {
        $this->variants = $variants;
    }


    public function view(): View
    {
        return view('exports.variant_sku', [
            'variants' => $this->variants
        ]);
    }
}
