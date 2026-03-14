$(document).ready(function () {
    let table = $('#comments-table').DataTable({
        order: [[ 0, "desc" ]],
        serverSide: true,
        ajax: {
			url: window.location.href
		},
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user', name: 'user' },
            { data: 'post', name: 'post' },
            { data: 'text', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '#comments-table .delete-resource', function (e) {
        e.preventDefault();
        deleteResource(table, $(this).data('link'));
    });

    $(document).on('submit', '.comment-update-status-form', function (e) {
        e.preventDefault();
        loading();
        let form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            success: (response)=>{
                table.draw();
                loading(false);
            },
            error: function(response) {
                showServerSuccess(response);
            }
        });
    })
});
