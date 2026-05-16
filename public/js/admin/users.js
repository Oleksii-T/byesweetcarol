$(document).ready(function () {
    let table = $('#users-table').DataTable({
        order: [[ 0, "desc" ]],
        serverSide: true,
        ajax: {
			url: window.location.href,
			data: function (filter) {
				filter.role = $('.table-filter[name=role]').val();
			}
		},
        columns: [
            { data: 'id', name: 'id' },
            { data: 'email', name: 'email' },
            { data: 'name', name: 'name' },
            { data: 'created_at', name: 'created_at', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '#users-table .delete-resource', function (e) {
        e.preventDefault();
        deleteResource(table, $(this).data('link'));
    });

	$('.table-filter').change(function() {
		table.draw();
	});
});
