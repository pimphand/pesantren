<select class="form-control {{$name}}" id="{{$name}}" name="{{$name}}" data-live-search="false">
    <option value="">Pilih {{$title}}</option>
    @foreach($options as $item)
        <option
            value="{{$item['id']}}" @if(isset($selected)) {{$selected == $item['id'] ? "selected" : ''}}  @endif>{{$item['name']}}</option>
    @endforeach
</select>
<code id="{{$name ?? ''}}_error"  class="error" style="display: none; background-color: transparent;"></code>
