$(document).ready(function () {
    let table = $('#feedback-bans-table').DataTable({
        order: [[ 0, "desc" ]],
        serverSide: true,
        ajax: {
			url: window.location.href,
			data: function (filter) {
                addTableFilters(filter);
			}
		},
        columns: [
            { data: 'id', name: 'id'},
            { data: 'type', name: 'type'},
            { data: 'value', name: 'value'},
            { data: 'tries', name: 'tries'},
            { data: 'action', name: 'action'},
            { data: 'is_active', name: 'is_active'},
            { data: 'created_at', name: 'created_at'},
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '#feedback-bans-table .delete-resource', function (e) {
        e.preventDefault();
        deleteResource(table, $(this).data('link'));
    });

	$('.table-filter').change(function() {
		table.draw();
	});
});
