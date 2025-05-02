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
                            <th class="{{$column == "Tindakan" ? 'text-right' : ''}} sortable"
                                data-sort="{{strtolower($column)}}">
                                {{$column}}
                                <span class="sort-icon">
                                    <i class="fa fa-sort"></i>
                                </span>
                            </th>
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

<style>
    .sortable {
        cursor: pointer;
        position: relative;
    }

    .sortable:hover {
        background-color: #f5f5f5;
    }

    .sort-icon {
        margin-left: 5px;
        display: inline-block;
    }

    .sort-icon i {
        color: #999;
    }

    .sortable.asc .fa-sort {
        transform: rotate(180deg);
        color: #007bff;
    }

    .sortable.desc .fa-sort {
        color: #007bff;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.querySelector('#table');
        const headers = table.querySelectorAll('th.sortable');
        let currentSort = {
            column: null,
            direction: 'asc'
        };

        headers.forEach(header => {
            header.addEventListener('click', function () {
                const column = this.dataset.sort;

                // Remove sort classes from all headers
                headers.forEach(h => h.classList.remove('asc', 'desc'));

                // Toggle sort direction
                if (currentSort.column === column) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.column = column;
                    currentSort.direction = 'asc';
                }

                // Add sort class to current header
                this.classList.add(currentSort.direction);

                // Trigger custom event for sorting
                const event = new CustomEvent('table-sort', {
                    detail: {
                        column: column,
                        direction: currentSort.direction
                    }
                });
                table.dispatchEvent(event);
            });
        });

        table.addEventListener('table-sort', function (e) {
            const { column, direction } = e.detail;
            // Implement your sorting logic here
            // You can use the column index and direction to sort your data
            // Then update the table contents
        });
    });
</script>