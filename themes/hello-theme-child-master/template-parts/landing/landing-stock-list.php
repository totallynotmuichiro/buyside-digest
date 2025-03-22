<?php
    $stock_list = wp_remote_get( 'https://sectobsddjango-production.up.railway.app/api/investors-crousal/ ');
    $stock_list = wp_remote_retrieve_body( $stock_list );
    $stock_list = json_decode( $stock_list, true );
    $stock_list = $stock_list['investors'];
    $stock_list = array_slice($stock_list, 0, 10);
?>

<div style="margin-top: 2rem;">
    <!-- Title -->
    <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 0.75rem;" class="border-b border-gray-500 pb-2">Stock Lists</h2>

    <!-- Flex container with inline styles for wrapping -->
    <div style="display: flex; flex-wrap: wrap; gap: 6px; width: 100%;">
        <?php foreach ($stock_list as $stock):?>
            <a href="<?php echo esc_url( $stock['url'] ); ?>" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
                onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
                onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
                <?php echo $stock['name'];?>
            </a>
        <?php endforeach;?>
    </div>
</div>