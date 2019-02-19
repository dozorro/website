<div class="block_statistica_porushen">
    <div class="container">
        <div class="list_porushen inline-layout">
            <a href="{{ route('page.complaints.type', ['type' => 'below']) }}" class="item inline-layout{{strpos($_SERVER['REQUEST_URI'], 'complaints/below')!==false ? ' selected':'' }}">
                <h3>ПОРУШЕННЯ НА
                    <span>ДОПОРОГОВИХ</span>
                    ЗАКУПІВЛЯХ
                </h3>
                <div class="img-holder">
                    <img src="/assets/images/porushenna2.png" alt="ПОРУШЕННЯ НА ДОПОРОГОВИХ ЗАКУПІВЛЯХ">
                </div>
            </a>
            <a href="{{ route('page.complaints.type', ['type' => 'above']) }}" class="item inline-layout{{strpos($_SERVER['REQUEST_URI'], 'complaints/above')!==false ? ' selected':'' }}">
                <h3>ПОРУШЕННЯ НА
                    <span>НАДПОРОГОВИХ</span>
                    ЗАКУПІВЛЯХ
                </h3>
                <div class="img-holder">
                    <img src="/assets/images/porushenna1.png" alt="ПОРУШЕННЯ НА НАДПОРОГОВИХ ЗАКУПІВЛЯХ">
                </div>
            </a>
        </div>
    </div>
</div>