<?php

function dki_hub_add_favicon() {
    echo '
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
        <link rel="manifest" href="/favicon/site.webmanifest">
        <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#ffffff">
        <link rel="shortcut icon" href="/favicon/favicon.ico">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-config" content="/favicon/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">
    ';
}
add_action( 'wp_head', 'dki_hub_add_favicon' );
add_action( 'admin_head', 'dki_hub_add_favicon' );