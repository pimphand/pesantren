<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="table">
    <div class="normal-table-list mg-t-5">
        <div class="basic-tb-hd">
            <h2>Daftar {{$title}}</h2>
            <div id="search_form"></div>
        </div>
        <div class="bsc-tbl-st">
            <table class="table table-striped">
                <thead>
                <tr>
                    @foreach($columns as $column)
                        <th class="{{$column == "Tindakan" ? 'text-right' : ''}}">{{$column}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody id="{{$id}}">
                </tbody>
            </table>
        </div>
    </div>
    <nav aria-label="Page navigation example" id="pagination">
        <ul class="pagination">

        </ul>
    </nav>
</div>


