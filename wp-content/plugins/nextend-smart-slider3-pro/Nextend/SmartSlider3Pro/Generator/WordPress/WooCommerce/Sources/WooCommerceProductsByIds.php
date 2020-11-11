<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\WooCommerce\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Translation\Translation;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use WC_Product_Factory;

class WooCommerceProductsByIds extends AbstractGenerator {

    protected $layout = 'product';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'WooCommerce' . n2_('Products by IDs and/or SKUs'));
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');
        new Textarea($filter, 'ids', n2_('IDs (one per line)'), '', array(
            'width'  => 200,
            'height' => 200
        ));
        new Textarea($filter, 'skus', n2_('SKUs (one per line)'), '', array(
            'width'  => 200,
            'height' => 200
        ));
    }

    public function filterName($name) {
        return $name . Translation::getCurrentLocale() . get_woocommerce_currency();
    }

    public static function cacheKey($params) {
        return get_woocommerce_currency();
    }

    protected function _getData($count, $startIndex) {
        $productFactory = new WC_Product_Factory();
        $i              = 0;
        $data           = array();

        $ids  = $this->getIDs();
        $skus = $this->getSKUs();
        if (!empty($skus)) {
            foreach ($skus AS $sku) {
                $ids[] = wc_get_product_id_by_sku(trim($sku));
            }
        }

        foreach ($ids AS $id) {
            $product = $productFactory->get_product($id);
            if ($product && $product->is_visible()) {
                $product_id   = $product->get_id();
                $thumbnail_id = get_post_thumbnail_id($product_id);
                $image        = wp_get_attachment_url($thumbnail_id);
                $thumbnail    = wp_get_attachment_image_src($thumbnail_id);
                if ($thumbnail[0] != null) {
                    $thumbnail = $thumbnail[0];
                } else {
                    $thumbnail = $image;
                }

                $data[$i] = array(
                    'title'          => $product->get_title(),
                    'url'            => $product->get_permalink(),
                    'description'    => get_post($product_id)->post_content,
                    'image'          => ResourceTranslator::urlToResource($image),
                    'thumbnail'      => ResourceTranslator::urlToResource($thumbnail),
                    'price'          => wc_price($product->get_price()),
                    'regular_price'  => wc_price($product->get_regular_price()),
                    'price_with_tax' => wc_price(wc_get_price_including_tax($product)),
                    'rating'         => $product->get_average_rating()
                );

                $image_sizes = get_intermediate_image_sizes();
                foreach ($image_sizes AS $image_size) {
                    $image_data = wp_get_attachment_image_src($thumbnail_id, $image_size);
                    $data[$i]   += array(
                        'image_' . $image_size => ResourceTranslator::urlToResource($image_data[0])
                    );
                }

                $product_gallery = get_post_meta($product_id, "_product_image_gallery", true);
                if (!empty($product_gallery)) {
                    $product_gallery = explode(',', $product_gallery);
                    for ($fora = 0; $fora < count($product_gallery); $fora++) {
                        $data[$i]['product_gallery_' . $fora . '_image']     = wp_get_attachment_url($product_gallery[$fora]);
                        $data[$i]['product_gallery_' . $fora . '_thumbnail'] = wp_get_attachment_image_src($product_gallery[$fora])[0];
                        if (empty($data[$i]['product_gallery_' . $fora . '_thumbnail'])) {
                            $data[$i]['product_gallery_' . $fora . '_thumbnail'] = $data[$i]['product_gallery_' . $fora . '_image'];
                        }
                    }
                }

                $post   = get_post($id);
                $seller = get_user_by("id", $post->post_author);
                if (is_object($seller)) {
                    $data[$i]['seller_display_name']  = $seller->display_name;
                    $data[$i]['seller_user_nicename'] = $seller->user_nicename;
                }

                if ($product->is_on_sale()) {
                    $data[$i]['sale_price'] = wc_price($product->get_sale_price());
                } else {
                    $data[$i]['sale_price'] = $data[$i]['price'];
                }

                $data[$i]['ID'] = $product_id;

                $i++;
            }
        }

        return $data;
    }

    protected function getSKUs($field = 'skus') {

        $skus = $this->data->get($field);
        if (empty($skus)) {
            return false;
        }

        return array_map('strval', explode("\n", str_replace(array(
            "\r\n",
            "\n\r",
            "\r"
        ), "\n", $this->data->get($field))));
    }

}
