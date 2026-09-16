<?php

namespace App\Services\Saweria;

use App\Models\CatalogItem;

class ProductDetailMapper
{
    /**
     * Convert the private Saweria response into the small, stable contract used
     * by our product page. Buyer identifiers are deliberately not represented as
     * form inputs here: checkout and data entry stay on Saweria.
     */
    public function map(CatalogItem $item, array $group): array
    {
        $products = [];

        $remoteProducts = $group['products'] ?? [];

        if (! array_key_exists('products', $group) || ! is_array($remoteProducts)) {
            return $this->unavailable($item);
        }

        foreach ($remoteProducts as $product) {
            if (! is_array($product) || ($product['status'] ?? null) !== 'ACTIVE') {
                continue;
            }

            $currency = strtoupper((string) data_get($product, 'pricing.currency', ''));
            $price = data_get($product, 'pricing.selling_price');
            $id = $product['product_id'] ?? null;
            $name = $this->text($product['product_name'] ?? null);
            $slug = $this->text($product['slug'] ?? null);

            if (
                $currency !== 'IDR'
                || ! is_numeric($price)
                || ! is_finite((float) $price)
                || (float) $price <= 0
                || (! is_int($id) && ! is_string($id))
                || trim((string) $id) === ''
                || $name === ''
                || $slug === ''
            ) {
                continue;
            }

            $products[] = [
                'id' => $id,
                'name' => $name,
                'category' => $this->text(data_get($product, 'category.name')),
                'price' => (float) $price,
                'checkout_url' => $this->checkoutUrl($item->topup_url, $slug),
            ];
        }

        return [
            'item' => $item,
            'products' => $products,
            'description' => $this->text($group['description'] ?? null),
            'instructions' => $this->text(data_get($group, 'digital_form.description')),
            'fields' => $this->fields(data_get($group, 'digital_form.fields', [])),
            'faqs' => $this->faqs($group['faq'] ?? null),
            // A successful response may legitimately contain no active products.
            // The view presents that state differently from an upstream failure.
            'unavailable' => false,
        ];
    }

    public function unavailable(CatalogItem $item): array
    {
        return [
            'item' => $item,
            'products' => [],
            'description' => '',
            'instructions' => '',
            'fields' => [],
            'faqs' => [],
            'unavailable' => true,
        ];
    }

    protected function checkoutUrl(string $topupUrl, string $productSlug): string
    {
        $separator = str_contains($topupUrl, '?') ? '&' : '?';

        return $topupUrl.$separator.http_build_query(
            ['item' => $productSlug],
            '',
            '&',
            PHP_QUERY_RFC3986
        );
    }

    protected function fields(mixed $fields): array
    {
        if (! is_array($fields)) {
            return [];
        }

        $result = [];

        foreach ($fields as $field) {
            if (! is_array($field)) {
                continue;
            }

            $label = $this->text($field['label'] ?? null);

            if ($label === '') {
                continue;
            }

            $result[] = [
                'label' => $label,
                'placeholder' => $this->text($field['placeholder'] ?? null),
                'input_type' => $this->text($field['input_type'] ?? null),
                'required' => (bool) ($field['required'] ?? false),
            ];
        }

        return $result;
    }

    protected function faqs(mixed $faq): array
    {
        if (! is_array($faq)) {
            return [];
        }

        $entries = [];

        if (isset($faq['how_to_order'])) {
            $entries[] = $faq['how_to_order'];
        }

        if (isset($faq['others']) && is_array($faq['others'])) {
            $entries = array_merge($entries, $faq['others']);
        }

        $result = [];

        foreach ($entries as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $question = $this->text($entry['question'] ?? null);
            $answer = $this->text($entry['answer'] ?? null);

            if ($question !== '' && $answer !== '') {
                $result[] = compact('question', 'answer');
            }
        }

        return $result;
    }

    protected function text(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }
}
