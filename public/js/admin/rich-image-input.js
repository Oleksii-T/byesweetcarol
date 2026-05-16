$(document).ready(function () {
    $(document).on('change', '.rii-filefile', function (e) {
        e.preventDefault();
        showFile(this);
    });

    $(document).on('click', '.rii-box', function (e) {
        e.preventDefault();
        let wraper = riiWraper(this);
        wraper.find('.rii-filefile').trigger('click');
    });

    document.querySelectorAll('.rii-box').forEach(function (dropBox) {
        dropBox.addEventListener('drop', handleDrop)
    });

    $(document).on('dragover', '.rii-box', function (e) {
        e.preventDefault();
    });

    $(document).on('click', '.rii-action-add', function (e) {
        e.preventDefault();
        let wraper = $(this).parent().find('.rii-multiple-wrapper');
        let clone = wraper.find('.rii-wrapper').first().clone();
        let uuid = uuidv4();
        clone.attr('data-uuid', uuid);
        clone.find('input').val(''); // clear inputs
        clone.find('.rii-box span').removeClass('d-none'); // remove image visualization
        clone.find('.rii-box img').addClass('d-none').attr('src', ''); // remove image visualization
        clone.find('.rii-box').get(0).addEventListener('drop', handleDrop); // add event for drag&drop
        wraper.append(clone);
    })

    $(document).on('click', '.rii-action-remove', function (e) {
        e.preventDefault();
        let wraper = $(this).closest('.rii-multiple-wrapper');
        let item = $(this).closest('.rii-wrapper');

        if (wraper.find('.rii-wrapper').length > 1) {
            item.remove();
            return;
        }

        item.find('input').val('');
        item.find('.rii-box span').text('Drag files here, or click to upload');
        item.find('.rii-box img').addClass('d-none').attr('src', '');
    })

    $(document).on('click', '.rii-newimage-submit', function (e) {
        e.preventDefault();
        let modal = $(this).closest('.modal');
        let alt = modal.find('[name="alt"]').val();
        let title = modal.find('[name="title"]').val();
        let uuid = modal.attr('data-uuid');
        let wraper = $(`.rii-wrapper[data-uuid="${uuid}"]`);

        // move new meta data to form inputs
        wraper.find('.rii-filealt').val(alt);
        wraper.find('.rii-filetitle').val(title);

        // close modal
        modal.find('[data-dismiss]').trigger('click');
    })

    $(document).on('click', '.rii-action-editnew', function (e) {
        let wraper = riiWraper($(this));
        let uuid = wraper.attr('data-uuid');
        let modal = $(this).data('target');
        let alt = wraper.find('.rii-filealt').val();
        let title = wraper.find('.rii-filetitle').val();

        // get the modal for edit meta
        modal = $(modal).attr('data-uuid', uuid);

        // fill the modal with meta data
        modal.find('[name="alt"]').val(alt);
        modal.find('[name="title"]').val(title);
    })

    $(document).on('click', '.rii-action-browse', function (e) {
        let uuid = riiWraper($(this)).attr('data-uuid');
        loadImages(1, uuid);
    })

    $(document).on('click', '.rii-is-img', function (e) {
        let modal = $(this).closest('#select-image');
        let uuid = modal.attr('data-uuid');
        let image = $(this).data('image');
        let rii = $(`.rii-wrapper[data-uuid="${uuid}"]`).closest('.rii-wrapper');

        modal.find('[data-dismiss]').trigger('click');

        if (!rii.hasClass('is-vue')) {
            rii.find('.rii-box img').attr('src', image.url);
            rii.find('.rii-filename').text(image.name);
            rii.find('.rii-fileid').val(image.id);
            rii.find('.rii-filefile').val('');
            rii.find('.rii-box img').removeClass('d-none');
            rii.find('.rii-action-editnew').addClass('d-none');
        }

        console.log(`todo - send event to vue`); //! LOG

        var selectionFired = new CustomEvent("rii-img-selected", {
            detail: {image, uuid}
        });
        document.dispatchEvent(selectionFired);
    })

    let riiSiSearchTimeOut = null;
    $(document).on('keyup', '.rii-is-search', function (e) {
        if (riiSiSearchTimeOut) {
            clearTimeout(riiSiSearchTimeOut);
        }
        riiSiSearchTimeOut = setTimeout(() => {
            loadImages();
        }, 700);
    })

    // move cursor to the end after refresh
    $(document).on('focus', '.rii-is-search', function (e) {
        var that = this;
        setTimeout(function(){ that.selectionStart = that.selectionEnd = 10000; }, 0);
    });

    $(document).on('change', '.rii-is-sort', function (e) {
        loadImages();
    })

    $(document).on('click', '.rii-is-pagination .page-link', function (e) {
        e.preventDefault();
        let page = $(this).text();
        loadImages(page);
    })
});

function riiWraper(el) {
    return $(el).closest('.rii-wrapper');
}

function handleDrop(e) {
    e.preventDefault();

    let file = e.dataTransfer.items[0].getAsFile();

    // Create a new DataTransfer object
    let dataTransfer = new DataTransfer();

    // Add the file to the DataTransfer object
    dataTransfer.items.add(file);

    // Find the file input element
    let fileInput = riiWraper(this).find('.rii-filefile')[0];

    // Assign the DataTransfer object to the file input element
    fileInput.files = dataTransfer.files;

    showFile(e.target);
}

function showFile(el) {
    let wraper = riiWraper(el);
    let input = wraper.find('.rii-filefile');

    // show file name
    let name = input.val().split('\\').pop();
    wraper.find('.rii-filename').text(name);

    // make file alt and title
    name = name.split('.');
    name = name.length==1 ? name[0] : name.slice(0, -1).join('.');
    name = name.replace(/-/g, ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    wraper.find('.rii-filealt').val(name);
    wraper.find('.rii-filetitle').val(name);
    wraper.find('.rii-fileid').val('');

    // show btn to edit file_alt and file_title
    wraper.find('.rii-action-editnew').removeClass('d-none');

    // show file preview
    const [file] = input[0].files;
    if (!file) {
        return;
    }

    // wraper.find('.rii-box span').addClass('d-none');
    wraper.find('.rii-filepreview').removeClass('d-none').attr('src', URL.createObjectURL(file));
}

function loadImages(page=1, uuid=null) {
    el = $('#select-image');
    if (uuid) {
        el.attr('data-uuid', uuid);
    }
    el.addClass('cursor-wait');
    $.ajax({
        url: $(el).data('url'),
        data: {
            search: el.find('[name=search]').val(),
            sort: el.find('[name=sort]').val(),
            page
        },
        success: (response)=>{
            el.removeClass('cursor-wait');
            el.find('.modal-body').html(response.data.html);
            if (el.find('.rii-is-search').val()) {
                el.find('.rii-is-search').focus();
            }
        },
        error: function(response) {
            showServerError(response);
        }
    });
}
function uuidv4() {
    return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, c =>
      (+c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> +c / 4).toString(16)
    );
}
