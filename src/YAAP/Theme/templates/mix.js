// mix for %theme_name% theme
mix.copyDirectory('themes/default/assets/img', 'public/themes/default/img');
mix.copyDirectory('themes/default/assets/fonts', 'public/themes/default/fonts');
// js
mix.js(['themes/default/assets/js/app.js'], 'public/themes/default/js/app.min.js')
//sass
mix.sass('themes/default/assets/sass/app.scss', 'public/themes/default/css/app.min.css')
