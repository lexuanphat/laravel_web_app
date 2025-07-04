<?php
namespace App\Services\Shipping;

class Base {
    public function extractAddressParts(string $address) {
        $parts = array_map('trim', explode(',', $address));
        $parts = array_reverse($parts);

        $province = $parts[0] ?? null;
        $district = $parts[1] ?? null;
        $ward = $parts[2] ?? null;

        $detailParts = array_slice($parts, 3);
        $detail = count($detailParts) ? implode(', ', array_reverse($detailParts)) : null;

        return [
            'province' => $province,
            'district' => $district,
            'ward' => $ward,
            'detail' => $detail,
        ];

    }
}