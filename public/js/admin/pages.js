$(document).ready(function () {
    let table = $('#pages-table').DataTable({
        order: [[ 0, "desc" ]],
        serverSide: true,
        pageLength: 25,
        ajax: {
			url: window.location.href
		},
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'link', name: 'link' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '#pages-table .delete-resource', function (e) {
        e.preventDefault();
        deleteResource(table, $(this).data('link'));
    });

    $(document).on('click', '.add-dynamic-block', function () {
        let $block = $('.card[data-dynamic="' + $(this).data('dynamic') + '"]').first().clone();
        $block.find('input[type="text"]').val('');
        $block.find('input.file-value-input-block').val('');
        $block.find('textarea').val('');
        $block.find('.ck-blurred').html('<p><br data-cke-filler="true"></p>');
        $block.find('.icon_thumbnails').hide();
        $block.find('img').attr('src', '');
        $block.find('.custom-file-label').text('Choose file');
        $('.list-dynamic-blocks[data-dynamic="' + $(this).data('dynamic') + '"]').append($block);

        $("html, body").animate({scrollTop: $block.offset().top - 70}, "fast");
    })

    $(document).on('click', '.remove-dynamic-block', function () {
        if ($('.card[data-dynamic="' + $(this).data('dynamic') + '"]').length <= 1) {
            swal.fire('Error!', 'You cannot delete the last block.', 'error');
            return false;
        }

        $(this).closest('.card[data-dynamic]').remove();
    });
});
