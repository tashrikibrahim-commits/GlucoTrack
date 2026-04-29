<?php
function logoSVG($size = 32) {
    $svg = '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect width="48" height="48" rx="12" fill="currentColor" fill-opacity="0.1"/>';
    $svg .= '<path d="M24 8C24 8 20 14 20 20C20 22.2 21.8 24 24 24C26.2 24 28 22.2 28 20C28 14 24 8 24 8Z" fill="currentColor" opacity="0.8"/>';
    $svg .= '<path d="M24 24V40" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>';
    $svg .= '<path d="M18 30C18 30 21 28 24 28C27 28 30 30 30 30" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>';
    $svg .= '<path d="M16 35C16 35 20 33 24 33C28 33 32 35 32 35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>';
    $svg .= '<circle cx="24" cy="20" r="3" fill="currentColor" opacity="0.4"/>';
    $svg .= '</svg>';
    return $svg;
}
