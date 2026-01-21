<?php
return [
    'default_paper_size' => 'A4',
    'default_orientation' => 'portrait',
    'default_font' => 'arial',
    'dpi' => 96,
    'enable_font_subsetting' => false,
    'font_height_ratio' => 1.1,
    'enable_html5_parser' => true,
    'enable_remote' => true, // IMPORTANT : Doit être TRUE pour le CSS
    'chroot' => realpath(base_path()),
];