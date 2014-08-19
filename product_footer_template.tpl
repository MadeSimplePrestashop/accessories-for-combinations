{foreach from=$accessories item=accessory name=accessory}
    {if $accessory->available_for_order && !isset($restricted_country_mode)}
        {assign var='accessoryLink' value=$link->getProductLink($accessory->id_product, $accessory->link_rewrite, $accessory->category)|cat:$accessory->url_hash}
        <li class="afc-template afc-template-{$accessory->id_product_attribute} col-md-3">
            <div class="product_desc">
                <a href="{$accessoryLink|escape:'html':'UTF-8'}" title="{$accessory->legend|escape:'html':'UTF-8'}" class="product-image product_image">
                    {if $accessory->images[0]}
                        <img class="lazyOwl" src="{$link->getImageLink($accessory->link_rewrite,  $accessory->images[0]['id_image'], 'home_default')|escape:'html':'UTF-8'}" alt="{$accessory->legend|escape:'html':'UTF-8'}" width="{$homeSize.width}" height="{$homeSize.height}"/>
                    {/if}
                </a>
            </div>
            <div class="s_title_block">
                <h5 class="product-name">
                    <strong><a href="{$accessoryLink|escape:'html':'UTF-8'}">
                            {$accessory->name|escape:'html':'UTF-8'}
                        </a></strong>
                </h5>
                {if $accessory->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
                    <span class="price">
                        {if $priceDisplay != 1}
                            {l s="Cena"}: {displayWtPrice p=$accessory->price}{else}{displayWtPrice p=$accessory->price_tax_exc}
                            {/if}
                        </span>
                    {/if}
                    {if $accessory->perex}
                        <p>{$accessory->perex}</p>
                    {/if}
                </div>
            </li>
            {cycle name='afc' values=',,<div class="clearfix visible-xs"></div>'}
        {/if}
    {/foreach}