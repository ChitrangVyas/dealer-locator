<?php

namespace App\Exports;

use App\Http\Controllers\Admin\MainController;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport extends MainController implements FromCollection,WithHeadings
{
    private $products;
    public function __construct($products){
        $this->products = $products;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        foreach($this->products as $product){            
            $product_image_html = (!empty($product->image))?url($product->image):'';
            $inventory = (empty($product->inventory))?'0':$product->inventory;
            $status = (!empty($product->status)) ? 'Enable': 'Disabled';
            $data[] = [$product->id,$product->name,$product->brand_name,$product_image_html,$inventory,$status];
        }
        return collect($data);
    }
    public function headings(): array
    {
        return [
            'id',
            'name',
            'brand',
            'image',
            'inventory',
            'status'
        ];
    }
}
