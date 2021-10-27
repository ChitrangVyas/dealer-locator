<?php

namespace App\Exports;

use App\Http\Controllers\Admin\MainController;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalesExport extends MainController implements FromCollection
{
    private $orders;

    public function __construct($orders)
    {
        parent::__construct();
        $this->orders = $orders;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        foreach($this->orders as $order){
            $order_data = Order::find($order->id);
            $order_status = 'N/A';
            if(!empty($order_data->getUserOrderStatus()) && (count($order_data->getUserOrderStatus()) > 0 )){
                $order_status = $order_data->getInvoiceStatus($order_data->getUserOrderStatus()[0]->status_id);
            }
            $customer = (!empty($order->customer))?$order->customer:$this->settingJson['name'];
            $added_by  = (!empty($order->added_by))?$order->added_by:$this->settingJson['name'];
            $data[] = [$order->id,$order->order_number,$order->amount, ucwords($added_by), ucwords($customer), $order_status ,$order->created_date];
        }
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'id',
            'order number',
            'total',
            'from',
            'to',
            'status',
            'date'
        ];
    }
}
