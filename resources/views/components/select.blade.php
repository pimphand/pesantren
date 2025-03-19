<div>
    <div class="nk-int-mk sl-dp-mn">
        <h2>{{ $title ?? '' }}</h2>
    </div>
    <div class="bootstrap-select fm-cmp-mg">
        <select class="selectpicker" name="{{ $name }}" id="{{ $name }}">
            @if(isset($data) && is_array($data))
                @foreach($data as $item)
                    @if(is_array($item) || is_object($item))
                        <option value="{{ $item['value'] ?? $item->value ?? '' }}">
                            {{ $item['label'] ?? $item->label ?? $item['value'] ?? $item->value ?? '' }}
                        </option>
                    @else
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>
</div>
