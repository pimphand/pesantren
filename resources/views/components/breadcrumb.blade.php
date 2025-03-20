<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="breadcomb-wp">
            <div class="breadcomb-icon">
                <i class="notika-icon notika-windows"></i>
            </div>
            <div class="breadcomb-ctn">
                <h2>{{$title ?? "Dashboard"}}</h2>
                <p>{{$description ?? "Dashboard "}}</p>
            </div>
        </div>
    </div>
    @if(isset($buttonTitle) && $buttonTitle)
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="breadcomb-report">
                <button data-toggle="tooltip"  data-placement="left" title="{{$buttonTitle ?? ""}}"
                        class="btn _add_button">
                    <i class="notika-icon notika-plus-symbol"></i>
                </button>
            </div>
        </div>
    @endif
</div>
