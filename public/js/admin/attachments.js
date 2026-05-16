$(document).ready(function () {
    let table = $('#attachments-table').DataTable({
        order: [[ 0, "desc" ]],
        serverSide: true,
        ajax: {
			url: window.location.href,
			data: function (filter) {
				addTableFilters(filter);
			}
		},
        columns: [
            { data: 'id', name: 'id' },
            { data: 'original_name', name: 'original_name' },
            { data: 'type', name: 'type' },
            { data: 'size', name: 'size' },
            { data: 'created_at', name: 'created_at', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

	$('.table-filter').change(function() {
		table.draw();
	});
});
