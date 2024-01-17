<?php if (!empty($sales)):?>
<section class="slider">
    <div class="slider__container swiper-container">

        <div class="slider__wrapper swiper-wrapper">
            <?php foreach ($sales as $item):?>
                <a href="<?=$this->alias($item['external_alias'])?>" class="slider__item swiper-slide" style="text-decoration: none">
                    <div class="slider__item-description">
                        <div class="slider__item-prev-text"><?=$item['sub_title']?></div>
                        <div class="slider__item-header">
                            <?php foreach (preg_split('/\s+/', $item['name'], 0, PREG_SPLIT_NO_EMPTY) as $value):?>
                                <span><?=$value?></span>
                            <?php endforeach;?>
                        </div>
                        <div class="slider__item-text">
                            <?=$this->clearStr($item['short_content'])?>
                        </div>
                        <div class="slider__item-logos">
                            <?php if (!empty($this->set['img_years']) && !empty($this->set['number_of_years'])):?>
                                <div class="slider__item-15yrs">
                                    <img src="<?=$this->img($this->set['img_years'])?>" alt="">
                                    <p><span><?=$this->wordsForCounter($this->set['number_of_years'])?></span>на рынке</p>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="slider__item-image">
                        <img src="<?=$this->img($item['img'])?>" alt="">
                    </div>
                </a>
            <?php endforeach;?>
        </div>

        <div class="slider__pagination swiper-pagination"></div>
        <div class="slider__controls controls _prev swiper-button-prev">
            <svg>
                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow"></use>
            </svg>
        </div>
        <div class="slider__controls controls _next swiper-button-next">
            <svg>
                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow"></use>
            </svg>
        </div>
</section>
<?php endif;?>

<?php if (!empty($this->menu['catalog'])):?>
<section class="catalog">
    <div class="division-internal__items">
        <?php foreach ($this->menu['catalog'] as $item):?>
            <a href="<?=$this->alias(['catalog' => $item['alias']])?>" class="division-internal-item">
                  <span class="division-internal-item__title">
                    <?=$item['name']?>
                  </span>
                <span class="division-internal-item__arrow-stat">
                <svg>
                  <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow-right"></use>
                </svg>
              </span>
                            <span class="division-internal-item__arrow">
                <img src="<?=PATH . TEMPLATE?>assets/img/divisions/devision-arrow.png" alt="">
              </span>
            </a>
        <?php endforeach;?>
    </div>
</section>
<?php endif;?>
<section class="offers">
    <div class="offers__tabs">
        <ul class="offers__tabs_header">
            <li class="active">
                <div class="icon-offer"><svg>
                        <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hit"></use>
                    </svg></div>Хиты продаж
            </li>
            <li>
                <div class="icon-offer">
                    <svg>
                        <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hot"></use>
                    </svg>
                </div>
                Горячие предложения
            </li>
            <li>
                <div class="icon-offer">%</div>Акции
            </li>
            <li>
                <div class="icon-offer"><span>new</span></div>Новинки
            </li>
        </ul>
        <div class="offers__tabs_content active">
            <div class="offers__tabs_subheader subheader">
                Хиты продаж
            </div>
            <div class="offers__tabs_container swiper-container">
                <div class="offers__tabs_wrapper swiper-wrapper">

                    <div class="offers__tabs_card swiper-slide">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <svg>
                                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hit"></use>
                            </svg>
                        </div>
                    </div>

                    <div class="offers__tabs_card swiper-slide">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <svg>
                                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hit"></use>
                            </svg>
                        </div>
                    </div>

                    <div class="offers__tabs_card swiper-slide">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <svg>
                                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hit"></use>
                            </svg>
                        </div>
                    </div>

                    <div class="offers__tabs_card swiper-slide">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <svg>
                                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hit"></use>
                            </svg>
                        </div>
                    </div>

                    <div class="offers__tabs_card swiper-slide">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <svg>
                                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hit"></use>
                            </svg>
                        </div>
                    </div>

                </div>
            </div>
            <a href="index.html" class="offers__readmore readmore">Смотреть каталог</a>
        </div>
        <div class="offers__tabs_content">
            <div class="offers__tabs_subheader subheader">
                Горячие предложения
            </div>
            <div class="offers__tabs_container swiper-container">
                <div class="offers__tabs_wrapper swiper-wrapper">
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <svg>
                                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hot"></use>
                            </svg>
                        </div>
                    </div>
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <svg>
                                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hot"></use>
                            </svg>
                        </div>
                    </div>
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <svg>
                                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hot"></use>
                            </svg>
                        </div>
                    </div>
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">

                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <svg>
                                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#hot"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <a href="index.html" class="offers__readmore readmore">Смотреть каталог</a>
        </div>
        <div class="offers__tabs_content">
            <div class="offers__tabs_subheader subheader">
                Акции
            </div>
            <div class="offers__tabs_container swiper-container">
                <div class="offers__tabs_wrapper swiper-wrapper">
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">

                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            %
                        </div>
                    </div>
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            %
                        </div>
                    </div>
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            %
                        </div>
                    </div>
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">

                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            %
                        </div>
                    </div>
                </div>
            </div>
            <a href="index.html" class="offers__readmore readmore">Смотреть каталог</a>
        </div>
        <div class="offers__tabs_content">
            <div class="offers__tabs_subheader subheader">
                Новинки
            </div>
            <div class="offers__tabs_container swiper-container">
                <div class="offers__tabs_wrapper swiper-wrapper">
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <span>New</span>
                        </div>
                    </div>
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">

                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <span>New</span>
                        </div>
                    </div>
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">

                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <span>New</span>
                        </div>
                    </div>
                    <div class="offers__tabs_card">
                        <div class="offers__tabs_image">

                            <img src="<?=PATH . TEMPLATE?>assets/img/offers.png" alt="">
                        </div>
                        <div class="offers__tabs_description">
                            <div class="offers__tabs_name">
                                <span>Смазка силиконовая SILICOT,</span>
                                универсальная с фторопластом 10г стик-пакет
                                <div class="card-main-info__table">
                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Бренд
                                        </div>
                                        <div class="card-main-info__table-item">
                                            ВМПАВТО
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Вес
                                        </div>
                                        <div class="card-main-info__table-item">
                                            0.1 кг
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Термостойкость
                                        </div>
                                        <div class="card-main-info__table-item">
                                            от -50°C до +230°C
                                        </div>
                                    </div>

                                    <div class="card-main-info__table-row">
                                        <div class="card-main-info__table-item">
                                            Номинальный объем
                                        </div>
                                        <div class="card-main-info__table-item">
                                            10 гр
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offers__tabs_price">
                                Цена: <span class="offers_old-price">84 руб.</span> <span class="offers_new-price">59 руб.</span>
                            </div>
                        </div>
                        <button class="offers__btn">купить сейчас</button>
                        <div class="icon-offer">
                            <span>New</span>
                        </div>
                    </div>
                </div>
            </div>
            <a href="index.html" class="offers__readmore readmore">Смотреть каталог</a>
        </div>
        <div class="offers__controls controls _prev">
            <svg>
                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow"></use>
            </svg>
        </div>
        <div class="offers__controls controls _next">
            <svg>
                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow"></use>
            </svg>
        </div>
    </div>
</section>

<div class="horizontal">
    <div class="horizontal__wrapper">
        <section class="about">
            <div class="about__description">
                <div class="about__description_name subheader">Интернет-магазин АвтоЗапчасти</div>
                <div class="about__description_text">
                    <p>начал свою работу в 1999 году. С самого начала главной целью было предложить нашим клиентам самый широкий спектр автомобильных запасных частей и аксессуаров, а развитие интернет–технологий дало возможность максимально упростить и ускорить процесс покупки.</p>
                    <p>Компания быстро росла, и сегодня, занимая одну из ведущих позиции на этом рынке, мы не стоим на месте. В основе проекта АвтоЗапчасти : самые современные информационные технологии, собственные программные разработки, накопленная за годы работы аналитическая и статистическая информация по рынку, высококвалифицированный коллектив — мы делаем все для того, чтобы Вы были довольны нашей работой.</p>
                </div>
                <a href="index.html" class="about__description_readmore readmore">Читать подробнее</a>
            </div>
            <div class="about__image">
                <img src="<?=PATH . TEMPLATE?>assets/img/about.png" alt="">
            </div>
        </section>

        <section class="advantages">
            <div class="advantages__name subheader">Наши преимущества</div>
            <div class="advantages__wrapper">
                <div class="advantages__row advantages__row_left">
                    <div class="advantages__item">
                        <div class="advantages__item_header">Опыт работы свыше 14 лет</div>
                        <img src="<?=PATH . TEMPLATE?>assets/img/advantages/adv1.png" class="advantages__item_image" alt="">
                    </div>
                    <div class="advantages__item">
                        <div class="advantages__item_header">Комплексный подход</div>
                        <img src="<?=PATH . TEMPLATE?>assets/img/advantages/adv2.png" class="advantages__item_image" alt="">
                    </div>
                    <div class="advantages__item">
                        <div class="advantages__item_header">Квалифицированные сотрудники</div>
                        <img src="<?=PATH . TEMPLATE?>assets/img/advantages/adv3.png" class="advantages__item_image" alt="">
                    </div>
                </div>
                <div class="advantages__row advantages__row_right">
                    <div class="advantages__item">
                        <div class="advantages__item_header">Долгосрочное сотрудничество</div>
                        <div class="advantages__item_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/advantages/adv4.png" alt="" class="advantages__item_image">
                        </div>
                    </div>
                    <div class="advantages__item">
                        <div class="advantages__item_header">Работаем со всеми современными системами</div>
                        <div class="advantages__item_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/advantages/adv5.png" alt="" class="advantages__item_image">
                        </div>
                    </div>
                    <div class="advantages__item">
                        <div class="advantages__item_header">Гарантия качества</div>
                        <div class="advantages__item_image">
                            <img src="<?=PATH . TEMPLATE?>assets/img/advantages/adv6.png" alt="" class="advantages__item_image">
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

<section class="feedback ">
    <div class="feedback__name subheader ">Остались вопросы</div>
    <form action="index.html" class="feedback__form">
        <div class="feedback__form_left">
            <input type="text" class="input-text feedback__input" placeholder="Ваше имя">
            <input type="email" class="input-text feedback__input" placeholder="E-mail">
            <input type="text" class="input-text feedback__input js-mask-phone" placeholder="Телефон">
        </div>
        <div class="feedback__form_right">
            <textarea class="input-textarea feedback__textarea" placeholder="Ваш вопрос"></textarea>
        </div>
        <div class="feedback__privacy">
            <label class="checkbox">
                <input type="checkbox" />
                <div class="checkbox__text">Соглашаюсь с правилами обработки персональных данных</div>
            </label>
        </div>
        <button type="submit" class="form-submit feedback__submit">Отправить</button>
    </form>
</section>

<section class="news">
    <div class="news__name subheader">Новости</div>
    <div class="news__wrapper">

        <div class="news__item">
            <div class="news__item_date">
                <span class="bigtext">24</span>
                <span>июня<br>
      2019</span>
            </div>
            <div class="news__item_main">
                <div class="news__item_header">В каталог аксессуаров добавлены модели: Chery Exeed TXL</div>
                <div class="news__item_text">Рады сообщить, что в нашем интернет-магазине для заказа стали доступны аксессуары для Chery Exeed TXL</div>
                <div class="news__item_readmore readmore-underline"><a href="index.html">Читать подробрнее</a></div>
            </div>
        </div>

        <div class="news__item">
            <div class="news__item_date">
                <span class="bigtext">24</span>
                <span>июня<br>
      2019</span>
            </div>
            <div class="news__item_main">
                <div class="news__item_header">В каталог аксессуаров добавлены модели: Chery Exeed TXL</div>
                <div class="news__item_text">Рады сообщить, что в нашем интернет-магазине для заказа стали доступны аксессуары для Chery Exeed TXL</div>
                <div class="news__item_readmore readmore-underline"><a href="index.html">Читать подробрнее</a></div>
            </div>
        </div>

        <div class="news__item">
            <div class="news__item_date">
                <span class="bigtext">24</span>
                <span>июня<br>
      2019</span>
            </div>
            <div class="news__item_main">
                <div class="news__item_header">В каталог аксессуаров добавлены модели: Chery Exeed TXL</div>
                <div class="news__item_text">Рады сообщить, что в нашем интернет-магазине для заказа стали доступны аксессуары для Chery Exeed TXL</div>
                <div class="news__item_readmore readmore-underline"><a href="index.html">Читать подробрнее</a></div>
            </div>
        </div>

    </div>
    <a href="index.html" class="news__reasdmore readmore">Смотреть все</a>
</section>

<div class="search ">
    <button>
        <svg class="inline-svg-icon svg-search">
            <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#search"></use>
        </svg>
    </button>
    <input type="search" placeholder="Поиск по каталогу">
</div>