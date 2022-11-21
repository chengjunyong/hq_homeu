<div style="text-align: center">
  <h1>{{$hamper->name}}</h1>
    @foreach($products as $product)
      <div>
        ({{$product->product_name}} - <strong>Rm {{number_format($product->price,2)}})</strong> x <strong>{{ number_format($list->where('barcode',$product->barcode)->first()->quantity,2) }}</strong> (Qty)
      </div>
    @endforeach
</div>

<script>
  window.print();
</script>
