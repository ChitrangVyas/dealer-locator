<?php

namespace App\Exports;

use App\Models\Inquiry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InquiryExport implements FromCollection,WithHeadings
{
    private $inquiries;

    public function __construct($inquiries){
        $this->inquiries = $inquiries;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        foreach($this->inquiries as $inquiry){
            $dealer = (!empty($inquiry->dealer))?$inquiry->dealer:'N/A';
            $retailer = (!empty($inquiry->retailer))?$inquiry->retailer:'N/A';
            $inquiryObj = Inquiry::find($inquiry->id);
            $inquiry_status = (!empty($inquiryObj->status) && $inquiryObj->status !=0 )?$inquiryObj->getInquirystatusName():'N/A';
            $status_html = $inquiry_status;
            $data[] = [$inquiry->id,$inquiry->product,ucwords($inquiry->customer),$inquiry->customer_email,$inquiry->customer_mobile,$inquiry->source,$dealer,$retailer,$status_html,$inquiry->created_at];
        }
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'id',
            'product',
            'customer',
            'email',
            'phone',
            'source',
            'dealer',
            'retailer',
            'status',
            'date'
        ];
    }
}
