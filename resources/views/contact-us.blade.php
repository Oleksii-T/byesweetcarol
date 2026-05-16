@extends('layouts.app')

@section('title', $page?->meta_title ?: 'Contact Us')
@section('description', $page?->meta_description)

@section('content')
<div class="page">

    <div class="cat-header">
        <h1 class="cat-title">Contact Us</h1>
    </div>

    <div class="contact-layout">

        <div class="contact-form-wrap">
            <form class="contact-form" id="contact-form">
                @csrf
                <div class="contact-form__field">
                    <label class="contact-form__label" for="contact-name">Name</label>
                    <input class="contact-form__input" type="text" id="contact-name" name="name" placeholder="Your name" required>
                </div>
                <div class="contact-form__field">
                    <label class="contact-form__label" for="contact-email">Email</label>
                    <input class="contact-form__input" type="email" id="contact-email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="contact-form__field">
                    <label class="contact-form__label" for="contact-subject">Subject</label>
                    <input class="contact-form__input" type="text" id="contact-subject" name="subject" placeholder="What is this about?" required>
                </div>
                <div class="contact-form__field">
                    <label class="contact-form__label" for="contact-text">Message</label>
                    <textarea class="contact-form__textarea" id="contact-text" name="text" rows="6" placeholder="Share the details." required></textarea>
                </div>
                <button class="contact-form__btn" type="submit">{{ $page->show('form:cta') }}</button>
            </form>
        </div>

        <div class="contact-side-blocks">
            <div class="contact-block">
                {!! $page->show('side:content') !!}
            </div>
        </div>
    </div>

    <div class="contact-blocks-grid">
        <x-content-blocks :blocks="$blocks" type="1" />
    </div>
</div>
@endsection

@section('scripts')
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.public_key') }}"></script>
<script>
$(document).ready(function () {
    $('#contact-form').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $btn  = $form.find('.contact-form__btn');
        $btn.prop('disabled', true).text('Sending…');

        grecaptcha.ready(function () {
            grecaptcha.execute('{{ config('services.recaptcha.public_key') }}', { action: 'contact' }).then(function (token) {
                $.ajax({
                    url:    '{{ route('contact-us') }}',
                    method: 'POST',
                    data: {
                        _token:    '{{ csrf_token() }}',
                        name:      $('#contact-name').val(),
                        email:     $('#contact-email').val(),
                        subject:   $('#contact-subject').val(),
                        text:      $('#contact-text').val(),
                        recaptcha: token,
                    },
                    success: function () {
                        Swal.fire({
                            icon:               'success',
                            title:              'Message sent!',
                            text:               'We\'ll get back to you within 2–3 business days.',
                            background:         '#0f0f10',
                            color:              '#ededed',
                            confirmButtonColor: '#ededed',
                            confirmButtonText:  'OK',
                        });
                        $form[0].reset();
                    },
                    error: function (xhr) {
                        var msg = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon:               'error',
                            title:              'Error',
                            text:               msg,
                            background:         '#0f0f10',
                            color:              '#ededed',
                            confirmButtonColor: '#ededed',
                        });
                    },
                    complete: function () {
                        $btn.prop('disabled', false).text('Send Message');
                    },
                });
            });
        });
    });
});
</script>
@endsection
