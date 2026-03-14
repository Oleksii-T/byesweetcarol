$(document).ready(function () {
    let table = $('#games-table').DataTable({
        order: [[ 0, "desc" ]],
        serverSide: true,
        ajax: {
			url: window.location.href,
			data: function (filter) {
                addTableFilters(filter);
			}
		},
        columns: [
            { data: 'id', name: 'id', searchable: false },
            { data: 'name', name: 'name' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '#games-table .delete-resource', function (e) {
        e.preventDefault();
        deleteResource(table, $(this).data('link'));
    });

    $('.scrape-game-btn').click(async function(e) {
        e.preventDefault();
        let res = await swal.fire({
            title: 'Are you sure?',
            text: "Existing fields may be overwritten!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, scrape it!'
        });

        if (!res.isConfirmed) {
            return;
        }

        loading();

        let btn = $(this);

        $.ajax({
            url: btn.data('url'),
            type: 'post',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response)=>{
                showServerSuccess(response)
                window.location.reload();
            },
            error: function(response) {
                showServerError(response)
            }
        });
    })

	$('.table-filter').change(function() {
		table.draw();
	});
});
