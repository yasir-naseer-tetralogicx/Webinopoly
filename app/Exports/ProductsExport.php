<?php

namespace App\Exports;

use App\Product;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsExport implements FromView
{
    private $product;

    /**
     * ProductsExport constructor.
     * @param $products
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * @inheritDoc
     */
    public function view(): View
    {
        // TODO: Implement view() method.
        return view('exports.product', [
            'products' => $this->products
        ]);
    }
}
