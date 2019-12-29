@extends('layouts.default')

@section('content')
<!-- Three -->
<section id="three" class="wrapper style2">
	<div class="inner">
		<div class="grid-style">

			<div>
				<div class="box">
					<a href="{{App\Libraries\Product\Lottery\Threed\Jisupailie3Lib::sGetIndexLink()}}">
						<div class="image fit">
							<img src="{{ asset('images/jisupailie3.jpg') }}" alt="" />
						</div>
						<div class="content">
							<header class="align-center">
								<h2>{{ trans('messages.product.lottery.3d.jisupailie3.name') }}</h2>
							</header>
						</div>
					</a>
				</div>
			</div>

			<div>
				<a href="{{App\Libraries\Product\Card\Poke\BlackjackLib::sGetIndexLink()}}">
					<div class="box">
						<div class="image fit">
							<img src="{{ asset('images/blackjack.jpg') }}" alt="" />
						</div>
						<div class="content">
							<header class="align-center">
								<h2>{{ trans('messages.product.card.poke.blackjack.name') }}</h2>
							</header>
						</div>
					</div>
				</a>
			</div>

		</div>
	</div>
</section>
@endsection