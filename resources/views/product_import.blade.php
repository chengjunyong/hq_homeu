<html>
<form action="{{route('postImport')}}" method="post" enctype='multipart/form-data'>
  @csrf
  <div>
    <input type="file" name="product_list" />
    <input type="password" name="password" placeholder="Password" />
    <input type="submit" value="Upload" />
  </div>
</form>
</html>