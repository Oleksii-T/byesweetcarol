window.SUMMERNOTE_DEFAULT_CONFIGS = {
    minHeight: '140px',
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['para', ['ul', 'ol', 'link']],
        ['insert', ['picture', 'table']],
        ['misc', ['undo', 'redo']],
        ['admin', ['codeview', 'htmlformat', 'htmlminify', 'insertlink']],
    ],
    buttons: {
        htmlformat: function (context) {
            var ui = $.summernote.ui;
            var button = ui.button({
                contents: '<i class="fa fa-paint-brush"/>',
                tooltip: 'Format HTML',
                click: function () {
                    let code = context.code();
                    const options = { indent_size: 2, space_in_empty_paren: true };
                    let formatted = html_beautify(code, options);
                    context.code(formatted);
                    showToast('Inner HTML formatted!');
                },
            });
            return button.render();
        },
        htmlminify: function (context) {
            var ui = $.summernote.ui;
            var button = ui.button({
                contents: '<i class="fa fa-eraser"/>',
                tooltip: 'Minify HTML',
                click: function () {
                    let code = context.code();
                    let minified = code.replace(/\t|\s{2,}/g, '');
                    context.code(minified);
                    showToast('Inner HTML minified!');
                },
            });
            return button.render();
        },
        insertlink: function (context) {
            var ui = $.summernote.ui;
            var button = ui.button({
                contents: '<i class="fa fa-link"/>',
                tooltip: 'Insert link',
                click: function () {
                    let selectedText = context.invoke('editor.getSelectedText');
                    let selectedLink = '';
                    let useBlank = false;
                    let useNofollow = false;
                    swal.fire({
                        title: 'Insert Link',
                        html: `<div class="custom-link-insertion" style="text-align: left;">
                            <div class="form-group">
                                <label>Text to display</label>
                                <input name="selected_text" type="text" class="form-control" value="${selectedText}">
                            </div>
                            <div class="form-group">
                                <label>To what URL should this link go?</label>
                                <input name="url_to_insert" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input name="use_blank" type="checkbox" value="1">
                                        Open in new window
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="use_nofollow" type="checkbox" value="1">
                                        Make nofollow
                                    </label>
                                </div>
                            </div>
                        </div>`,
                        showCancelButton: false,
                        confirmButtonText: 'Insert Link',
                        preConfirm: function () {
                            let wraper = $('.custom-link-insertion');
                            selectedText = wraper.find('[name="selected_text"]').val();
                            selectedLink = wraper.find('[name="url_to_insert"]').val();
                            useBlank = wraper.find('[name="use_blank"]').is(':checked');
                            useNofollow = wraper.find('[name="use_nofollow"]').is(':checked');

                            if (!selectedText || !selectedLink) {
                                return Swal.showValidationMessage('Please fill the data');
                            }
                        },
                    }).then(result => {
                        console.log(result); //! LOG
                        if (result.value) {
                            console.log('insert link 4'); //! LOG
                            var node = document.createElement('a');
                            node.href = selectedLink;
                            node.textContent = selectedText;
                            if (useBlank) {
                                node.target = '_blank';
                            }
                            if (useNofollow) {
                                node.rel = 'nofollow';
                            }
                            context.invoke('editor.restoreRange');
                            context.invoke('editor.insertNode', node);
                        }
                    });
                },
            });
            return button.render();
        },
    },
    styleTags: ['p', 'h2', 'h3', 'h4'],
    disableDragAndDrop: true,
    codeviewIframeFilter: true,
    callbacks: {
        onFocus: () => $('.note-editor').addClass('focused'),
        onBlur: () => $('.note-editor').removeClass('focused'),
        onImageUpload: function (files) {
            console.log(`FUNCTION onImageUpload`); //! LOG
            var editor = $(this);
            var data = new FormData();
            data.append('file', files[0]);
            data.append('_token', window.Laravel.csrf);
            $.ajax({
                url: '/admin/attachments/upload/', // your upload script
                type: 'post',
                data: data,
                processData: false,
                contentType: false,
                success: function (response) {
                    // insert the returned image url to the editor
                    editor.summernote('insertImage', response.data.url, function ($image) {
                        $image.attr('title', response.data.title);
                        $image.attr('alt', response.data.alt);
                    });
                },
            });
        },
    },
    codemirror: {
        theme: 'monokai',
    },
};

$(document).ready(function () {
    tippy('[data-tippy-content]');
    $('.summernote').summernote();
    $('.custom-summernote').summernote(window.SUMMERNOTE_DEFAULT_CONFIGS);
    $('.select2').select2();
    $('.select2-tags').select2({
        tags: true,
    });
    $('.daterangepicker-single').each(function (index) {
        let val = $(this).val() ?? '';
        $(this)
            .daterangepicker({
                singleDatePicker: true,
                timePicker: false,
                showDropdowns: true,
                // minYear: 2000,
                // maxYear: parseInt(moment().format('YYYY'),10),
                locale: {
                    format: 'YYYY-MM-DD',
                },
            })
            .val(val);
    });

    $('.daterangepicker-single-clear').click(function () {
        let picker = $(this).parent().find('.daterangepicker-single');
        picker.val('');
    });

    // general logic of ajax form submit (supports files)
    $(document).on('submit', 'form.general-ajax-submit', async function (e) {
        e.preventDefault();
        loading();
        let form = $(this);
        let formData = new FormData(this);
        $('.input-error').empty();

        if (form.hasClass('ask')) {
            const res = await swal.fire({
                title: form.data('asktitle') || 'Are you sure?',
                text: form.data('asktext') || "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, do it!',
            });

            if (!res.isConfirmed) {
                return;
            }
        }

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: response => {
                showServerSuccess(response);
            },
            error: function (response) {
                swal.close();
                showServerError(response);
            },
        });
    });

    // show uploaded file name
    $(document).on('change', '.show-uploaded-file-name input', function (e) {
        let name = $(this).val().split('\\').pop();
        $(this).closest('.show-uploaded-file-name').find('.custom-file-label').text(name);
    });

    // show uploaded file preview
    $(document).on('change', '.show-uploaded-file-preview input', function (e) {
        const [file] = this.files;
        if (file) {
            let el = $(this).closest('.show-uploaded-file-preview').find('.custom-file-preview');
            el.removeClass('d-none');
            el.attr('src', URL.createObjectURL(file));
        }
    });

    // trigger element by click manuly
    $('[data-trigger]').click(function () {
        $($(this).data('trigger')).trigger('click');
    });

    // copy text
    $('[data-copy]').click(function (e) {
        e.preventDefault();
        let text = $($(this).data('copy')).text();
        let message = $(this).data('message');
        var dummy = document.createElement('textarea');
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);
        Toast.fire({
            icon: 'success',
            title: message ?? 'Copied successfully',
        });
    });

    // automaticaly fill slug from other field
    let autoslugs = {};
    $('[data-autoslug]').each(function (index) {
        let target = $(this).data('autoslug');
        let autoSlugEl = $(this);

        autoslugs[target] = {
            do: true,
            slug: autoSlugEl,
            target: $(target),
        };

        $(target).on('input', function () {
            let val = $(this).val();
            if (!val || !autoslugs[target].do) {
                return;
            }
            val = slugify(val);
            autoSlugEl.val(val);
        });

        autoSlugEl.on('input', function () {
            autoslugs[target].do = false;
        });
    });
});

// flash notification
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: toast => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
});

// show message depends on role and fade out it after 3 sec
function showToast(title, icon = true) {
    Toast.fire({
        icon: icon ? 'success' : 'error',
        title: title,
        // toast: false
    });
}

// show popup notification
function showPopUp(title = null, text = null, role) {
    if (title === null) {
        title = role ? 'Success' : 'Oops!';
    }
    if (text === null) {
        text = role ? '' : 'Something went wrong!';
    }
    swal.fire(title, text, role ? 'success' : 'error');
}

//delete resource from datatable
function deleteResource(dataTable, url) {
    swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
    }).then(result => {
        if (result.value) {
            $.ajax({
                url: url,
                type: 'delete',
                data: {
                    _token: $("[name='csrf-token']").attr('content'),
                },
                success: response => {
                    if (response.success) {
                        if (dataTable) {
                            Toast.fire({
                                icon: 'success',
                                title: response.message,
                            });
                            dataTable.draw();
                        } else {
                            showServerSuccess(response);
                        }
                    } else {
                        swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    swal.fire('Error!', '', 'error');
                },
            });
        }
    });
}

// general error logic, after ajax form submit been processed
function showServerError(response) {
    if (response.status == 422) {
        for ([field, value] of Object.entries(response.responseJSON.errors)) {
            // escape dot in field name
            field = field.replace(/\./g, '\\.');

            // compact the error message
            let errorText = '';
            for (const [key, error] of Object.entries(value)) {
                errorText = errorText ? errorText + '<br>' + error : error;
            }

            // find the element to show error
            let errorElement = $(`.input-error[data-input=${field}]`);

            // check if field is array
            let dotI = field.indexOf('.');
            if (!errorElement.length && dotI != -1) {
                field = field.slice(0, dotI);
                errorElement = $(`.input-error[data-input=${field}]`);
            }

            // insert error message
            if (errorElement.length) {
                errorElement.html(errorText);
            }
        }
        return;
    }

    showToast(response.responseJSON.message, false);
}

// general success logic, after ajax form submit been processed
function showServerSuccess(response) {
    if (response.success) {
        if (response.data?.redirect) {
            window.location.href = response.data.redirect;
        } else if (response.data?.reload) {
            window.location.reload();
        } else {
            showToast(response.message);
        }
    } else {
        swal.fire('Error!', response.message, 'error');
    }
}

//show loading unclosable popup
function loading(text = 'Request processing...') {
    if (text === false) {
        swal.close();
        return;
    }

    swal.fire({
        title: 'Wait!',
        text: text,
        showConfirmButton: false,
        allowOutsideClick: false,
    });
}

// simple slugify
function slugify(str) {
    return String(str)
        .normalize('NFKD') // split accented characters into their base characters and diacritical marks
        .replace(/[\u0300-\u036f]/g, '') // remove all the accents, which happen to be all in the \u03xx UNICODE block.
        .trim() // trim leading or trailing whitespace
        .toLowerCase() // convert to lowercase
        .replace(/[^a-z0-9 -]/g, '') // remove non-alphanumeric characters
        .replace(/\s+/g, '-') // replace spaces with hyphens
        .replace(/-+/g, '-'); // remove consecutive hyphens
}

// add table-filters to data-table request
function addTableFilters(data, wraper = null) {
    let selector = '.table-filter';
    let filters = wraper ? wraper.find(selector) : $(selector);

    filters.each(function (i) {
        let name = $(this).attr('name');
        let val;
        if ($(this).attr('type') == 'checkbox') {
            val = $(this).prop('checked') ? 1 : 0;
        } else {
            val = $(this).val();
        }
        data[name] = val;
    });
}
