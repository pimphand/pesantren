<div class="form-group">
    <div class="nk-int-st">
        <input type="{{$type ?? 'text'}}" name="{{$name}}" id="{{$name}}" class="form-control" placeholder="{{$placeholder ?? "Enter text here"}}">
    </div>
    <code id="{{$name ?? ''}}_error"  class="error" style="display: none"></code>
</div>
