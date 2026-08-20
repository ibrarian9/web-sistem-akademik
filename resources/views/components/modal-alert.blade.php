@props([
    'rounded' => 'rounded-2xl',
    'badgeRounded' => 'rounded-xl',
    'categoryRounded' => 'rounded-lg',
])

<x-micromodal-notification :rounded="$rounded" :badgeRounded="$badgeRounded" :categoryRounded="$categoryRounded" />
