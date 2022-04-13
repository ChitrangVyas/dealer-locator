@foreach ($order->getPOImages() as $orderImage)
<div class="col-sm-12 mt-2 mb-2 uplodedPo">
    @if(is_array($orderImage->getSrc()))
    <a href="{!! url($orderImage->getSrc()['file_path']) !!}" target="_blnk">
    <img
    width="50px"
    src="{!! $orderImage->getSrc()['src'] !!}"
        title="{!! $orderImage->getSrc()['title'] !!}"
    />
    <span>{!! $orderImage->getSrc()['title'] !!} </span>
    </a> 
   <i class="fa fa-trash text-danger ml-2 delete_po_image" style="cursor:pointer" data-id="{{$orderImage['id']}}"></i>
   @else
    <a href="{!! url($orderImage->image) !!}" target="_blnk">{{$orderImage->image}}</a>
    <i class="fa fa-trash text-danger ml-2 delete_po_image" style="cursor:pointer" data-id="{{$orderImage['id']}}"></i>
    @endif
</div>                
@endforeach
            