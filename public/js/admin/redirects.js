$(document).ready(function () {
    let table = $('#redirects-table').DataTable({
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
            { data: 'from', name: 'from'},
            { data: 'to', name: 'to'},
            { data: 'hits', name: 'hits'},
            { data: 'code', name: 'code'},
            { data: 'last_at', name: 'last_at'},
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '#redirects-table .delete-resource', function (e) {
        e.preventDefault();
        deleteResource(table, $(this).data('link'));
    });

	$('.table-filter').change(function() {
		table.draw();
	});
});
