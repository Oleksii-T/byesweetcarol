$(document).ready(function () {
    let table = $('#posts-table').DataTable({
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
            { data: 'title', name: 'title' },
            { data: 'category', name: 'category' },
            { data: 'author', name: 'author' },
            { data: 'views_stats', name: 'views_stats' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '#posts-table .delete-resource', function (e) {
        e.preventDefault();
        deleteResource(table, $(this).data('link'));
    });

	$('.table-filter').change(function() {
		table.draw();
	});

    $('.add-image-input').click(function(e) {
        e.preventDefault();
        $(this)
            .closest('.card')
            .find('.image-input.clone')
            .clone()
            .appendTo($(this).closest('.card').find('.row'))
            .removeClass('d-none')
            .removeClass('clone');
    })

    $(document).on('click', '.delete-image-input', function (e) {
        e.preventDefault();
        let el = $(this).closest('.image-input');
        let url = $(this).data('url');
        if (!url) {
            el.remove();
            return;
        }
        loading();

        $.ajax({
            url: url,
            type: 'post',
            data: {
                _method: 'DELETE',
                _token: $('meta[name=csrf-token]').attr('content')
            },
            success: (response)=>{
                el.remove();
                swal.close();
            },
            error: function(response) {
                showServerError(response);
            }
        });

    })

    $('.add-block').click(function(e) {
        e.preventDefault();
        console.log('add block');
        let wapper = $('.post-block-wrapper');
        wapper.find('.post-block.clone')
            .clone()
            .appendTo(wapper)
            .removeClass('d-none')
            .removeClass('clone');
    })

    $(document).on('click', '.remove-block', function (e) {
        e.preventDefault();
        console.log('remove block');
        $(this).closest('.post-block').remove();
    })

    $(document).on('click', '.add-item', function (e) {
        e.preventDefault();
        console.log('add item');
        let wapper = $(this).closest('.post-block').find('.block-item-wrapper');
        console.log(wapper.find('.block-item.clone'));
        wapper.find('.block-item.clone')
            .clone()
            .appendTo(wapper)
            .removeClass('d-none')
            .removeClass('clone');
    })

    $(document).on('click', '.remove-item', function (e) {
        e.preventDefault();
        console.log('remove item');
        $(this).closest('.block-item').remove();
    })

    $(document).on('change', '.item-type-select', function (e) {
        e.preventDefault();
        console.log('change item type');
        let type = '.' + $(this).val();
        $(this).closest('.block-item')
            .find('.item-input')
            .empty()
            .append(
                $('.item-inputs').find(type).clone()
            );
    })
});
