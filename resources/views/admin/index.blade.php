@extends('layouts.admin.app')

@section('title', 'Dashboard')

@section('content_header')
    <x-admin.title
        text="Dashboard"
        bcRoute="admin.index"
    />
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="small-box" style="background: linear-gradient(94.79deg, #1AA7F6 1.91%, #57C3FF 95.64%);">
                <div class="inner">
                    <h3>{{$data['users_total']}}</h3>
                    <p>User</p>
                </div>
                <div class="icon">
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 47.5C5 42 9.5 37.5 15 37.5H25C30.5 37.5 35 42 35 47.5" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M26.2499 14.9999C29.7499 18.4999 29.7499 23.9999 26.2499 27.2499C22.7499 30.4999 17.2499 30.7499 13.9999 27.2499C10.7499 23.7499 10.4999 18.4999 13.7499 14.9999C16.9999 11.4999 22.7499 11.7499 26.2499 14.9999" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M40 35H47.5C51.75 35 55 38.25 55 42.5" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M48.25 16.75C50.75 19.25 50.75 23.25 48.25 25.5C45.75 27.75 41.75 28 39.5 25.5C37.25 23 37 19 39.5 16.75C41.75 14.5 45.75 14.5 48.25 16.75" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <a href="{{route('admin.users.index')}}" class="small-box-footer">More Info
                    <span class="fas">
                        <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_965_31214)">
                                <path d="M0.967557 1.59001L3.87756 4.50001L0.967557 7.41001C0.898121 7.47944 0.843041 7.56188 0.805462 7.6526C0.767884 7.74332 0.748542 7.84056 0.748542 7.93876C0.748542 8.03696 0.767884 8.13419 0.805462 8.22492C0.843041 8.31564 0.898121 8.39807 0.967557 8.46751C1.03699 8.53694 1.11943 8.59202 1.21015 8.6296C1.30087 8.66718 1.39811 8.68652 1.49631 8.68652C1.59451 8.68652 1.69174 8.66718 1.78246 8.6296C1.87319 8.59202 1.95562 8.53694 2.02506 8.46751L5.46756 5.02501C5.53708 4.95562 5.59224 4.87321 5.62988 4.78248C5.66752 4.69175 5.68689 4.59448 5.68689 4.49626C5.68689 4.39803 5.66752 4.30077 5.62988 4.21004C5.59224 4.11931 5.53708 4.03689 5.46756 3.96751L2.02506 0.525007C1.73256 0.232507 1.26006 0.232507 0.967557 0.525008C0.682557 0.817508 0.675057 1.29751 0.967557 1.59001Z" fill="white" />
                            </g>
                            <defs>
                                <clipPath id="clip0_965_31214">
                                    <rect width="6" height="9" fill="white" transform="translate(6 9) rotate(180)" />
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="small-box" style="background: linear-gradient(94.79deg, #1DE183 1.91%, #5CECA7 95.64%);">
                <div class="inner">
                    <h3>{{$data['pages_total']}}</h3>
                    <p>Pages</p>
                </div>
                <div class="icon">
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M48.535 15.32L42.1775 8.9625C41.24 8.0275 39.97 7.5 38.6425 7.5H22.5C19.7375 7.5 17.5 9.7375 17.5 12.5V39.285C17.5 42.0475 19.7375 44.285 22.5 44.285H45C47.7625 44.285 50 42.0475 50 39.285V18.8575C50 17.53 49.4725 16.26 48.535 15.32V15.32Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M50 20H40C38.62 20 37.5 18.88 37.5 17.5V7.5" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M42.5 44.285V47.5C42.5 50.2625 40.2625 52.5 37.5 52.5H15C12.2375 52.5 10 50.2625 10 47.5V20C10 17.2375 12.2375 15 15 15H17.5" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <a href="{{route('admin.pages.index')}}" class="small-box-footer">More Info
                    <span class="fas">
                        <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_965_31214)">
                                <path d="M0.967557 1.59001L3.87756 4.50001L0.967557 7.41001C0.898121 7.47944 0.843041 7.56188 0.805462 7.6526C0.767884 7.74332 0.748542 7.84056 0.748542 7.93876C0.748542 8.03696 0.767884 8.13419 0.805462 8.22492C0.843041 8.31564 0.898121 8.39807 0.967557 8.46751C1.03699 8.53694 1.11943 8.59202 1.21015 8.6296C1.30087 8.66718 1.39811 8.68652 1.49631 8.68652C1.59451 8.68652 1.69174 8.66718 1.78246 8.6296C1.87319 8.59202 1.95562 8.53694 2.02506 8.46751L5.46756 5.02501C5.53708 4.95562 5.59224 4.87321 5.62988 4.78248C5.66752 4.69175 5.68689 4.59448 5.68689 4.49626C5.68689 4.39803 5.66752 4.30077 5.62988 4.21004C5.59224 4.11931 5.53708 4.03689 5.46756 3.96751L2.02506 0.525007C1.73256 0.232507 1.26006 0.232507 0.967557 0.525008C0.682557 0.817508 0.675057 1.29751 0.967557 1.59001Z" fill="white" />
                            </g>
                            <defs>
                                <clipPath id="clip0_965_31214">
                                    <rect width="6" height="9" fill="white" transform="translate(6 9) rotate(180)" />
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="small-box" style="background: linear-gradient(94.79deg, #FF9900 1.91%, #FFBB55 95.64%);">
                <div class="inner">
                    <h3>{{$data['posts_total']}}</h3>
                    <p>Posts</p>
                </div>
                <div class="icon">
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.4969 52.5095H12.4927C9.73011 52.5095 7.4906 50.27 7.4906 47.5074V12.4928C7.4906 9.73023 9.73011 7.49072 12.4927 7.49072H42.5052C45.2678 7.49072 47.5073 9.73023 47.5073 12.4928V19.9959" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17.4948 19.9959H32.501" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M17.4948 30H22.4969" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M30 52.5094L36.5652 51.699C37.0266 51.6408 37.4558 51.4315 37.7857 51.1038L53.6899 35.1997C54.5383 34.3548 55.0152 33.2069 55.0152 32.0096C55.0152 30.8123 54.5383 29.6643 53.6899 28.8195V28.8195C52.8451 27.9711 51.6971 27.4941 50.4998 27.4941C49.3025 27.4941 48.1545 27.9711 47.3097 28.8195L31.5531 44.5761C31.234 44.8954 31.0269 45.3096 30.9629 45.7566L30 52.5094Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <a href="{{route('admin.posts.index')}}" class="small-box-footer">More Info
                    <span class="fas">
                        <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_965_31214)">
                                <path d="M0.967557 1.59001L3.87756 4.50001L0.967557 7.41001C0.898121 7.47944 0.843041 7.56188 0.805462 7.6526C0.767884 7.74332 0.748542 7.84056 0.748542 7.93876C0.748542 8.03696 0.767884 8.13419 0.805462 8.22492C0.843041 8.31564 0.898121 8.39807 0.967557 8.46751C1.03699 8.53694 1.11943 8.59202 1.21015 8.6296C1.30087 8.66718 1.39811 8.68652 1.49631 8.68652C1.59451 8.68652 1.69174 8.66718 1.78246 8.6296C1.87319 8.59202 1.95562 8.53694 2.02506 8.46751L5.46756 5.02501C5.53708 4.95562 5.59224 4.87321 5.62988 4.78248C5.66752 4.69175 5.68689 4.59448 5.68689 4.49626C5.68689 4.39803 5.66752 4.30077 5.62988 4.21004C5.59224 4.11931 5.53708 4.03689 5.46756 3.96751L2.02506 0.525007C1.73256 0.232507 1.26006 0.232507 0.967557 0.525008C0.682557 0.817508 0.675057 1.29751 0.967557 1.59001Z" fill="white" />
                            </g>
                            <defs>
                                <clipPath id="clip0_965_31214">
                                    <rect width="6" height="9" fill="white" transform="translate(6 9) rotate(180)" />
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Posts</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table id="posts-table" class="table text-nowrap">
                        <thead>
                            <tr>
                                <th class="ids-column">Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Created at</th>
                                <th class="actions-column-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['posts'] as $post)
                                <tr>
                                    <td>{{$post->title}}</td>
                                    <td>{{$post->category->name}}</td>
                                    <td>{{$post->author?->name}}</td>
                                    <td>{{$post->status->readable()}}</td>
                                    <td>{{$post->created_at->format(env('ADMIN_DATETIME_FORMAT'))}}</td>
                                    <td>@include('components.admin.actions', ['name' => 'pages', 'model' => $post])</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table id="users-table" class="table text-nowrap">
                        <thead>
                            <tr>
                                <th class="ids-column">ID</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Created_at</th>
                                <th class="actions-column-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['users'] as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->created_at->format(env('ADMIN_DATETIME_FORMAT'))}}</td>
                                    <td>
                                        {{-- @include('admin.users.actions-list', ['model' => $user]) --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pages</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table id="pages-table" class="table text-nowrap">
                        <thead>
                            <tr>
                                <th class="ids-column">Title</th>
                                <th>URL</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="actions-column-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['pages'] as $page)
                                <tr>
                                    <td>{{$page->title}}</td>
                                    <td>{{$page->link}}</td>
                                    <td>{{$page->status}}</td>
                                    <td>{{$page->created_at->format(env('ADMIN_DATETIME_FORMAT'))}}</td>
                                    <td>@include('components.admin.actions', ['name' => 'pages', 'model' => $page])</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
