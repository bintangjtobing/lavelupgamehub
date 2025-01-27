<section class="topup-section pt-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="section-header text-center">
                    <span class="section-sub-titel"><i class="las la-gamepad"></i> Recharge</span>
                    <h2 class="section-title"> <span class="text--base">TOP UP GAMES</span></h2>
                </div>
            </div>
        </div>

        <!-- Top Up Games Area -->
        <div class="topup-area">
            <div class="row justify-content-center mb-30-none">
                @foreach($games as $game)
                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-6 mb-30">
                    <a href="{{ url($game->game_url) }}" target="_blank">
                        <div class="topup-item">
                            <div class="topup-thumb">
                                <img src="{{ url($game->image_url) }}" data-src="{{ url($game->data_src) }}"
                                    alt="{{ $game->name }} - LevelUp Gaming Market" class="lazy">
                            </div>
                            <div class="topup-content">
                                <h5 class="title">{{ $game->name }}</h5>
                                <p>{{ \Carbon\Carbon::parse($game->created_date)->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination for Games -->
        <div class="d-flex justify-content-center">
            {{ $games->links('pagination::bootstrap-4') }}
        </div>

        <!-- Vouchers Area -->
        <div class="topup-area pt-120">
            <div class="section-header text-center">
                <h2 class="section-title"> <span class="text--base">VOUCHER GAMES</span></h2>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach($vouchers as $voucher)
                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-4 col-xs-6 mb-30">
                    <a href="{{ url($voucher->voucher_url) }}" target="_blank">
                        <div class="topup-item">
                            <div class="topup-thumb">
                                <img src="{{ url($voucher->image_url) }}" data-src="{{ url($voucher->data_src) }}"
                                    alt="{{ $voucher->name }} - LevelUp Gaming Market" class="lazy">
                            </div>
                            <div class="topup-content">
                                <h5 class="title">{{ $voucher->name }}</h5>
                                <p>{{ $voucher->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination for Vouchers -->
        <div class="d-flex justify-content-center">
            {{ $vouchers->links('pagination::bootstrap-4') }}
        </div>
    </div>
</section>
