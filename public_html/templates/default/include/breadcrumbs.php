<nav class="breadcrumbs">
    <ul class="breadcrumbs__list" itemscope="" itemtype="http://schema.org/BreadcrumbList">
        <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a class="breadcrumbs__link" itemprop="item" href="<?=PATH?>">
                <span itemprop="name">Главная</span>
            </a>
            <meta itemprop="position" content="1" />
        </li>
        <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a class="breadcrumbs__link" itemprop="item" href="<?=$this->alias('catalog')?>">
                <span itemprop="name">Каталог товаров</span>
            </a>
            <meta itemprop="position" content="2" />
        </li>
    </ul>
</nav>