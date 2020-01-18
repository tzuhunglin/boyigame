<!DOCTYPE HTML>

<html>
    <head>
        <title>{{ trans('messages.main.title') }}</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="{{ asset('css/main.css') }}" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Scripts -->
            <script src="{{ asset('js/jquery.min.js') }}" defer></script>
            <script src="{{ asset('js/jquery.scrolly.min.js') }}" defer></script>
            <script src="{{ asset('js/jquery.scrollex.min.js') }}" defer></script>
            <script src="{{ asset('js/skel.min.js') }}" defer></script>
            <script src="{{ asset('js/util.js') }}" defer></script>
            <script src="{{ asset('js/main.js') }}" defer></script>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    </head>
    <body class="subpage">
        <!-- Header -->
            <header id="header">
                <div class="logo"><a href="/">{{ trans('messages.main.title') }}</a></div>
                @if(Auth::guest())
                    <a class="nav-link" href="{{ url('/login') }}">{{ trans('messages.main.login') }}</a>
                    <a class="nav-link" href="{{ url('/register') }}">{{ trans('messages.main.register') }}</a>
                @else
                    <span>{{ Auth::user()->name }} 餘額:{{ Auth::user()->totalmoney }}</span>
                    <a href="#menu" class="toggle"></a>
            </header>

        <!-- Nav -->
            <nav id="menu">
                <ul class="links">
                    <li><a href="/">Home</a></li>
                    <li><a href="{{App\Libraries\Manage\ChildrenLib::sGetIndexLink(Auth::user()->id)}}">下級設定</a></li>
                    <li><a href="{{App\Libraries\Manage\ChildrenLib::sGetBetRecordLink(Auth::user()->id)}}">投注紀錄</a></li>
                    <li><a href="{{App\Libraries\Manage\ChildrenLib::sGetGameRecordLink(Auth::user()->id)}}">遊戲紀錄</a></li>
                    <li><a href="{{App\Libraries\Manage\ChildrenLib::sGetReturnRecordLink(Auth::user()->id)}}">返點紀錄</a></li>
                    <li>
                        <a class="dropdown-item" href="{{ url('/logout') }}">
                            Logout
                        </a>
                    </li>
                </ul>
            </nav>
                @endif

        <!-- Banner -->
            <!-- <section id="banner" style="height:50px;"></section> -->

        <!-- Content -->
            @yield('content')
        <!-- Footer -->
            <footer id="footer" class="wrapper">
                <div class="inner">

                </div>
            </footer>
    @yield('scripts')
    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </body>
</html>