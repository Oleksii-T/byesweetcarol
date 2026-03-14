@extends('layouts.app')

@section('content')
    <main>
        <form>
            <label>
                Name
                <input type="text" name="name" placeholder="Your name">
            </label>
            <label>
                Email
                <input type="email" name="email" placeholder="name@example.com">
            </label>
            <label>
                Subject
                <input type="text" name="subject" placeholder="What is this about?">
            </label>
            <label class="full">
                Message
                <textarea name="message" rows="6" placeholder="Share the details."></textarea>
            </label>
            <button type="submit">Send</button>
        </form>
    </main>
@endsection


@section('scripts')
    <script src="https://www.google.com/recaptcha/api.js?render={{config('services.recaptcha.public_key')}}"></script>
@endsection
