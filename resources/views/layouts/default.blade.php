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
            <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

    </head>
    <body class="subpage">
        <!-- Header -->
            <header id="header">
                <div class="logo"><a href="index.html">{{ trans('messages.main.title') }}</a></div>
                @if(Auth::guest())
                    <a class="nav-link" href="{{ url('/login') }}">{{ trans('messages.main.login') }}</a>
                    <a class="nav-link" href="{{ url('/register') }}">{{ trans('messages.main.register') }}</a>
                @else
                    <span>{{ Auth::user()->name }}</span>
                    <a href="#menu" class="toggle"></a>
            </header>

        <!-- Nav -->
            <nav id="menu">
                <ul class="links">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="generic.html">Generic</a></li>
                    <li><a href="elements.html">Elements</a></li>
                    <li>
                        <a class="dropdown-item" href="{{ url('/logout') }}">
                            Logout
                        </a>
                    </li>
                    <li><a href="{{App\Libraries\Manage\ChildrenLib::sGetIndexLink(Auth::user()->id)}}">下級設定</a></li>
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
                    <header class="align-center">
                        <h2>Morbi interdum mollis sapien</h2>
                        <p>Cras aliquet urna ut sapien tincidunt, quis malesuada elit facilisis. Vestibulum sit amet tortor velit. Nam elementum nibh a libero pharetra elementum. Maecenas feugiat ex purus, quis volutpat lacus placerat malesuada. Praesent in sem ex. Morbi mattis sapien pretium tellus venenatis, at egestas urna ornare.</p>
                    </header>
                    <div class="copyright">
                        &copy; Untitled Design: <a href="https://templated.co/">TEMPLATED</a>. Images <a href="https://unsplash.com/">Unsplash</a>. Video <a href="http://coverr.co/">Coverr</a>.
                    </div>
                </div>
            </footer>
    @yield('scripts')
    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <!-- {{-- <script src="{{ elixir('js/appq.js') }}"></script> --}} -->
    <!-- <script src="/js/app.js"></script> -->
    
    </body>
</html>