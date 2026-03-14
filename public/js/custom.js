var waitClass = 'cursor-wait';

console.slog = (...args) => console.log('🟢', ...args);
console.elog = (...args) => console.log('🔴', ...args);
console.ilog = (...args) => console.log('🔵', ...args);

$.fn.lock = function () {
  this.addClass(waitClass);
  return this;
};
$.fn.isLocked = function () {
  return this.hasClass(waitClass);
};
$.fn.unlock = function () {
  this.removeClass(waitClass);
  return this;
};
$.fn.toggleClassIf = function (className, condition) {
  if (condition) {
    this.addClass(className);
  } else {
    this.removeClass(className);
  }
  return this;
};

$(document).ready(function () {
  // Fancybox.bind(document.querySelectorAll("[data-fancybox='postsgallery']"));
  let fancyBoxGroupsInitted = [];
  $("[data-fancybox='group']").each(function (index) {
    let group = $(this).data('group');
    if (group && !fancyBoxGroupsInitted.includes(group)) {
      console.log(`init fancy box with`, `[data-fancybox='group'][data-group='${group}']`); //! LOG
      Fancybox.bind(`[data-fancybox='group'][data-group='${group}']`);
      fancyBoxGroupsInitted.push(group);
    }
  });
  console.log(`fancyBoxGroups`, fancyBoxGroupsInitted); //! LOG
  Fancybox.bind("[data-fancybox='postsgallery']");
  Fancybox.bind('.preview-image', {
    groupAttr: false,
  });

  // general logic of ajax form submit (supports files)
  $('form.general-ajax-submit').submit(async function (e) {
    e.preventDefault();
    let form = $(this);
    let button = $(this).find('button[type=submit]');
    let formData = new FormData(this);
    if (button.isLocked()) {
      return;
    }

    if (form.hasClass('with-recaptcha')) {
      // grecaptcha.ready(function() {});
      let token = await grecaptcha.execute(window.Laravel.recaptcha_key, { action: 'submit' });
      formData.append('g-recaptcha-response', token);
    }

    if (form.hasClass('ask')) {
      swal
        .fire({
          title: form.data('asktitle'),
          text: form.data('asktext'),
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: form.data('askyes'),
          cancelButtonText: form.data('askno'),
        })
        .then(result => {
          if (result.value) {
            ajaxSubmit(form, formData, button);
          }
        });
      return;
    }

    ajaxSubmit(form, formData, button);
  });

  // send view
  let sendViewUrl = $('[data-sendview]').data('sendview');
  if (sendViewUrl) {
    $.ajax({
      url: sendViewUrl,
      type: 'post',
      data: {
        _token: window.Laravel.csrf,
      },
    });
  }

  // ajax pagination
  $(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    let url = $(this).attr('href');
    let button = $(this);
    let wraper = $('.pagination-content');

    if (!url) {
      return;
    }

    if (wraper.isLocked()) {
      return;
    }

    wraper.lock();

    const offsetTop =
      document.querySelector('.pagination-content').getBoundingClientRect().top +
      document.documentElement.scrollTop -
      20;

    scroll({
      top: offsetTop,
      behavior: 'smooth',
    });

    $.ajax({
      url,
      type: 'get',
      success: response => {
        wraper.html(response.data.html);
        wraper.unlock();
      },
      error: function (response) {
        showServerError(response);
        wraper.unlock();
      },
    });
  });

  // load more posts by click
  $('.show-more-posts').click(function (e) {
    e.preventDefault();
    let button = $(this);
    let type = button.data('type');
    let page = parseInt(button.data('page') || 2);

    if (button.isLocked()) {
      return;
    }

    button.lock();

    $.ajax({
      url: button.data('url'),
      type: 'get',
      data: { page, type },
      success: response => {
        let wraper = button.parent().find('.guides__list');

        if (wraper.length) {
          wraper.append(response.data.html);
        } else {
          button.before(response.data.html);
        }

        button.data('page', page + 1);
        button.unlock();

        if (!response.data.hasMore) {
          button.addClass('d-none');
        }
      },
      error: function (response) {
        showServerError(response);
        button.unlock();
      },
    });
  });

  $('.feedback-form').submit(async function (e) {
    e.preventDefault();
    let button = $(this).find('button[type=submit]');
    let form = button.closest('form');

    if (button.isLocked()) {
      return;
    }

    button.lock();

    let formData = new FormData(this);
    let token = await grecaptcha.execute(window.Laravel.recaptcha_key, { action: 'submit' });
    formData.append('g-recaptcha-response', token);

    $.ajax({
      url: form.attr('action'),
      type: form.attr('method'),
      data: formData,
      contentType: false,
      processData: false,
      success: response => {
        button.unlock();

        if (!response.success) {
          showPopUp(null, response.message, false);
          return;
        }

        showPopUp(null, response.message);
        form.find('[name="name"]').val('');
        form.find('[name="email"]').val('');
        form.find('[name="subject"]').val('');
        form.find('[name="text"]').val('');
      },
      error: function (response) {
        showServerError(response);
        button.unlock();
      },
    });
  });

  bindEnterPressLinks();
  typeText();
});

async function ajaxSubmit(form, formData, button, successCallback = null, errorCallback = null) {
  $('.form-error').empty();
  button.lock();
  return await $.ajax({
    url: form.attr('action'),
    type: form.attr('method'),
    data: formData,
    cache: false,
    contentType: false,
    processData: false,
    success: response => {
      if (successCallback) {
        successCallback(response);
        return;
      }
      button.unlock();
      showServerSuccess(response);
    },
    error: function (response) {
      if (errorCallback) {
        errorCallback(response);
        return;
      }
      button.unlock();
      showServerError(response);
    },
  });
}

// toast notification object
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  // didOpen: (toast) => {
  //     toast.addEventListener('mouseenter', Swal.stopTimer)
  //     toast.addEventListener('mouseleave', Swal.resumeTimer)
  // }
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
function showPopUp(title = null, text = null, role = true) {
  if (title === null) {
    title = role ? 'Success' : 'Oops!';
  }
  if (text === null) {
    text = role ? '' : 'Something went wrong!';
  }
  swal.fire(title, text, role ? 'success' : 'error');
}

//show loading unclosable popup
async function loading(text = 'Request processing...') {
  swal.fire({
    title: 'Wait!',
    text: text,
    showConfirmButton: false,
    allowOutsideClick: false,
  });
}

// general success logic, after ajax form submit been processed
function showServerSuccess(response) {
  if (response.success) {
    if (response.data?.redirect) {
      window.location.href = response.data.redirect;
    } else if (response.data?.open) {
      window.open(response.data.open, '_blank').focus();
    } else if (response.data?.reload) {
      window.location.reload();
    } else if (response.message) {
      showToast(response.message);
    }
  } else {
    showPopUp(null, response.message, false);
  }
}

// general error logic, after ajax form submit been processed
function showServerError(response) {
  let msg = response.responseJSON?.message;
  msg = msg ? msg : response.statusText;

  if (response.status == 402) {
    showSubRequiredModal(msg);
    return;
  }

  if (response.status == 422) {
    swal.close();
    let r = response.responseJSON ?? JSON.parse(response.responseText);
    let firstError = null;
    for ([field, value] of Object.entries(r.errors)) {
      let dotI = field.indexOf('.');
      // if (dotI != -1) {
      //     field = field.slice(0, dotI);
      // }
      field = field.replaceAll('.', '\\.');
      let errorText = '';
      let errorElement = $(`.form-error[data-input=${field}]`);
      errorElement = errorElement.length ? errorElement : $(`.form-error[data-input="${field}[]"]`);
      errorElement = errorElement.length
        ? errorElement
        : $(`[name=${field}]`).closest('.form-group').find('.form-error');
      errorElement = errorElement.length
        ? errorElement
        : $(`[name="${field}[]"]`).closest('.form-group').find('.form-error');

      if (!errorElement.length) {
        continue;
      }

      for (const [key, error] of Object.entries(value)) {
        errorText = errorText ? errorText + '<br>' + error : error;
      }
      errorElement.html(errorText);

      if (!firstError || errorElement.offset().top < firstError.offset().top) {
        firstError = errorElement;
      }
    }

    let firstErrorField = firstError.data('input').replaceAll('.', '\\.');
    let input = $(`[name=${firstErrorField}]`);

    if (input.length && !isScrolledIntoView(input)) {
      animatedScroll(input, 50);
    }
    return;
  }

  showPopUp(null, msg ? msg : null, false);
}

// auth required modal
function showAuthRequiredModal(msg = null) {
  let html = `<h2 class="swal2-title" style="padding: 0 0 24px 0">Authorization required</h2><p>`;
  if (msg) html += msg + '<br>';
  html += `Go to <a href="/login" >Login</a> page.</p>`;

  swal.fire({
    html,
    showConfirmButton: false,
    showCancelButton: true,
  });
}

function typeText() {
  const container = document.querySelector("[data-typing='true']");
  if (!container) {
    return;
  }

  const cursor = container.querySelector('.cursor');
  const targets = Array.from(container.querySelectorAll('.type-target'));
  if (!cursor || targets.length === 0) {
    return;
  }

  const renderWithBreaks = (el, text) => {
    const safe = text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/\n/g, '<br>');
    el.innerHTML = safe;
  };

  let targetIndex = 0;
  let charIndex = 0;
  const targetsText = targets.map(target => {
    const raw = target.dataset.text ?? target.textContent ?? '';
    target.textContent = '';
    return raw.replace(/\s+\n/g, '\n').trim();
  });

  const step = () => {
    const target = targets[targetIndex];
    const fullText = targetsText[targetIndex] || '';
    const currentText = fullText.slice(0, charIndex);
    renderWithBreaks(target, currentText);
    target.appendChild(cursor);

    if (charIndex < fullText.length) {
      charIndex += 1;
      window.setTimeout(step, 12);
      return;
    }

    targetIndex += 1;
    charIndex = 0;
    if (targetIndex < targets.length) {
      window.setTimeout(step, 220);
      return;
    }

    cursor.classList.add('is-blinking');
  };

  step();
}

function bindEnterPressLinks() {
  const links = Array.from(document.querySelectorAll('[data-press="enter"]'));
  if (links.length === 0) {
    return;
  }

  document.addEventListener('keydown', event => {
    if (event.key !== 'Enter') {
      return;
    }
    if (event.metaKey || event.ctrlKey || event.altKey || event.shiftKey) {
      return;
    }

    const active = document.activeElement;
    if (
      active &&
      (active.tagName === 'INPUT' ||
        active.tagName === 'TEXTAREA' ||
        active.isContentEditable)
    ) {
      return;
    }

    const target = links[0];
    const href = target.getAttribute('href');
    if (href) {
      window.location.href = href;
      return;
    }

    target.click();
  });
}
