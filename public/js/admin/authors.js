$(document).ready(function () {
    let table = $('#authors-table').DataTable({
        order: [[ 0, "desc" ]],
        serverSide: true,
        ajax: {
			url: window.location.href
		},
        columns: [
            { data: 'id', name: 'id' },
            { data: 'avatar', name: 'avatar' },
            { data: 'name', name: 'name' },
            { data: 'created_at', name: 'created_at', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '#authors-table .delete-resource', function (e) {
        e.preventDefault();
        deleteResource(table, $(this).data('link'));
    });
});
