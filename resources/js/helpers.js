import Swal from 'sweetalert2';

export default {
    showLoading(text='Request processing...') {
        return Swal.fire({
            title: 'Wait!',
            text: text,
            showConfirmButton: false,
            allowOutsideClick: false
        });
    },
    showError(title='Oops!', text='Something goes wrong<br>Please refresh the page and try again') {
        return Swal.fire(title, text, 'error');
    },
    showToast(title='Success', type=true) {
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        }).fire({
            icon: type ? 'success' : 'error',
            title: title,
        });
    },
    showNotif(title='', text='', type=true) {
        if (typeof type == 'boolean') {
            type = type ? 'success' : 'error';
        }
        return Swal.fire(title, text, type);
    },
    objToFormData(formData, data, parentKey) {
        if (Array.isArray(data)) {
            for (let i = 0; i < data.length; i++) {
                this.objToFormData(formData, data[i], parentKey ? `${parentKey}[${i}]` : parentKey);
            }
        } else if (data && typeof data === "object" && !(data instanceof File)) {
            Object.keys(data).forEach((key) => {
                this.objToFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key)
            })
        } else {
            if (!data) {
                return
            }

            let value = typeof data === "boolean" || typeof data === "number" ? data.toString() : data
            formData.append(parentKey, value)
        }
    },
    // simple slugify
    slugify(str) {
        return String(str)
          .normalize('NFKD') // split accented characters into their base characters and diacritical marks
          .replace(/[\u0300-\u036f]/g, '') // remove all the accents, which happen to be all in the \u03xx UNICODE block.
          .trim() // trim leading or trailing whitespace
          .toLowerCase() // convert to lowercase
          .replace(/[^a-z0-9 -]/g, '') // remove non-alphanumeric characters
          .replace(/\s+/g, '-') // replace spaces with hyphens
          .replace(/-+/g, '-'); // remove consecutive hyphens
    },
    findParent(el, sel) {
        while ((el = el.parentElement) && !((el.matches || el.matchesSelector).call(el, sel)));
        return el;
    },
    uuidv4() {
        return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, c =>
          (+c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> +c / 4).toString(16)
        );
    }
};
