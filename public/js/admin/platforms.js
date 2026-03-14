$(document).ready(function () {
    let selector = '#platforms-table';
    let table = $(selector).DataTable({
        order: [[ 0, "desc" ]],
        serverSide: true,
        autoWidth: false,
        responsive: true,
        ajax: {
			url: $(selector).data('url') ? $(selector).data('url') : window.location.href,
			data: function (filter) {
                addTableFilters(filter);
			}
		},
        columns: [
            { data: 'id', name: 'id'},
            { data: 'name', name: 'name'},
            { data: 'icon', name: 'icon'},
            { data: 'games', name: 'games'},
            { data: 'created_at', name: 'created_at'},
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', selector+' .delete-resource', function (e) {
        e.preventDefault();
        deleteResource(table, $(this).data('link'));
    });

    $(document).on('click', selector+' .update-resource', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let key = $(this).data('key');
        let data = {
            _token: $("[name='csrf-token']").attr("content"),
            _method: 'PUT'
        };
        data[key] = $(this).data('value');
        $.ajax({
            url,
            type: 'POST',
            data,
            success: (response)=>{
                if (response.success) {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    table.draw();
                } else {
                    swal.fire("Error!", response.message, 'error');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                swal.fire("Error!", '', 'error');
            }
        });
    });

	$('.table-filter').change(function() {
		table.draw();
	});
});
