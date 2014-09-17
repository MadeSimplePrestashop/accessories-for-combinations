{foreach from=$accessories item=accessory name=accessories_list}
    {if $accessory->available_for_order && !isset($restricted_country_mode)}
        {assign var='accessoryLink' value=$link->getProductLink($accessory->id_product, $accessory->link_rewrite, $accessory->category)|cat:$accessory->url_hash}
        <li class="afc-template afc-template-{$accessory->id_product_attribute} col-xs-6 col-sm-4 col-md-3 item product-box ajax_block_product{if $smarty.foreach.accessories_list.first} first_item{elseif $smarty.foreach.accessories_list.last} last_item{else} item{/if} product_accessories_description">
            <div class="product_desc">
                <a href="{$accessoryLink|escape:'html':'UTF-8'}" title="{$accessory->legend|default|escape:'html':'UTF-8'}" class="product-image product_image">
                    <img class="lazyOwl" src="{$link->getImageLink($accessory->link_rewrite, $accessory->images[0]['id_image'], 'home_default')|escape:'html':'UTF-8'}" alt="{$accessory->legend|default|escape:'html':'UTF-8'}" width="{$homeSize.width|default}" height="{$homeSize.height|default}"/>
                </a>
                <div class="block_description">
                    <a href="{$accessoryLink|escape:'html':'UTF-8'}" title="{l s='More' mod='accessoriesforcombinations'}" class="product_description">
                        {$accessory->description_short|strip_tags|truncate:25:'...'}
                    </a>
                </div>
            </div>
            <div class="s_title_block">
                <h5 class="product-name">
                    <a href="{$accessoryLink|escape:'html':'UTF-8'}">
                        {$accessory->name|escape:'html':'UTF-8'}
                        <br />
                        {$accessory->attributes_group_names|escape:'html':'UTF-8'}
                    </a>
                </h5>
                {if $accessory->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
                    <span class="price">
                        {if $priceDisplay != 1}
                        {displayWtPrice p=$accessory->price}{else}{displayWtPrice p=$accessory->price_tax_exc}
                        {/if}
                    </span>
                {/if}
            </div>
        </li>
        {cycle name='afc' values=',,<div class="clearfix visible-xs"></div>'}
    {/if}
{/foreach}