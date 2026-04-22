<?php
/*
Plugin Name:    VHAP
Plugin URI:     https://vahro.ru/blog/plugins/vhap-administration-plugin-template
Description:    VHAP - Administration plugin page template
Author:         vahro
Author URI:     https://vahro.ru/
Version:        1.0.1

Ver 1.0.1
Добавлен вывод икноки плагина в Top Level Menu
*/

defined( 'ABSPATH' ) OR exit;
// !defined('ABSPATH') && exit; // Game over
// If this file is called directly, abort.
// if ( !class_exists('WP') ) die();
// if ( !defined('WPINC') ) die;


// активация плагина
register_activation_hook( __FILE__, 
  function () {
    // что-то делаем при активации плагина в админке
    if( !get_option('vhap_toplevelmenu') ) {
      // если опции vhap_toplevelmenu не существует, она добавляется в БД (wp_options) со значением 'top' 
      add_option('vhap_toplevelmenu', 'top');
    }
    if( !get_option('vhap_deactivation') ) {
      // если опции vhap_deactivation не существует, она добавляется в БД (wp_options) со значением 'yes' 
      add_option('vhap_deactivation', 'yes');
    }
});

// деактивация плагина
register_deactivation_hook(  __FILE__, 
  function () {
  // что-то делаем при деактивации плагина в админке
});

// удаление плагина
// !!! анонимная функция в этом хуке не работает
register_uninstall_hook( __FILE__, 'vhap_uninstall');
function vhap_uninstall () {
  delete_option( 'vhap_toplevelmenu' );
  delete_option( 'vhap_deactivation' );
}

// add_action( 'delete_option_vhap_toplevelmenu', function () { error_log( 'delete_option_vhap_toplevelmenu' ); } );
// add_action( 'delete_option_vhap_deactivation', function () { error_log( 'delete_option_vhap_deactivation' ); } );


// =================================================================================================== Загрузка CSS и JS
add_action( 'admin_enqueue_scripts', function( $hook_suffix ) {
  // error_log('hook_suffix => ' . $hook_suffix); // определяется $hook_suffix
  if ( $hook_suffix == 'toplevel_page_vhap' || $hook_suffix == 'settings_page_vhap' ) {
    // CSS файл загружается только если это страница настройки плагина VHAP в админке
    wp_enqueue_style( 'vhap', plugin_dir_url(__FILE__) . 'vhap.css');
  }
});

// =================================================================================================== register_setting

add_action( 'admin_init', function() { 

// $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21">
// <rect fill="none" stroke="#000" class="d" x=".5" y=".5" width="20" height="20"/>
// </svg>';
// $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21" fill="#000" stroke="none">
// <rect stroke-linejoin="round" fill="none" stroke="#000" class="d" x=".5" y=".5" width="20" height="20"/>
// </svg>';
// $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21" fill="#000" stroke="none"><path d="M10.84,7.05l-3.31,8.58h-3.12L1.12,7.05h2.91l1.99,5.91,1.97-5.91h2.85Z"/>
// <path   d="M19.41,15.63h-2.76v-4.26c0-.35-.02-.69-.05-1.04-.04-.34-.1-.6-.18-.76-.1-.19-.25-.33-.45-.41-.2-.09-.47-.13-.82-.13-.25,0-.5,.04-.76,.12-.26,.08-.54,.21-.84,.39v6.08h-2.76V3.74h2.76V8c.49-.38,.96-.68,1.41-.88,.45-.2,.95-.31,1.5-.31,.93,0,1.65,.27,2.17,.81,.52,.54,.78,1.35,.78,2.42v5.59Z"/>
// </svg>';
// // $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21" fill="#000" stroke="none"><path d="M10.84,7.05l-3.31,8.58h-3.12L1.12,7.05h2.91l1.99,5.91,1.97-5.91h2.85Z"/>
// // <path   d="M19.41,15.63h-2.76v-4.26c0-.35-.02-.69-.05-1.04-.04-.34-.1-.6-.18-.76-.1-.19-.25-.33-.45-.41-.2-.09-.47-.13-.82-.13-.25,0-.5,.04-.76,.12-.26,.08-.54,.21-.84,.39v6.08h-2.76V3.74h2.76V8c.49-.38,.96-.68,1.41-.88,.45-.2,.95-.31,1.5-.31,.93,0,1.65,.27,2.17,.81,.52,.54,.78,1.35,.78,2.42v5.59Z"/>
// // <rect stroke-linejoin="round" fill="none" stroke="#000" class="d" x=".5" y=".5" width="20" height="20"/></svg>';
// error_log( base64_encode( $svg ) );

  register_setting( 'vhap_setting', 'vhap_toplevelmenu', ['sanitize_callback' => 'vhap_toplevelmenu_validate',] );
  register_setting( 'vhap_setting', 'vhap_deactivation', ['sanitize_callback' => 'vhap_deactivation_validate',] );

  add_settings_section(
    'vhap_section_id', // ID секции, при добавлении поля (add_settings_field) ссылаемся на ID секции в которую добавляем
    'Настройка плагина', // заголовок секции (не обязательно)
    '', // функция для вывода HTML секции (необязательно)
    'vhap_page', // ярлык страницы
    array(
      'before_section' => '<fieldset class=%s>',
      'after_section'  => '</fieldset>',
      'section_class'  => 'vhap_section',
    )
  );

// ============================================== vhap_deactivation
  add_settings_field(
      'vhap_deactivation', 
      'VHAP Deactivation', 
      'vhap_deactivation_html', // callback
      'vhap_page',
      'vhap_section_id', // Id секции где размещается поле
      ['name' => 'vhap_deactivation',] 
      );

  function vhap_deactivation_html ( $args ) {
    // Деактивация плагина - 'yes'/'no'
      $field = $args[ 'name' ];
      $option = get_option( $args[ 'name' ] ); // 'vhap_deactivation'
      $html = '<input type="checkbox" name="' . $field . '" value="yes" ' . checked( 'yes', $option, false ) . '/>';
      $html = $html . ' убрать/восстановить пункт "Деактивировать" в списке плагинов в <a href="plugins.php">меню "Плагины"</a>';
      echo $html;
  }
  function vhap_deactivation_validate ( $input ) {
    // register_setting( 'vhap_setting', 'vhap_deactivation', ['sanitize_callback' => 'vhap_deactivation_validate',] )
    return( $input != 'yes' ? 'no' : $input );
  }

// ============================================== vhap_toplevelmenu
  add_settings_field(
      'vhap_toplevelmenu', 
      'Top Level Menu', 
      'vhap_toplevelmenu_html', // callback
      'vhap_page',
      'vhap_section_id', // Id секции где размещается поле
      ['name' => 'vhap_toplevelmenu',] 
      );

  function vhap_toplevelmenu_html ( $args ) {
    // Размещение меню 'top'/'sub'
    // 'top' - Top Level Menu
    // 'sub' - submenu
      $field = $args[ 'name' ];
      $option = get_option( $args[ 'name' ] ); // 'vhap_toplevelmenu'
      $html = '<input type="checkbox" name="' . $field . '" value="top" ' . checked( 'top', $option, false ) . '/>';
      $html = $html . ' пункт меню VHAP либо в основном меню слева (Top Level Menu) либо в дополнительном (Submenu) пункта "Настройки"';
      echo $html;
  }
  function vhap_toplevelmenu_validate ( $input ) {
    // register_setting( 'vhap_setting', 'vhap_toplevelmenu', ['sanitize_callback' => 'vhap_toplevelmenu_validate',] );
    return( $input != 'top' ? 'sub' : $input );
  }

} );

// ================================================================================================= admin_notices
add_action( 'admin_notices', function () {
// https://developer.wordpress.org/reference/hooks/admin_notices/
// После нажатия конпки "Submit" - функция submit_button()
// выводится сообщение на страницу настроек - 'Done!'
// * notice-error – will display the message with a white background and a red left border.
// * notice-warning– will display the message with a white background and a yellow/orange left border.
// * notice-success – will display the message with a white background and a green left border.
// * notice-info – will display the message with a white background a blue left border.
  if (isset( $_GET[ 'settings-updated' ] ) && true == $_GET[ 'settings-updated' ]	) {
  ?>
  <div class="notice notice-success is-dismissible">
    <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
  </div>
  <?php }
} );

// ================================================================================================= vhap_options_html
// Вывод страницы настроек https://vahro.ru/wp-admin/admin.php?page=vhap
function vhap_options_html() {
  ?>
  <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); // будет выведен заголовок страницы который определен в add_submenu_page ?></h1>
      <form method="post" action="options.php">
        <?php
          settings_errors( 'vhap_settings_errors' ); // Вывод ошибки на экран здесь, если ошибки есть
          settings_fields( 'vhap_setting' ); // название настроек - https://developer.wordpress.org/reference/functions/settings_fields/
          do_settings_sections( 'vhap_page' ); // ярлык страницы, не более
          submit_button(); // функция для вывода кнопки submit - https://developer.wordpress.org/reference/functions/submit_button/
       ?>
      </form></div>
  <?php
}

// 
// =================================================================================================== Menu
// 
/**
 * Add the top level menu page.
 */
add_action( 'admin_menu', function() { 

// Регистрируется пункт VHAP в главном меню и несколько пунктов подменю
  global $submenu;
  $option = get_option( 'vhap_toplevelmenu' );
  if ( $option == 'sub' ) {
    // 'vhap_toplevelmenu' может быть или 'top' или 'sub'
    // Submenu - пункт "Настройки" - slug - options-general.php
    // add_submenu_page ( 'options-general.php', ...)
    add_options_page( 'VHAP', 'VHAP', 'manage_options', 'vhap', 'vhap_options_html' );
    return;
  }

  // Top Level Menu; 'vhap_toplevelmenu' => 'top'
//   $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
// <path fill="#a7aaad" d="M19.03,0H.97C.44,0,0,.44,0,.97V18.85c0,.54,.44,.97,.97,.97H19.03c.54,0,.97-.44,.97-.97V.97c0-.54-.44-.97-.97-.97Zm-.71,15.43h-2.63v-4.02c0-.33-.02-.65-.05-.98-.03-.32-.09-.56-.18-.72-.1-.18-.24-.31-.43-.39-.19-.08-.45-.12-.78-.12-.24,0-.48,.04-.72,.12-.25,.08-.51,.2-.8,.37v5.74h-2.63V7.86l-2.95,7.57h-2.97L1.06,7.33H3.82l1.9,5.58,1.87-5.58h2.51v-3.12h2.63v4.02c.47-.36,.91-.64,1.34-.83s.9-.29,1.43-.29c.88,0,1.57,.26,2.07,.76,.5,.51,.75,1.27,.75,2.29v5.27Z"/>
// </svg>';
// error_log( $svg );
//   $icon = "data:image/svg+xml;base64,". base64_encode( $svg );
// error_log( $icon );
$icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+DQo8cGF0aCBmaWxsPSIjYTdhYWFkIiBkPSJNMTkuMDMsMEguOTdDLjQ0LDAsMCwuNDQsMCwuOTdWMTguODVjMCwuNTQsLjQ0LC45NywuOTcsLjk3SDE5LjAzYy41NCwwLC45Ny0uNDQsLjk3LS45N1YuOTdjMC0uNTQtLjQ0LS45Ny0uOTctLjk3Wm0tLjcxLDE1LjQzaC0yLjYzdi00LjAyYzAtLjMzLS4wMi0uNjUtLjA1LS45OC0uMDMtLjMyLS4wOS0uNTYtLjE4LS43Mi0uMS0uMTgtLjI0LS4zMS0uNDMtLjM5LS4xOS0uMDgtLjQ1LS4xMi0uNzgtLjEyLS4yNCwwLS40OCwuMDQtLjcyLC4xMi0uMjUsLjA4LS41MSwuMi0uOCwuMzd2NS43NGgtMi42M1Y3Ljg2bC0yLjk1LDcuNTdoLTIuOTdMMS4wNiw3LjMzSDMuODJsMS45LDUuNTgsMS44Ny01LjU4aDIuNTF2LTMuMTJoMi42M3Y0LjAyYy40Ny0uMzYsLjkxLS42NCwxLjM0LS44M3MuOS0uMjksMS40My0uMjljLjg4LDAsMS41NywuMjYsMi4wNywuNzYsLjUsLjUxLC43NSwxLjI3LC43NSwyLjI5djUuMjdaIi8+DQo8L3N2Zz4=";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+DQo8cGF0aCBmaWxsPSIjY2NjIiBkPSJNMTkuMDMsMEguOTdDLjQ0LDAsMCwuNDQsMCwuOTdWMTguODVjMCwuNTQsLjQ0LC45NywuOTcsLjk3SDE5LjAzYy41NCwwLC45Ny0uNDQsLjk3LS45N1YuOTdjMC0uNTQtLjQ0LS45Ny0uOTctLjk3Wm0tLjcxLDE1LjQzaC0yLjYzdi00LjAyYzAtLjMzLS4wMi0uNjUtLjA1LS45OC0uMDMtLjMyLS4wOS0uNTYtLjE4LS43Mi0uMS0uMTgtLjI0LS4zMS0uNDMtLjM5LS4xOS0uMDgtLjQ1LS4xMi0uNzgtLjEyLS4yNCwwLS40OCwuMDQtLjcyLC4xMi0uMjUsLjA4LS41MSwuMi0uOCwuMzd2NS43NGgtMi42M1Y3Ljg2bC0yLjk1LDcuNTdoLTIuOTdMMS4wNiw3LjMzSDMuODJsMS45LDUuNTgsMS44Ny01LjU4aDIuNTF2LTMuMTJoMi42M3Y0LjAyYy40Ny0uMzYsLjkxLS42NCwxLjM0LS44M3MuOS0uMjksMS40My0uMjljLjg4LDAsMS41NywuMjYsMi4wNywuNzYsLjUsLjUxLC43NSwxLjI3LC43NSwyLjI5djUuMjdaIi8+DQo8L3N2Zz4=";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+DQo8cGF0aCBmaWxsPSJyZWQiIGQ9Ik0xOS4wMywwSC45N0MuNDQsMCwwLC40NCwwLC45N1YxOC44NWMwLC41NCwuNDQsLjk3LC45NywuOTdIMTkuMDNjLjU0LDAsLjk3LS40NCwuOTctLjk3Vi45N2MwLS41NC0uNDQtLjk3LS45Ny0uOTdabS0uNzEsMTUuNDNoLTIuNjN2LTQuMDJjMC0uMzMtLjAyLS42NS0uMDUtLjk4LS4wMy0uMzItLjA5LS41Ni0uMTgtLjcyLS4xLS4xOC0uMjQtLjMxLS40My0uMzktLjE5LS4wOC0uNDUtLjEyLS43OC0uMTItLjI0LDAtLjQ4LC4wNC0uNzIsLjEyLS4yNSwuMDgtLjUxLC4yLS44LC4zN3Y1Ljc0aC0yLjYzVjcuODZsLTIuOTUsNy41N2gtMi45N0wxLjA2LDcuMzNIMy44MmwxLjksNS41OCwxLjg3LTUuNThoMi41MXYtMy4xMmgyLjYzdjQuMDJjLjQ3LS4zNiwuOTEtLjY0LDEuMzQtLjgzcy45LS4yOSwxLjQzLS4yOWMuODgsMCwxLjU3LC4yNiwyLjA3LC43NiwuNSwuNTEsLjc1LDEuMjcsLjc1LDIuMjl2NS4yN1oiLz4NCjwvc3ZnPg==";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMS42NCAyMS40NiI+DQo8cmVjdCBmaWxsPSJub25lIiBzdHJva2U9ImJsYWNrIiBzdHJva2Utd2lkdGg9IjJweCIgeD0iMSIgeT0iMSIgd2lkdGg9IjE5LjY0IiBoZWlnaHQ9IjE5LjQ2IiByeD0iOS43MyIgcnk9IjkuNzMiLz4NCjxwYXRoIGQ9Ik0xMC42OCw4LjJsLTMuMSw3Ljk2aC0yLjkyTDEuNTksOC4yaDIuNzJsMS44Niw1LjQ4LDEuODQtNS40OGgyLjY3WiIvPg0KPHBhdGggZD0iTTE4LjU0LDE2LjE1aC0yLjU4di0zLjk1YzAtLjMyLS4wMi0uNjQtLjA1LS45Ni0uMDMtLjMyLS4wOS0uNTUtLjE3LS43MS0uMDktLjE3LS4yMy0uMy0uNDItLjM4LS4xOC0uMDgtLjQ0LS4xMi0uNzctLjEyLS4yMywwLS40NywuMDQtLjcxLC4xMS0uMjQsLjA4LS41LC4yLS43OCwuMzZ2NS42NGgtMi41OFY1LjEzaDIuNTh2My45NWMuNDYtLjM1LC45LS42MywxLjMyLS44MXMuODktLjI4LDEuNC0uMjhjLjg3LDAsMS41NCwuMjUsMi4wMywuNzUsLjQ5LC41LC43MywxLjI1LC43MywyLjI1djUuMThaIi8+DQo8L3N2Zz4=";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMS42NCAyMS40NiI+PGRlZnM+PHN0eWxlPi5ke2ZpbGw6bm9uZTtzdHJva2U6IzAwMDtzdHJva2Utd2lkdGg6MnB4O308L3N0eWxlPjwvZGVmcz48cmVjdCBjbGFzcz0iZCIgeD0iMSIgeT0iMSIgd2lkdGg9IjE5LjY0IiBoZWlnaHQ9IjE5LjQ2IiByeD0iOS43MyIgcnk9IjkuNzMiLz48cGF0aCBkPSJNMTAuNjgsOC4ybC0zLjEsNy45NmgtMi45MkwxLjU5LDguMmgyLjcybDEuODYsNS40OCwxLjg0LTUuNDhoMi42N1oiLz48cGF0aCBkPSJNMTguNTQsMTYuMTVoLTIuNTh2LTMuOTVjMC0uMzItLjAyLS42NC0uMDUtLjk2LS4wMy0uMzItLjA5LS41NS0uMTctLjcxLS4wOS0uMTctLjIzLS4zLS40Mi0uMzgtLjE4LS4wOC0uNDQtLjEyLS43Ny0uMTItLjIzLDAtLjQ3LC4wNC0uNzEsLjExLS4yNCwuMDgtLjUsLjItLjc4LC4zNnY1LjY0aC0yLjU4VjUuMTNoMi41OHYzLjk1Yy40Ni0uMzUsLjktLjYzLDEuMzItLjgxcy44OS0uMjgsMS40LS4yOGMuODcsMCwxLjU0LC4yNSwyLjAzLC43NSwuNDksLjUsLjczLDEuMjUsLjczLDIuMjV2NS4xOFoiLz48L3N2Zz4=";
  // $icon = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDIwLjY0IDIwLjQ2Ij48ZGVmcz48c3R5bGU+LmR7ZmlsbDpub25lO3N0cm9rZTojMDAwO308L3N0eWxlPjwvZGVmcz48ZyBpZD0iYSIvPjxnIGlkPSJiIj48ZyBpZD0iYyI+PHJlY3QgY2xhc3M9ImQiIHg9Ii41IiB5PSIuNSIgd2lkdGg9IjE5LjY0IiBoZWlnaHQ9IjE5LjQ2IiByeD0iOS43MyIgcnk9IjkuNzMiLz48cGF0aCBkPSJNMTAuMTgsNy43bC0zLjEsNy45NmgtMi45MkwxLjA5LDcuN0gzLjgxbDEuODYsNS40OCwxLjg0LTUuNDhoMi42N1oiLz48cGF0aCBkPSJNMTguMDQsMTUuNjVoLTIuNTh2LTMuOTVjMC0uMzItLjAyLS42NC0uMDUtLjk2LS4wMy0uMzItLjA5LS41NS0uMTctLjcxLS4wOS0uMTctLjIzLS4zLS40Mi0uMzgtLjE4LS4wOC0uNDQtLjEyLS43Ny0uMTItLjIzLDAtLjQ3LC4wNC0uNzEsLjExLS4yNCwuMDgtLjUsLjItLjc4LC4zNnY1LjY0aC0yLjU4VjQuNjNoMi41OHYzLjk1Yy40Ni0uMzUsLjktLjYzLDEuMzItLjgxcy44OS0uMjgsMS40LS4yOGMuODcsMCwxLjU0LC4yNSwyLjAzLC43NSwuNDksLjUsLjczLDEuMjUsLjczLDIuMjV2NS4xOFoiLz48L2c+PC9nPjwvc3ZnPg==";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMSAyMSIgZmlsbD0iIzAwMCIgc3Ryb2tlPSJub25lIj48cGF0aCBkPSJNMTAuODQsNy4wNWwtMy4zMSw4LjU4aC0zLjEyTDEuMTIsNy4wNWgyLjkxbDEuOTksNS45MSwxLjk3LTUuOTFoMi44NVoiLz4NCjxwYXRoICAgZD0iTTE5LjQxLDE1LjYzaC0yLjc2di00LjI2YzAtLjM1LS4wMi0uNjktLjA1LTEuMDQtLjA0LS4zNC0uMS0uNi0uMTgtLjc2LS4xLS4xOS0uMjUtLjMzLS40NS0uNDEtLjItLjA5LS40Ny0uMTMtLjgyLS4xMy0uMjUsMC0uNSwuMDQtLjc2LC4xMi0uMjYsLjA4LS41NCwuMjEtLjg0LC4zOXY2LjA4aC0yLjc2VjMuNzRoMi43NlY4Yy40OS0uMzgsLjk2LS42OCwxLjQxLS44OCwuNDUtLjIsLjk1LS4zMSwxLjUtLjMxLC45MywwLDEuNjUsLjI3LDIuMTcsLjgxLC41MiwuNTQsLjc4LDEuMzUsLjc4LDIuNDJ2NS41OVoiLz4NCjxyZWN0IHN0cm9rZS1saW5lam9pbj0icm91bmQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwMCIgY2xhc3M9ImQiIHg9Ii41IiB5PSIuNSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIi8+PC9zdmc+";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMSAyMSIgZmlsbD0iIzAwMCIgc3Ryb2tlPSJub25lIj48cGF0aCBkPSJNMTAuODQsNy4wNWwtMy4zMSw4LjU4aC0zLjEyTDEuMTIsNy4wNWgyLjkxbDEuOTksNS45MSwxLjk3LTUuOTFoMi44NVoiLz4NCjxwYXRoICAgZD0iTTE5LjQxLDE1LjYzaC0yLjc2di00LjI2YzAtLjM1LS4wMi0uNjktLjA1LTEuMDQtLjA0LS4zNC0uMS0uNi0uMTgtLjc2LS4xLS4xOS0uMjUtLjMzLS40NS0uNDEtLjItLjA5LS40Ny0uMTMtLjgyLS4xMy0uMjUsMC0uNSwuMDQtLjc2LC4xMi0uMjYsLjA4LS41NCwuMjEtLjg0LC4zOXY2LjA4aC0yLjc2VjMuNzRoMi43NlY4Yy40OS0uMzgsLjk2LS42OCwxLjQxLS44OCwuNDUtLjIsLjk1LS4zMSwxLjUtLjMxLC45MywwLDEuNjUsLjI3LDIuMTcsLjgxLC41MiwuNTQsLjc4LDEuMzUsLjc4LDIuNDJ2NS41OVoiLz4NCjxyZWN0IHN0cm9rZS1saW5lam9pbj0icm91bmQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwMCIgY2xhc3M9ImQiIHg9Ii41IiB5PSIuNSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIi8+PC9zdmc+";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMSAyMSI+DQo8cmVjdCBmaWxsPSJub25lIiBzdHJva2U9IiMwMDAiIGNsYXNzPSJkIiB4PSIuNSIgeT0iLjUiIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIvPg0KPC9zdmc+";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMSAyMSIgZmlsbD0iIzAwMCIgc3Ryb2tlPSJub25lIj4NCjxyZWN0IHN0cm9rZS1saW5lam9pbj0icm91bmQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwMCIgY2xhc3M9ImQiIHg9Ii41IiB5PSIuNSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIi8+DQo8L3N2Zz4=";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMSAyMSIgZmlsbD0iIzAwMCIgc3Ryb2tlPSJub25lIj48cGF0aCBkPSJNMTAuODQsNy4wNWwtMy4zMSw4LjU4aC0zLjEyTDEuMTIsNy4wNWgyLjkxbDEuOTksNS45MSwxLjk3LTUuOTFoMi44NVoiLz4NCjxwYXRoICAgZD0iTTE5LjQxLDE1LjYzaC0yLjc2di00LjI2YzAtLjM1LS4wMi0uNjktLjA1LTEuMDQtLjA0LS4zNC0uMS0uNi0uMTgtLjc2LS4xLS4xOS0uMjUtLjMzLS40NS0uNDEtLjItLjA5LS40Ny0uMTMtLjgyLS4xMy0uMjUsMC0uNSwuMDQtLjc2LC4xMi0uMjYsLjA4LS41NCwuMjEtLjg0LC4zOXY2LjA4aC0yLjc2VjMuNzRoMi43NlY4Yy40OS0uMzgsLjk2LS42OCwxLjQxLS44OCwuNDUtLjIsLjk1LS4zMSwxLjUtLjMxLC45MywwLDEuNjUsLjI3LDIuMTcsLjgxLC41MiwuNTQsLjc4LDEuMzUsLjc4LDIuNDJ2NS41OVoiLz4NCjwvc3ZnPg==";
  // $icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMSAyMSIgZmlsbD0iIzAwMCIgc3Ryb2tlPSJub25lIj48cGF0aCBkPSJNMTAuODQsNy4wNWwtMy4zMSw4LjU4aC0zLjEyTDEuMTIsNy4wNWgyLjkxbDEuOTksNS45MSwxLjk3LTUuOTFoMi44NVoiLz4NCjxwYXRoICAgZD0iTTE5LjQxLDE1LjYzaC0yLjc2di00LjI2YzAtLjM1LS4wMi0uNjktLjA1LTEuMDQtLjA0LS4zNC0uMS0uNi0uMTgtLjc2LS4xLS4xOS0uMjUtLjMzLS40NS0uNDEtLjItLjA5LS40Ny0uMTMtLjgyLS4xMy0uMjUsMC0uNSwuMDQtLjc2LC4xMi0uMjYsLjA4LS41NCwuMjEtLjg0LC4zOXY2LjA4aC0yLjc2VjMuNzRoMi43NlY4Yy40OS0uMzgsLjk2LS42OCwxLjQxLS44OCwuNDUtLjIsLjk1LS4zMSwxLjUtLjMxLC45MywwLDEuNjUsLjI3LDIuMTcsLjgxLC41MiwuNTQsLjc4LDEuMzUsLjc4LDIuNDJ2NS41OVoiLz4NCjxyZWN0IHN0cm9rZS1saW5lam9pbj0icm91bmQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwMCIgY2xhc3M9ImQiIHg9Ii41IiB5PSIuNSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIi8+PC9zdmc+";
  add_menu_page (
    'Плагин VHAP', // заголовок страницы - будем выводить его в <h1></h1>
    'VHAP', // текст ссылки в меню - будем выводить в основное меню админки WP слева
    'manage_options', // права пользователя, необходимые для доступа к странице - manage_options - administrator
    'vhap', // page id - https://site.ru/wp-admin/admin.php?page=vhap
    'vhap_options_html', // callback - функция, которая выводит содержимое страницы
    // 'dashicons-superhero', // по умолчанию 'dashicons-admin-generic', посмотреть здесь https://developer.wordpress.org/resource/dashicons/
    $icon, // по умолчанию 'dashicons-admin-generic', посмотреть здесь https://developer.wordpress.org/resource/dashicons/
    );
  // 100 // позиция в меню
  // "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMSAyMSIgZmlsbD0iIzAwMCIgc3Ryb2tlPSJub25lIj48cGF0aCBkPSJNMTAuODQsNy4wNWwtMy4zMSw4LjU4aC0zLjEyTDEuMTIsNy4wNWgyLjkxbDEuOTksNS45MSwxLjk3LTUuOTFoMi44NVoiLz4NCjxwYXRoICAgZD0iTTE5LjQxLDE1LjYzaC0yLjc2di00LjI2YzAtLjM1LS4wMi0uNjktLjA1LTEuMDQtLjA0LS4zNC0uMS0uNi0uMTgtLjc2LS4xLS4xOS0uMjUtLjMzLS40NS0uNDEtLjItLjA5LS40Ny0uMTMtLjgyLS4xMy0uMjUsMC0uNSwuMDQtLjc2LC4xMi0uMjYsLjA4LS41NCwuMjEtLjg0LC4zOXY2LjA4aC0yLjc2VjMuNzRoMi43NlY4Yy40OS0uMzgsLjk2LS42OCwxLjQxLS44OCwuNDUtLjIsLjk1LS4zMSwxLjUtLjMxLC45MywwLDEuNjUsLjI3LDIuMTcsLjgxLC41MiwuNTQsLjc4LDEuMzUsLjc4LDIuNDJ2NS41OVoiLz4NCjxyZWN0IHN0cm9rZS1saW5lam9pbj0icm91bmQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwMCIgY2xhc3M9ImQiIHg9Ii41IiB5PSIuNSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIi8+PC9zdmc+"
  add_submenu_page ( 
    // повторяется пункт из основного меню - одинаковый callback - vhap_options_html
    'vhap',
    'Плагин VHAP',
    'Настройка',
    'manage_options',
    'vhap',
    'vhap_options_html'
    );

  add_submenu_page ( 
    // дополнительный пункт меню - описание (документация) плагина - callback - vhap_doc_html
    'vhap',
    'Описание VHAP',
    'Описание',
    'manage_options',
    'vhap_doc',
    'vhap_doc_html'
    );

  // пункт submenu - ссылка на любой URL
  $permalink = 'https://vahro.ru';
  $submenu['vhap'][] = array( 'vahro', 'manage_options', $permalink );
});

function vhap_doc_html() {
  // vhapdoc.php - документация к плагину, обычный html файл и чуть-чуть php
  include __DIR__ . DIRECTORY_SEPARATOR .'vhapdoc.php';
}



// ================================ ССЫЛКИ в общей таблице плагинов
// Добавим ссылку на страницу настроек в таблицу плагинов на странице плагинов https://site.ru/wp-admin/plugins.php

// Хук plugin_action_links срабатывает при выводе списка плагинов и позволяет добавлять собственные ссылки
$plugin_file = plugin_basename( __FILE__ ); // вернет vhap/vhap.php
add_filter("plugin_action_links_$plugin_file", function ( $links ) {  

    if ( get_option( 'vhap_deactivation' ) != 'yes' ) 
      unset( $links['deactivate'] );  // Remove the deactivate link

    if ( get_option( 'vhap_toplevelmenu' ) != 'sub' ) 
      array_unshift( $links, '<a href="admin.php?page=vhap_doc" rel="noopener">Документация</a>' ); 

    array_unshift( $links, '<a href="admin.php?page=vhap">Настройки</a>' ); 

    return $links;
});


