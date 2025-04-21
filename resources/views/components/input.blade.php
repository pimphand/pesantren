<div class="form-group">
    <div class="nk-int-st">
        @if (isset($type) && $type == 'file')
        <div class="custom-file-input">
            <label class="file-label" for="photo">Pilih foto</label>
            <span class="file-name" id="fileName">Belum ada foto yang dipilih</span>
            <input type="{{$type ?? 'text'}}" {{ isset($type) && $type === 'number' ? 'min=0' : '' }} name="{{$name}}" id="{{$name}}" accept=".jpg,.jpeg,.png" class="form-control hidden-file-input" placeholder="{{$placeholder ?? "Enter text here"}}">
        </div>
        @else
        <input type="{{$type ?? 'text'}}" {{ isset($type) && $type === 'number' ? 'min=0' : '' }} name="{{$name}}" id="{{$name}}" accept=".jpg,.jpeg,.png" class="form-control" placeholder="{{$placeholder ?? "Enter text here"}}">
        @endif
    </div>
    <code id="{{$name ?? ''}}_error"  class="error" style="display: none; background-color: transparent;"></code>
</div>