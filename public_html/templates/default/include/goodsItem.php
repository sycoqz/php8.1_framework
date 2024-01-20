<?php if (!empty($data)):?>
    <div class="offers__tabs_card swiper-slide">
    <div class="offers__tabs_image">
        <img src="<?=$this->img($data['img'] ?? '')?>" alt="<?=$data['name']?>">
    </div>
    <div class="offers__tabs_description">
        <div class="offers__tabs_name">
            <span><?=$data['name']?></span>
            <?=$data['short_content']?>
            <?php if (!empty($data['filters'])):?>
                <div class="card-main-info__table">
                <?php foreach ($data['filters'] as $item):?>
                    <div class="card-main-info__table-row">
                        <div class="card-main-info__table-item">
                            <?=$item['name']?>
                        </div>
                        <div class="card-main-info__table-item">
                            <?=implode(', ', array_column($item['values'], 'name'))?>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
            <?php endif;?>
        </div>
        <div class="offers__tabs_price">
            Цена: <?=!empty($data['old_price']) ? '<span class="offers_old-price">' . $data['old_price'] . 'руб.</span>' : ''?>
            <span class="offers_new-price"><?=$data['price']?> руб.</span>
        </div>
    </div>
    <button class="offers__btn" data-addToCart="<?=$data['id']?>">купить</button>
    <?php if (!empty($parameters['icon'])):?>
        <div class="icon-offer">
            <?=$parameters['icon']?>
        </div>
    <?php endif;?>
</div>
<?php endif;?>