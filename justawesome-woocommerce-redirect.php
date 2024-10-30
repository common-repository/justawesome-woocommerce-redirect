<?php
/*
Plugin Name: JustAwesome WooCommerce Redirect  
Plugin URI: https://www.justawesome.de
Description: JustAwesome WooCommerce Redirect Shop Page to Page/Product or URL
Author: Matthias Graffe 
Author URI: https://www.justawesome.de/ueber-uns/
Text Domain: justawesome-woocommerce-redirect
Version: 1.0.0
*/


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'justawesome_woocommerce_redirect_action_links' );
function justawesome_woocommerce_redirect_action_links ( $links ) 
{
	if (class_exists('WooCommerce'))
	{		
		$mylinks = array(
		'<a href="' . admin_url( 'admin.php?page=justawesome-woocommerce-redirect' ) . '">'.__('Settings').'</a>',
		);
	}
	return array_merge( $links, $mylinks );
}

add_action('admin_menu', 'justawesome_woocommerce_redirect_admin_menu');	
function justawesome_woocommerce_redirect_admin_menu() 
{ 
	if (class_exists('WooCommerce'))
	{	
		add_submenu_page( 'woocommerce','JustAwesome WooCommerce Redirect Shop Page Options', 'Redirect Shop Page', 'manage_options', 'justawesome-woocommerce-redirect', 'justawesome_woocommerce_redirect_options' );
	}
}
function justawesome_woocommerce_redirect_options()
{
	if ( !current_user_can( 'manage_options' ) )  
	{
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	if(isset($_POST['save']))
	{
		if(is_string($_POST['redirect']))
		{ 
			$redirect = sanitize_text_field( $_POST['redirect'] );
			if (get_option('justawesome_woocommerce_redirect_type') !== false) 
			{
				update_option( 'justawesome_woocommerce_redirect_type', $redirect );
				wp_cache_delete ( 'alloptions', 'options' );
			} else 
			{
				add_option( 'justawesome_woocommerce_redirect_type', $redirect);
			}		
		}
		if(is_string($_POST['page_id_url']))
		{
			$page_id_url = esc_url( $_POST['page_id_url'] );
			if (get_option('justawesome_woocommerce_redirect_url') !== false) 
			{
				update_option( 'justawesome_woocommerce_redirect_url', $page_id_url);
				wp_cache_delete ( 'alloptions', 'options' );
			} else 
			{
				add_option( 'justawesome_woocommerce_redirect_url', $page_id_url);
			}			
		}
		if(is_numeric($_POST['page_id']) && $_POST['page_id'] >= -1)
		{
			$page_id = intval($_POST['page_id'] );
			if (get_option('justawesome_woocommerce_redirect_pageid') !== false) 
			{
				update_option( 'justawesome_woocommerce_redirect_pageid', $page_id );
				wp_cache_delete ( 'alloptions', 'options' );
			} else 
			{
				add_option( 'justawesome_woocommerce_redirect_pageid', $page_id);
			}	
		}	
		if(is_numeric($_POST['product_id']) && $_POST['product_id'] >= -1)
		{		
			$product_id = intval( $_POST['product_id'] );
			if (get_option('justawesome_woocommerce_redirect_productid') !== false) 
			{
				update_option( 'justawesome_woocommerce_redirect_productid', $product_id );
				wp_cache_delete ( 'alloptions', 'options' );
			} else 
			{
				add_option( 'justawesome_woocommerce_redirect_productid', $product_id );
			}	
		}			
	}
	
	$currentRedirectType = get_option('justawesome_woocommerce_redirect_type','none');
	
	?><style> .justawesome_options { width:250px; } </style><?php
	print '<a href="https://www.justawesome.de" target="_blank" title="JustAwesome Webdesign"><img src="'.plugin_dir_url(__FILE__).'justawesome.png"></a><br><br>';
	print '<form method="POST" name="settings">';				
		print '<b>'.__('Redirect WooCommerce','justawesome-woocommerce-redirect').' <a href="'.get_page_link( get_option( 'woocommerce_shop_page_id' ) ).'" target="_blank">'.get_the_title( get_option( 'woocommerce_shop_page_id' ) ).'</a> '.__('Page','justawesome-woocommerce-redirect').':</b>';
		print '<br><select name="redirect" class="justawesome_options">';
			print '<option value="none"';
				if($currentRedirectType == 'none')
				{
					print ' selected';
				}
			print '>'.__('No Redirect','justawesome-woocommerce-redirect').'</option>';
			print '<option value="url"';
				if($currentRedirectType == 'url')
				{
					print ' selected';
				}
			print '>'.__('to URL','justawesome-woocommerce-redirect').'</option>';
			print '<option value="page"';
				if($currentRedirectType == 'page')
				{
					print ' selected';
				}
			print '>'.__('to Page','justawesome-woocommerce-redirect').'</option>';
			print '<option value="product"';
				if($currentRedirectType == 'product')
				{
					print ' selected';
				}
			print '>'.__('to Product','justawesome-woocommerce-redirect').'</option>';
		print '</select>';
		print '<br><br><b>'.__('URL','justawesome-woocommerce-redirect').':</b>';
		print '<br><input type="text" name="page_id_url" value="'.get_option('justawesome_woocommerce_redirect_url','').'" placeholder="'.__('URL','justawesome-woocommerce-redirect').'" class="justawesome_options">';
		print '<br><br><b>'.__('or Page','justawesome-woocommerce-redirect').':</b><br>';
		$args = array(
			'name'                  => 'page_id',
			'class'                 => 'justawesome_options',
			'selected'				=> get_option('justawesome_woocommerce_redirect_pageid',-1),
			'show_option_none'      => '-- '.__('None','justawesome-woocommerce-redirect').' --',
			'option_none_value'     => -1,	
			'post_type' 			=> 'page',
		);		
		wp_dropdown_pages($args);
		
		$currectProductId = get_option('justawesome_woocommerce_redirect_productid',-1);
		print '<br><br><b>'.__('or Product','justawesome-woocommerce-redirect').':</b><br>';
		print '<select name="product_id" class="justawesome_options">';
		print '<option value="-1"';
			if($currectProductId == '-1')
			{
				print ' selected';
			}
		print '>-- '.__('None','justawesome-woocommerce-redirect').' --</option>';
		$products = wc_get_products(array());	
		foreach($products as $product)
		{
			print '<option value="'.$product->id.'"';
				if($currectProductId == $product->id)
				{
					print ' selected';
				}
			print '>'.$product->name.'</option>';
		}		
		print '</select>';
		
		print '<br><br><button type="submit" name="save" class="button justawesome_options">'.__('Save','justawesome-woocommerce-redirect').'</button>';		
		
	print '</form>';
	
	if(isset($_POST['save']))
	{
		print '<br><b>'.__('Settings saved','justawesome-woocommerce-redirect').'</b>';
	}
	
	print '<br><br><br><b>Contact: <a href="https://www.justawesome.de/kontakt/" target="_blank" title="JustAwesome Webdesign">JustAwesome Webdesign</a></b><br>';
	
	global $wpdb;
	
}

function justawesome_shop_page_redirect() 
{
	$currentRedirectType = get_option('justawesome_woocommerce_redirect_type','none');
	
	if($currentRedirectType != 'none')
	{
		if (class_exists('WooCommerce')){	
			if( is_shop() ){				
				switch($currentRedirectType)				
				{
					case 'url':
						$targetUrl = get_option('justawesome_woocommerce_redirect_url','');
						if($targetUrl != '')
						{
							wp_redirect($targetUrl);
							exit();						
						}
						break;
					case 'page':
						$pageId = get_option('justawesome_woocommerce_redirect_pageid',-1);
						if($pageId > -1)
						{
							wp_redirect(get_permalink($pageId));
							exit();
						}
						break;
					case 'product':
						$productId = get_option('justawesome_woocommerce_redirect_productid',-1);
						if($productId > -1)
						{
							wp_redirect(get_permalink($productId));
							exit();
						}
						break;
				}
			}
		}
	}
}
add_action( 'template_redirect', 'justawesome_shop_page_redirect' );

?>